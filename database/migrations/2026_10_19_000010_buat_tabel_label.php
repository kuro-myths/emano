<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('label_surat', function (Blueprint $tabel) {
            $tabel->id();
            $tabel->foreignId('pengguna_id')->constrained('pengguna')->cascadeOnDelete();
            $tabel->string('nama', 60);
            $tabel->string('warna', 20)->default('abu');
            $tabel->timestamp('waktu_dibuat')->useCurrent();
            $tabel->timestamp('waktu_diperbarui')->useCurrent();
            $tabel->unique(['pengguna_id', 'nama']);
        });

        Schema::create('label_akun_surat', function (Blueprint $tabel) {
            $tabel->foreignId('label_surat_id')->constrained('label_surat')->cascadeOnDelete();
            $tabel->foreignId('akun_surat_id')->constrained('akun_surat')->cascadeOnDelete();
            $tabel->primary(['label_surat_id', 'akun_surat_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('label_akun_surat');
        Schema::dropIfExists('label_surat');
    }
};
