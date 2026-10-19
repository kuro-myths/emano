<?php

namespace App\Services;

use App\Models\AkunSuratElektronik;
use App\Models\CatatanAudit;
use App\Models\PenyediaSurat;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

/**
 * Mengimpor daftar ALAMAT akun dari CSV.
 *
 * Penting: impor ini hanya menyimpan alamat. Kolom kata sandi (jika ada di berkas) SENGAJA
 * diabaikan dan tidak pernah dibaca ke basis data. Akun baru berstatus "belum terhubung"
 * sampai pengguna menghubungkannya lewat OAuth2/IMAP yang sah.
 */
class LayananImporAkun
{
    private const UKURAN_BAGIAN = 500;
    private const MAKS_CONTOH_KESALAHAN = 50;
    private const KOLOM_DIKENAL = ['alamat_surat_elektronik', 'nama_tampilan', 'penyedia'];

    /**
     * @return array{total_baris:int, berhasil_diimpor:int, duplikat_dalam_berkas:int, duplikat_di_basis_data:int, tidak_valid:int, kolom_diabaikan:array<int,string>, contoh_kesalahan:array<int,array{baris:int,nilai:string,alasan:string}>}
     */
    public function impor(int $penggunaId, string $jalurBerkas): array
    {
        $gagang = @fopen($jalurBerkas, 'r');
        if ($gagang === false) {
            throw new InvalidArgumentException('Berkas tidak dapat dibuka.');
        }

        try {
            return $this->proses($penggunaId, $gagang);
        } finally {
            fclose($gagang);
        }
    }

    /** Menebak kode penyedia dari domain alamat. */
    public static function tebakKodePenyedia(string $alamat): string
    {
        $domain = mb_strtolower(substr((string) strrchr($alamat, '@'), 1));

        return match (true) {
            in_array($domain, ['gmail.com', 'googlemail.com'], true) => 'gmail',
            in_array($domain, ['outlook.com', 'hotmail.com', 'live.com', 'msn.com'], true),
            str_starts_with($domain, 'outlook.'),
            str_starts_with($domain, 'hotmail.') => 'outlook',
            str_starts_with($domain, 'yahoo.'), $domain === 'ymail.com' => 'yahoo',
            in_array($domain, ['proton.me', 'protonmail.com', 'protonmail.ch', 'pm.me'], true) => 'proton',
            str_starts_with($domain, 'zoho.'), $domain === 'zohomail.com' => 'zoho',
            in_array($domain, ['icloud.com', 'me.com', 'mac.com'], true) => 'icloud',
            default => 'lainnya',
        };
    }

