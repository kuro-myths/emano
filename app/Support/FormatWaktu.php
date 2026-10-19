<?php

namespace App\Support;

use Carbon\CarbonInterface;

/**
 * Basis data menyimpan waktu dalam UTC; kelas ini mengonversi ke Asia/Jakarta untuk tampilan.
 */
class FormatWaktu
{
    public const ZONA_TAMPILAN = 'Asia/Jakarta';

    /** Contoh: Senin, 19 Oktober 2026 */
    public static function lengkap(?CarbonInterface $waktu): string
    {
        return self::ubah($waktu, fn ($w) => $w->translatedFormat('l, j F Y'));
    }

    /** Contoh: 19/10/2026 */
    public static function ringkas(?CarbonInterface $waktu): string
    {
        return self::ubah($waktu, fn ($w) => $w->format('d/m/Y'));
    }

    /** Contoh: 08.30 WIB */
    public static function jam(?CarbonInterface $waktu): string
    {
        return self::ubah($waktu, fn ($w) => $w->format('H.i') . ' WIB');
    }

    public static function lengkapDenganJam(?CarbonInterface $waktu): string
    {
        return self::ubah($waktu, fn ($w) => $w->translatedFormat('l, j F Y') . ', ' . $w->format('H.i') . ' WIB');
    }

    private static function ubah(?CarbonInterface $waktu, callable $pemformat): string
    {
        if ($waktu === null) {
            return '—';
        }

        return $pemformat($waktu->copy()->timezone(self::ZONA_TAMPILAN)->locale('id'));
    }
}
