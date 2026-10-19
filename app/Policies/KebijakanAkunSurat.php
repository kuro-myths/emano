<?php

namespace App\Policies;

use App\Models\AkunSuratElektronik;
use App\Models\Pengguna;

/**
 * Setiap pengguna hanya boleh mengakses akun surat miliknya sendiri.
 */
class KebijakanAkunSurat
{
    public function lihat(Pengguna $pengguna, AkunSuratElektronik $akun): bool
    {
        return (int) $pengguna->id === (int) $akun->pengguna_id;
    }

    public function ubah(Pengguna $pengguna, AkunSuratElektronik $akun): bool
    {
        return $this->lihat($pengguna, $akun);
    }

    public function hapus(Pengguna $pengguna, AkunSuratElektronik $akun): bool
    {
        return $this->lihat($pengguna, $akun);
    }
}
