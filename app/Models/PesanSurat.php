<?php

namespace App\Models;

use App\Models\Konsern\MemakaiKolomWaktuIndonesia;
use Illuminate\Database\Eloquent\Model;

class PesanSurat extends Model
{
    use MemakaiKolomWaktuIndonesia;

    protected $table = 'pesan_surat';

    protected $guarded = [];
}
