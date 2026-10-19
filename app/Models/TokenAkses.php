<?php

namespace App\Models;

use App\Models\Konsern\MemakaiKolomWaktuIndonesia;
use Illuminate\Database\Eloquent\Model;

class TokenAkses extends Model
{
    use MemakaiKolomWaktuIndonesia;

    protected $table = 'token_akses';

    protected $guarded = [];
}
