<?php

namespace App\Models;

use App\Models\Konsern\MemakaiKolomWaktuIndonesia;
use Database\Factories\PenggunaFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Pengguna extends Authenticatable
{
    use HasFactory, Notifiable, MemakaiKolomWaktuIndonesia;

    protected $table = 'pengguna';

    protected $fillable = ['nama', 'alamat_surat_elektronik', 'kata_sandi'];

    protected $hidden = ['kata_sandi', 'token_ingat'];

    protected function casts(): array
    {
        return ['kata_sandi' => 'hashed'];
    }

    // Kontrak autentikasi Laravel: memberi tahu kolom kata sandi dan token ingat yang dipakai.
    public function getAuthPasswordName(): string
    {
        return 'kata_sandi';
    }

    public function getRememberTokenName(): string
    {
        return 'token_ingat';
    }

    protected static function newFactory(): PenggunaFactory
    {
        return PenggunaFactory::new();
    }

    public function akunSurat(): HasMany
    {
        return $this->hasMany(AkunSuratElektronik::class, 'pengguna_id');
    }

    public function pengaturan(): HasOne
    {
        return $this->hasOne(PengaturanPengguna::class, 'pengguna_id');
    }
}
