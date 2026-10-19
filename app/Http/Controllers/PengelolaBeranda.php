<?php

namespace App\Http\Controllers;

use App\Models\AkunSuratElektronik;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PengelolaBeranda extends Controller
{
    public function __invoke(Request $permintaan): View
    {
        $penggunaId = $permintaan->user()->id;

        $ringkasan = AkunSuratElektronik::query()
            ->milikPengguna($penggunaId)
            ->selectRaw(
                'COUNT(*) AS jumlah_akun, '
                . 'SUM(CASE WHEN status_koneksi = ? THEN 1 ELSE 0 END) AS jumlah_aktif, '
                . 'SUM(CASE WHEN status_koneksi = ? THEN 1 ELSE 0 END) AS jumlah_bermasalah',
                [AkunSuratElektronik::STATUS_TERHUBUNG, AkunSuratElektronik::STATUS_BERMASALAH]
            )
            ->first();

        $perPenyedia = DB::table('akun_surat')
            ->join('penyedia_surat', 'penyedia_surat.id', '=', 'akun_surat.penyedia_surat_id')
            ->where('akun_surat.pengguna_id', $penggunaId)
            ->select('penyedia_surat.nama', DB::raw('COUNT(*) AS jumlah_akun'))
            ->groupBy('penyedia_surat.nama')
            ->orderByDesc('jumlah_akun')
            ->get();

        return view('beranda.index', [
            'total' => (int) $ringkasan->jumlah_akun,
            'aktif' => (int) $ringkasan->jumlah_aktif,
            'bermasalah' => (int) $ringkasan->jumlah_bermasalah,
            'perPenyedia' => $perPenyedia,
        ]);
    }
}
