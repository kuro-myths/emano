<?php

namespace App\Http\Controllers;

use App\Models\CatatanAudit;
use App\Models\PengaturanPengguna;
use App\Models\Pengguna;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PengelolaOtentikasi extends Controller
{
    public function formulirMasuk(): View
    {
        return view('otentikasi.masuk');
    }

    public function masuk(Request $permintaan): RedirectResponse
    {
        $data = $permintaan->validate([
            'alamat_surat_elektronik' => ['required', 'string', 'email'],
            'kata_sandi' => ['required', 'string'],
        ]);

        $kunci = Str::transliterate(Str::lower($data['alamat_surat_elektronik']) . '|' . $permintaan->ip());

        if (RateLimiter::tooManyAttempts($kunci, 5)) {
            return back()->withErrors([
                'alamat_surat_elektronik' => __('auth.throttle', ['seconds' => RateLimiter::availableIn($kunci)]),
            ])->onlyInput('alamat_surat_elektronik');
        }

        // Kunci "password" dipakai oleh penyedia autentikasi Laravel; nilainya dari kolom kata_sandi.
        $berhasil = Auth::attempt([
            'alamat_surat_elektronik' => Str::lower($data['alamat_surat_elektronik']),
            'password' => $data['kata_sandi'],
        ], $permintaan->boolean('ingat_saya'));

        if (! $berhasil) {
            RateLimiter::hit($kunci, 60);

            return back()->withErrors(['alamat_surat_elektronik' => __('auth.failed')])->onlyInput('alamat_surat_elektronik');
        }

        RateLimiter::clear($kunci);
        $permintaan->session()->regenerate();
        CatatanAudit::catat(Auth::id(), 'masuk');

        return redirect()->intended(route('beranda'));
    }

    public function formulirDaftar(): View
    {
        return view('otentikasi.daftar');
    }

    public function daftar(Request $permintaan): RedirectResponse
    {
        $permintaan->merge([
            'alamat_surat_elektronik' => Str::lower(trim((string) $permintaan->input('alamat_surat_elektronik'))),
        ]);

        $data = $permintaan->validate([
            'nama' => ['required', 'string', 'max:100'],
            'alamat_surat_elektronik' => ['required', 'string', 'email', 'max:254', 'unique:pengguna,alamat_surat_elektronik'],
            'kata_sandi' => ['required', 'string', 'min:10', 'confirmed'],
        ]);

        $pengguna = Pengguna::create($data);
        PengaturanPengguna::create(['pengguna_id' => $pengguna->id]);

        Auth::login($pengguna);
        $permintaan->session()->regenerate();
        CatatanAudit::catat($pengguna->id, 'daftar');

        return redirect()->route('beranda');
    }

    public function keluar(Request $permintaan): RedirectResponse
    {
        CatatanAudit::catat(Auth::id(), 'keluar');
        Auth::logout();
        $permintaan->session()->invalidate();
        $permintaan->session()->regenerateToken();

        return redirect()->route('login');
    }
}
