<?php

namespace App\Models;

use App\Models\Konsern\MemakaiKolomWaktuIndonesia;
use Illuminate\Database\Eloquent\Model;

class FolderSurat extends Model
{
    use MemakaiKolomWaktuIndonesia;

    protected $table = 'folder_surat';

    protected $guarded = [];
}
