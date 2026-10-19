<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengaturan_pengguna', function (Blueprint $tabel) {
            $tabel->id();
            $tabel->foreignId('pengguna_id')->unique()->constrained('pengguna')->cascadeOnDelete();
            $tabel->string('zona_waktu', 60)->default('Asia/Jakarta');
            $tabel->unsignedSmallInteger('jumlah_per_halaman')->default(25);
            $tabel->boolean('sembunyikan_alamat')->default(false);
            $tabel->timestamp('waktu_dibuat')->useCurrent();
            $tabel->timestamp('waktu_diperbarui')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengaturan_pengguna');
    }
};
