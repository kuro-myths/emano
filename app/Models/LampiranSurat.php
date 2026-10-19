<?php

namespace App\Models;

use App\Models\Konsern\MemakaiKolomWaktuIndonesia;
use Illuminate\Database\Eloquent\Model;

class LampiranSurat extends Model
{
    use MemakaiKolomWaktuIndonesia;

    protected $table = 'lampiran_surat';

    protected $guarded = [];
}
