<?php

namespace App\Models;

use App\Models\Konsern\MemakaiKolomWaktuIndonesia;
use Illuminate\Database\Eloquent\Model;

class LabelSurat extends Model
{
    use MemakaiKolomWaktuIndonesia;

    protected $table = 'label_surat';

    protected $guarded = [];
}
