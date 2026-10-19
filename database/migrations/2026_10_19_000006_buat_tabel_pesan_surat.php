<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pesan_surat', function (Blueprint $tabel) {
            $tabel->id();
            $tabel->foreignId('akun_surat_id')->constrained('akun_surat')->cascadeOnDelete();
            $tabel->foreignId('folder_surat_id')->nullable()->constrained('folder_surat')->nullOnDelete();
            $tabel->string('id_pesan_jarak_jauh');
            $tabel->string('subjek', 500)->nullable();
            $tabel->string('pengirim', 320)->nullable();
            $tabel->text('penerima')->nullable();
            $tabel->text('ringkasan')->nullable();
            $tabel->boolean('sudah_dibaca')->default(false);
            $tabel->timestamp('waktu_pesan')->nullable();
            $tabel->timestamp('waktu_dibuat')->useCurrent();
            $tabel->timestamp('waktu_diperbarui')->useCurrent();
            $tabel->unique(['akun_surat_id', 'id_pesan_jarak_jauh']);
            $tabel->index(['akun_surat_id', 'waktu_pesan']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pesan_surat');
    }
};
