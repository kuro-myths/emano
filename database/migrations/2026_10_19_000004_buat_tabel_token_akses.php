<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('token_akses', function (Blueprint $tabel) {
            $tabel->id();
            $tabel->foreignId('akun_surat_id')->constrained('akun_surat')->cascadeOnDelete();
            $tabel->string('jenis', 30);
            $tabel->text('token_terenkripsi');
            $tabel->text('lingkup')->nullable();
            $tabel->timestamp('waktu_kedaluwarsa')->nullable();
            $tabel->timestamp('waktu_dibuat')->useCurrent();
            $tabel->timestamp('waktu_diperbarui')->useCurrent();
            $tabel->index(['akun_surat_id', 'jenis']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('token_akses');
    }
};
