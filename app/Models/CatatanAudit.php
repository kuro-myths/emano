<?php

namespace App\Models;

use App\Models\Konsern\MemakaiKolomWaktuIndonesia;
use Illuminate\Database\Eloquent\Model;

class CatatanAudit extends Model
{
    use MemakaiKolomWaktuIndonesia;

    protected $table = 'catatan_audit';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['metadata' => 'array'];
    }

    /**
     * Mencatat aktivitas. Jangan pernah memasukkan token, kata sandi, atau isi pesan ke $metadata.
     */
    public static function catat(?int $penggunaId, string $aksi, ?string $jenisObjek = null, ?int $idObjek = null, array $metadata = []): self
    {
        return self::create([
            'pengguna_id' => $penggunaId,
            'aksi' => $aksi,
            'jenis_objek' => $jenisObjek,
            'id_objek' => $idObjek,
            'metadata' => $metadata ?: null,
            'alamat_ip' => request()?->ip(),
        ]);
    }
}
