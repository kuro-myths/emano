<?php

namespace App\Models;

use App\Models\Konsern\MemakaiKolomWaktuIndonesia;
use Illuminate\Database\Eloquent\Model;

class CatatanSinkronisasi extends Model
{
    use MemakaiKolomWaktuIndonesia;

    protected $table = 'catatan_sinkronisasi';

    protected $guarded = [];
}
