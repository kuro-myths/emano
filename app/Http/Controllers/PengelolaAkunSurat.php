<?php

namespace App\Http\Controllers;

use App\Http\Requests\SimpanAkunSurat;
use App\Models\AkunSuratElektronik;
use App\Models\CatatanAudit;
use App\Models\PenyediaSurat;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PengelolaAkunSurat extends Controller
{
    private const JUMLAH_PER_HALAMAN = 25;

    public function index(Request $permintaan): View
    {
        $akun = AkunSuratElektronik::query()
            ->with('penyedia')
            ->milikPengguna($permintaan->user()->id)
            ->cari($permintaan->query('kata_pencarian'))
            ->when($permintaan->filled('penyedia'), fn ($k) => $k->where('penyedia_surat_id', (int) $permintaan->query('penyedia')))
            ->when($permintaan->filled('status'), fn ($k) => $k->where('status_koneksi', (string) $permintaan->query('status')))
            ->orderBy('alamat_surat_elektronik')
            ->paginate(self::JUMLAH_PER_HALAMAN)
            ->withQueryString();

        return view('akun-surat.index', [
            'akun' => $akun,
            'daftarPenyedia' => PenyediaSurat::query()->orderBy('nama')->get(),
            'daftarStatus' => AkunSuratElektronik::DAFTAR_STATUS,
        ]);
    }

    public function tambah(): View
    {
        return view('akun-surat.tambah', [
            'daftarPenyedia' => PenyediaSurat::query()->where('aktif', true)->orderBy('nama')->get(),
        ]);
    }

    public function simpan(SimpanAkunSurat $permintaan): RedirectResponse
    {
        $data = $permintaan->validated();

        $akun = AkunSuratElektronik::create([
            'pengguna_id' => $permintaan->user()->id,
            'penyedia_surat_id' => $data['penyedia_surat_id'],
            'alamat_surat_elektronik' => $data['alamat_surat_elektronik'],
            'nama_tampilan' => $data['nama_tampilan'] ?? null,
            'jenis_autentikasi' => AkunSuratElektronik::STATUS_BELUM_TERHUBUNG,
            'status_koneksi' => AkunSuratElektronik::STATUS_BELUM_TERHUBUNG,
        ]);

        CatatanAudit::catat($permintaan->user()->id, 'tambah_akun_surat', 'akun_surat', $akun->id);

        return redirect()->route('akun-surat.index')->with('berhasil', 'Akun surat elektronik berhasil ditambahkan.');
    }

    public function ubah(AkunSuratElektronik $akun): View
    {
        Gate::authorize('ubah', $akun);

        return view('akun-surat.ubah', [
            'akun' => $akun,
            'daftarPenyedia' => PenyediaSurat::query()->where('aktif', true)->orderBy('nama')->get(),
        ]);
    }

    public function perbarui(SimpanAkunSurat $permintaan, AkunSuratElektronik $akun): RedirectResponse
    {
        Gate::authorize('ubah', $akun);

        $akun->update($permintaan->validated());
        CatatanAudit::catat($permintaan->user()->id, 'ubah_akun_surat', 'akun_surat', $akun->id);

        return redirect()->route('akun-surat.index')->with('berhasil', 'Akun surat elektronik berhasil diperbarui.');
    }

    public function hapus(Request $permintaan, AkunSuratElektronik $akun): RedirectResponse
    {
        Gate::authorize('hapus', $akun);

        CatatanAudit::catat($permintaan->user()->id, 'hapus_akun_surat', 'akun_surat', $akun->id);
        $akun->delete();

        return redirect()->route('akun-surat.index')->with('berhasil', 'Akun surat elektronik berhasil dihapus.');
    }

    public function ekspor(Request $permintaan): StreamedResponse
    {
        $penggunaId = $permintaan->user()->id;
        CatatanAudit::catat($penggunaId, 'ekspor_akun_surat', 'akun_surat');

        return response()->streamDownload(function () use ($penggunaId) {
            $keluaran = fopen('php://output', 'w');
            fputcsv($keluaran, ['alamat_surat_elektronik', 'nama_tampilan', 'penyedia', 'status_koneksi', 'waktu_dibuat_utc'], ',', '"', '');

            AkunSuratElektronik::query()
                ->with('penyedia')
                ->milikPengguna($penggunaId)
                ->lazyById(1000)
                ->each(function (AkunSuratElektronik $akun) use ($keluaran) {
                    fputcsv($keluaran, [
                        self::netralkanRumus($akun->alamat_surat_elektronik),
                        self::netralkanRumus((string) $akun->nama_tampilan),
                        $akun->penyedia?->kode,
                        $akun->status_koneksi,
                        $akun->waktu_dibuat?->utc()->toIso8601String(),
                    ], ',', '"', '');
                });

            fclose($keluaran);
        }, 'akun-surat-' . now()->format('Ymd-His') . '.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    /** Mencegah injeksi rumus saat CSV dibuka di spreadsheet. */
    private static function netralkanRumus(string $nilai): string
    {
        return $nilai !== '' && in_array($nilai[0], ['=', '+', '-', '@', "\t", "\r"], true) ? "'" . $nilai : $nilai;
    }
}
