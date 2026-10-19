<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('penyedia_surat', function (Blueprint $tabel) {
            $tabel->id();
            $tabel->string('kode', 30)->unique();
            $tabel->string('nama', 100);
            $tabel->string('autentikasi_didukung', 100)->default('imap');
            $tabel->string('host_imap')->nullable();
            $tabel->unsignedSmallInteger('port_imap')->nullable();
            $tabel->string('host_smtp')->nullable();
            $tabel->unsignedSmallInteger('port_smtp')->nullable();
            $tabel->boolean('aktif')->default(true);
            $tabel->timestamp('waktu_dibuat')->useCurrent();
            $tabel->timestamp('waktu_diperbarui')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penyedia_surat');
    }
};
