<?php

namespace App\Models;

use App\Models\Konsern\MemakaiKolomWaktuIndonesia;
use Illuminate\Database\Eloquent\Model;

class PekerjaanSinkronisasi extends Model
{
    use MemakaiKolomWaktuIndonesia;

    protected $table = 'pekerjaan_sinkronisasi';

    protected $guarded = [];
}