    private function proses(int $penggunaId, $gagang): array
    {
        $barisJudul = fgets($gagang);
        if ($barisJudul === false || trim($barisJudul) === '') {
            throw new InvalidArgumentException('Berkas kosong.');
        }

        $barisJudul = preg_replace('/^\xEF\xBB\xBF/', '', $barisJudul);
        $pemisah = substr_count($barisJudul, ';') > substr_count($barisJudul, ',') ? ';' : ',';
        $kolom = array_map(fn ($nama) => mb_strtolower(trim((string) $nama)), str_getcsv($barisJudul, $pemisah, '"', ''));
        $indeks = array_flip($kolom);

        if (! isset($indeks['alamat_surat_elektronik'])) {
            throw new InvalidArgumentException('Kolom "alamat_surat_elektronik" tidak ditemukan pada baris judul.');
        }

        $idPerKode = PenyediaSurat::query()->pluck('id', 'kode')->all();
        if (! isset($idPerKode['lainnya'])) {
            throw new InvalidArgumentException('Data penyedia surat belum diisi. Jalankan: php artisan db:seed');
        }

        $sudahAdaDiBasisData = array_flip(
            AkunSuratElektronik::query()->milikPengguna($penggunaId)->pluck('alamat_surat_elektronik')->all()
        );

        $laporan = [
            'total_baris' => 0,
            'berhasil_diimpor' => 0,
            'duplikat_dalam_berkas' => 0,
            'duplikat_di_basis_data' => 0,
            'tidak_valid' => 0,
            'kolom_diabaikan' => array_values(array_diff($kolom, self::KOLOM_DIKENAL)),
            'contoh_kesalahan' => [],
        ];

        $terlihat = [];
        $penampung = [];
        $nomorBaris = 1;
        $sekarang = now()->toDateTimeString();

        while (($baris = fgetcsv($gagang, 0, $pemisah, '"', '')) !== false) {
            $nomorBaris++;
            if ($baris === [null]) {
                continue; // baris kosong
            }

            $laporan['total_baris']++;
            $alamat = mb_strtolower(trim((string) ($baris[$indeks['alamat_surat_elektronik']] ?? '')));

            if ($alamat === '' || mb_strlen($alamat) > 254 || ! filter_var($alamat, FILTER_VALIDATE_EMAIL)) {
                $laporan['tidak_valid']++;
                $this->catatKesalahan($laporan, $nomorBaris, $alamat, 'Format alamat tidak valid');
                continue;
            }

            if (isset($terlihat[$alamat])) {
                $laporan['duplikat_dalam_berkas']++;
                $this->catatKesalahan($laporan, $nomorBaris, $alamat, 'Duplikat di dalam berkas');
                continue;
            }
            $terlihat[$alamat] = true;

            if (isset($sudahAdaDiBasisData[$alamat])) {
                $laporan['duplikat_di_basis_data']++;
                $this->catatKesalahan($laporan, $nomorBaris, $alamat, 'Sudah ada di daftar akun');
                continue;
            }

            $kode = mb_strtolower(trim((string) (isset($indeks['penyedia']) ? ($baris[$indeks['penyedia']] ?? '') : '')));
            if ($kode === '' || ! isset($idPerKode[$kode])) {
                $kode = self::tebakKodePenyedia($alamat);
            }

            $nama = isset($indeks['nama_tampilan']) ? mb_substr(trim((string) ($baris[$indeks['nama_tampilan']] ?? '')), 0, 100) : '';

            $penampung[] = [
                'pengguna_id' => $penggunaId,
                'penyedia_surat_id' => $idPerKode[$kode] ?? $idPerKode['lainnya'],
                'alamat_surat_elektronik' => $alamat,
                'nama_tampilan' => $nama !== '' ? $nama : null,
                'jenis_autentikasi' => AkunSuratElektronik::STATUS_BELUM_TERHUBUNG,
                'status_koneksi' => AkunSuratElektronik::STATUS_BELUM_TERHUBUNG,
                'waktu_dibuat' => $sekarang,
                'waktu_diperbarui' => $sekarang,
            ];

            if (count($penampung) >= self::UKURAN_BAGIAN) {
                $this->simpanBagian($penampung, $laporan);
                $penampung = [];
            }
        }

        if ($penampung !== []) {
            $this->simpanBagian($penampung, $laporan);
        }

        CatatanAudit::catat($penggunaId, 'impor_akun_surat', 'akun_surat', null, [
            'total_baris' => $laporan['total_baris'],
            'berhasil_diimpor' => $laporan['berhasil_diimpor'],
            'tidak_valid' => $laporan['tidak_valid'],
        ]);

        return $laporan;
    }

    private function simpanBagian(array $baris, array &$laporan): void
    {
        $tersimpan = DB::table('akun_surat')->insertOrIgnore($baris);
        $laporan['berhasil_diimpor'] += $tersimpan;
        // Selisih = baris yang bentrok dengan indeks unik (mis. impor bersamaan).
        $laporan['duplikat_di_basis_data'] += count($baris) - $tersimpan;
    }

    private function catatKesalahan(array &$laporan, int $baris, string $nilai, string $alasan): void
    {
        if (count($laporan['contoh_kesalahan']) < self::MAKS_CONTOH_KESALAHAN) {
            $laporan['contoh_kesalahan'][] = ['baris' => $baris, 'nilai' => mb_substr($nilai, 0, 60), 'alasan' => $alasan];
        }
    }
}
