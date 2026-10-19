<?php

namespace App\Http\Controllers;

use App\Services\LayananImporAkun;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use InvalidArgumentException;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PengelolaImporSurat extends Controller
{
    public function formulir(): View
    {
        return view('impor.index');
    }

    public function proses(Request $permintaan, LayananImporAkun $layanan): RedirectResponse
    {
        $permintaan->validate([
            'berkas_csv' => ['required', 'file', 'mimes:csv,txt', 'max:10240'],
        ]);

        try {
            $laporan = $layanan->impor($permintaan->user()->id, $permintaan->file('berkas_csv')->getRealPath());
        } catch (InvalidArgumentException $kesalahan) {
            return back()->withErrors(['berkas_csv' => $kesalahan->getMessage()]);
        }

        return redirect()->route('impor.formulir')->with('laporan_impor', $laporan);
    }

    public function templat(): StreamedResponse
    {
        return response()->streamDownload(function () {
            echo "alamat_surat_elektronik,nama_tampilan,penyedia\n";
            echo "contoh@gmail.com,Contoh Satu,gmail\n";
            echo "contoh2@outlook.com,Contoh Dua,\n";
        }, 'templat-impor-akun.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }
}
