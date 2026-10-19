<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengguna', function (Blueprint $tabel) {
            $tabel->id();
            $tabel->string('nama');
            $tabel->string('alamat_surat_elektronik')->unique();
            $tabel->string('kata_sandi');
            $tabel->string('token_ingat', 100)->nullable();
            $tabel->timestamp('waktu_dibuat')->useCurrent();
            $tabel->timestamp('waktu_diperbarui')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengguna');
    }
};
