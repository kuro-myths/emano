<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lampiran_surat', function (Blueprint $tabel) {
            $tabel->id();
            $tabel->foreignId('pesan_surat_id')->constrained('pesan_surat')->cascadeOnDelete();
            $tabel->string('nama_berkas');
            $tabel->string('jenis_mime', 150)->nullable();
            $tabel->unsignedBigInteger('ukuran_bita')->default(0);
            $tabel->string('jalur_penyimpanan')->nullable();
            $tabel->timestamp('waktu_dibuat')->useCurrent();
            $tabel->timestamp('waktu_diperbarui')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lampiran_surat');
    }
};
