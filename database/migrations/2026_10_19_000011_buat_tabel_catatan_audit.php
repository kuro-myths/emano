<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('catatan_audit', function (Blueprint $tabel) {
            $tabel->id();
            $tabel->foreignId('pengguna_id')->nullable()->constrained('pengguna')->nullOnDelete();
            $tabel->string('aksi', 60);
            $tabel->string('jenis_objek', 60)->nullable();
            $tabel->unsignedBigInteger('id_objek')->nullable();
            $tabel->json('metadata')->nullable();
            $tabel->string('alamat_ip', 45)->nullable();
            $tabel->timestamp('waktu_dibuat')->useCurrent();
            $tabel->timestamp('waktu_diperbarui')->useCurrent();
            $tabel->index(['pengguna_id', 'waktu_dibuat']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('catatan_audit');
    }
};
