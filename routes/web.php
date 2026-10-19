<?php

use App\Http\Controllers\PengelolaAkunSurat;
use App\Http\Controllers\PengelolaBeranda;
use App\Http\Controllers\PengelolaImporSurat;
use App\Http\Controllers\PengelolaOtentikasi;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('beranda'));

Route::middleware('guest')->group(function () {
    // Nama rute "login" diwajibkan framework (pengalihan tamu ke halaman masuk).
    Route::get('/masuk', [PengelolaOtentikasi::class, 'formulirMasuk'])->name('login');
    Route::post('/masuk', [PengelolaOtentikasi::class, 'masuk'])->name('masuk.proses');
    Route::get('/daftar', [PengelolaOtentikasi::class, 'formulirDaftar'])->name('daftar');
    Route::post('/daftar', [PengelolaOtentikasi::class, 'daftar'])->name('daftar.proses')->middleware('throttle:10,1');
});

Route::middleware('auth')->group(function () {
    Route::post('/keluar', [PengelolaOtentikasi::class, 'keluar'])->name('keluar');
    Route::get('/beranda', PengelolaBeranda::class)->name('beranda');

    Route::get('/akun-surat', [PengelolaAkunSurat::class, 'index'])->name('akun-surat.index');
    Route::get('/akun-surat/tambah', [PengelolaAkunSurat::class, 'tambah'])->name('akun-surat.tambah');
    Route::post('/akun-surat', [PengelolaAkunSurat::class, 'simpan'])->name('akun-surat.simpan');
    Route::get('/akun-surat/ekspor', [PengelolaAkunSurat::class, 'ekspor'])->name('akun-surat.ekspor');
    Route::get('/akun-surat/{akun}/ubah', [PengelolaAkunSurat::class, 'ubah'])->name('akun-surat.ubah');
    Route::put('/akun-surat/{akun}', [PengelolaAkunSurat::class, 'perbarui'])->name('akun-surat.perbarui');
    Route::delete('/akun-surat/{akun}', [PengelolaAkunSurat::class, 'hapus'])->name('akun-surat.hapus');

    Route::get('/impor', [PengelolaImporSurat::class, 'formulir'])->name('impor.formulir');
    Route::get('/impor/templat', [PengelolaImporSurat::class, 'templat'])->name('impor.templat');
    Route::post('/impor', [PengelolaImporSurat::class, 'proses'])->name('impor.proses')->middleware('throttle:10,1');
});
