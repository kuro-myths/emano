<?php

namespace App\Models;

use App\Models\Konsern\MemakaiKolomWaktuIndonesia;
use App\Policies\KebijakanAkunSurat;
use Illuminate\Database\Eloquent\Attributes\UsePolicy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[UsePolicy(KebijakanAkunSurat::class)]
class AkunSuratElektronik extends Model
{
    use MemakaiKolomWaktuIndonesia;

    public const STATUS_BELUM_TERHUBUNG = 'belum_terhubung';
    public const STATUS_TERHUBUNG = 'terhubung';
    public const STATUS_BERMASALAH = 'bermasalah';
    public const STATUS_DIPUTUSKAN = 'diputuskan';

    public const DAFTAR_STATUS = [
        self::STATUS_BELUM_TERHUBUNG => 'Belum terhubung',
        self::STATUS_TERHUBUNG => 'Terhubung',
        self::STATUS_BERMASALAH => 'Bermasalah',
        self::STATUS_DIPUTUSKAN => 'Diputuskan',
    ];

    protected $table = 'akun_surat';

    // Token sengaja tidak boleh diisi massal; hanya diatur eksplisit oleh layanan koneksi.
    protected $fillable = [
        'pengguna_id',
        'penyedia_surat_id',
        'alamat_surat_elektronik',
        'nama_tampilan',
        'jenis_autentikasi',
        'status_koneksi',
        'waktu_sinkronisasi_terakhir',
    ];

    protected $hidden = ['token_akses_terenkripsi', 'token_penyegaran_terenkripsi'];

    protected function casts(): array
    {
        return [
            'token_akses_terenkripsi' => 'encrypted',
            'token_penyegaran_terenkripsi' => 'encrypted',
            'waktu_sinkronisasi_terakhir' => 'datetime',
        ];
    }

    public function pengguna(): BelongsTo
    {
        return $this->belongsTo(Pengguna::class, 'pengguna_id');
    }

    public function penyedia(): BelongsTo
    {
        return $this->belongsTo(PenyediaSurat::class, 'penyedia_surat_id');
    }

    public function folder(): HasMany
    {
        return $this->hasMany(FolderSurat::class, 'akun_surat_id');
    }

    public function labelStatus(): string
    {
        return self::DAFTAR_STATUS[$this->status_koneksi] ?? $this->status_koneksi;
    }

    public function scopeMilikPengguna(Builder $kueri, int $penggunaId): Builder
    {
        return $kueri->where('pengguna_id', $penggunaId);
    }

    public function scopeCari(Builder $kueri, ?string $kataPencarian): Builder
    {
        $kata = mb_strtolower(trim((string) $kataPencarian));
        if ($kata === '') {
            return $kueri;
        }

        $kata = str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $kata);

        return $kueri->where(function (Builder $w) use ($kata) {
            $w->whereRaw('LOWER(alamat_surat_elektronik) LIKE ?', ["%{$kata}%"])
                ->orWhereRaw('LOWER(nama_tampilan) LIKE ?', ["%{$kata}%"]);
        });
    }
}
