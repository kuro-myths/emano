<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('akun_surat', function (Blueprint $tabel) {
            $tabel->id();
            $tabel->foreignId('pengguna_id')->constrained('pengguna')->cascadeOnDelete();
            $tabel->foreignId('penyedia_surat_id')->constrained('penyedia_surat');
            $tabel->string('alamat_surat_elektronik', 254);
            $tabel->string('nama_tampilan', 100)->nullable();
            $tabel->string('jenis_autentikasi', 30)->default('belum_terhubung');
            $tabel->text('token_akses_terenkripsi')->nullable();
            $tabel->text('token_penyegaran_terenkripsi')->nullable();
            $tabel->string('status_koneksi', 30)->default('belum_terhubung');
            $tabel->timestamp('waktu_sinkronisasi_terakhir')->nullable();
            $tabel->timestamp('waktu_dibuat')->useCurrent();
            $tabel->timestamp('waktu_diperbarui')->useCurrent();

            // Pencegahan duplikasi per pengguna (alamat selalu disimpan huruf kecil).
            $tabel->unique(['pengguna_id', 'alamat_surat_elektronik'], 'akun_surat_pengguna_alamat_unik');
            $tabel->index(['pengguna_id', 'status_koneksi'], 'akun_surat_pengguna_status_idx');
            $tabel->index(['pengguna_id', 'penyedia_surat_id'], 'akun_surat_pengguna_penyedia_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('akun_surat');
    }
};
