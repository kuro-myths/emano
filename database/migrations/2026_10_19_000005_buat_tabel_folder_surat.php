<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('folder_surat', function (Blueprint $tabel) {
            $tabel->id();
            $tabel->foreignId('akun_surat_id')->constrained('akun_surat')->cascadeOnDelete();
            $tabel->string('nama');
            $tabel->string('jenis', 30)->default('lainnya');
            $tabel->string('id_jarak_jauh')->nullable();
            $tabel->timestamp('waktu_dibuat')->useCurrent();
            $tabel->timestamp('waktu_diperbarui')->useCurrent();
            $tabel->unique(['akun_surat_id', 'id_jarak_jauh']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('folder_surat');
    }
};
