<?php

namespace Tests\Unit;

use App\Services\LayananImporAkun;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class LayananSuratElektronikTest extends TestCase
{
    public static function dataDomain(): array
    {
        return [
            'gmail' => ['a@gmail.com', 'gmail'],
            'googlemail' => ['a@googlemail.com', 'gmail'],
            'outlook' => ['a@outlook.co.id', 'outlook'],
            'hotmail' => ['a@hotmail.com', 'outlook'],
            'yahoo' => ['a@yahoo.co.id', 'yahoo'],
            'proton' => ['a@proton.me', 'proton'],
            'zoho' => ['a@zoho.com', 'zoho'],
            'icloud' => ['a@icloud.com', 'icloud'],
            'huruf besar' => ['A@GMAIL.COM', 'gmail'],
            'domain lain' => ['a@sekolah.sch.id', 'lainnya'],
            'tanpa at' => ['bukan-alamat', 'lainnya'],
        ];
    }

    #[Test]
    #[DataProvider('dataDomain')]
    public function menebak_kode_penyedia_dari_domain(string $alamat, string $diharapkan): void
    {
        $this->assertSame($diharapkan, LayananImporAkun::tebakKodePenyedia($alamat));
    }
}
