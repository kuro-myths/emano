<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('catatan_sinkronisasi', function (Blueprint $tabel) {
            $tabel->id();
            $tabel->foreignId('pekerjaan_sinkronisasi_id')->constrained('pekerjaan_sinkronisasi')->cascadeOnDelete();
            $tabel->string('tingkat', 20)->default('info');
            $tabel->text('pesan');
            $tabel->timestamp('waktu_dibuat')->useCurrent();
            $tabel->timestamp('waktu_diperbarui')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('catatan_sinkronisasi');
    }
};
