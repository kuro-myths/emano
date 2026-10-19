<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pekerjaan_sinkronisasi', function (Blueprint $tabel) {
            $tabel->id();
            $tabel->foreignId('akun_surat_id')->constrained('akun_surat')->cascadeOnDelete();
            $tabel->string('status', 30)->default('menunggu');
            $tabel->unsignedSmallInteger('jumlah_percobaan')->default(0);
            $tabel->text('pesan_kesalahan')->nullable();
            $tabel->timestamp('waktu_mulai')->nullable();
            $tabel->timestamp('waktu_selesai')->nullable();
            $tabel->timestamp('waktu_dibuat')->useCurrent();
            $tabel->timestamp('waktu_diperbarui')->useCurrent();
            $tabel->index(['akun_surat_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pekerjaan_sinkronisasi');
    }
};
