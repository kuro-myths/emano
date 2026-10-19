<?php

namespace App\Models\Konsern;

/**
 * Mengganti nama kolom created_at / updated_at bawaan Eloquent
 * dengan waktu_dibuat / waktu_diperbarui.
 */
trait MemakaiKolomWaktuIndonesia
{
    public function getCreatedAtColumn(): string
    {
        return 'waktu_dibuat';
    }

    public function getUpdatedAtColumn(): string
    {
        return 'waktu_diperbarui';
    }
}
