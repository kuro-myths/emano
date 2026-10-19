<?php

namespace App\Models;

use App\Models\Konsern\MemakaiKolomWaktuIndonesia;
use Illuminate\Database\Eloquent\Model;

class PenyediaSurat extends Model
{
    use MemakaiKolomWaktuIndonesia;

    protected $table = 'penyedia_surat';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['aktif' => 'boolean'];
    }
}
