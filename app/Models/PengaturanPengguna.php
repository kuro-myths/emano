<?php

namespace App\Models;

use App\Models\Konsern\MemakaiKolomWaktuIndonesia;
use Illuminate\Database\Eloquent\Model;

class PengaturanPengguna extends Model
{
    use MemakaiKolomWaktuIndonesia;

    protected $table = 'pengaturan_pengguna';

    protected $guarded = [];
}
