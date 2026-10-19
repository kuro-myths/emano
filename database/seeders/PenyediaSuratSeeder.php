<?php

namespace Database\Seeders;

use App\Models\PenyediaSurat;
use Illuminate\Database\Seeder;

class PenyediaSuratSeeder extends Seeder
{
    public function run(): void
    {
        $daftar = [
            ['gmail', 'Gmail', 'oauth2', 'imap.gmail.com', 993, 'smtp.gmail.com', 587],
            ['outlook', 'Outlook', 'oauth2', 'outlook.office365.com', 993, 'smtp.office365.com', 587],
            ['yahoo', 'Yahoo Mail', 'imap', 'imap.mail.yahoo.com', 993, 'smtp.mail.yahoo.com', 587],
            ['proton', 'Proton Mail', 'bridge', null, null, null, null],
            ['zoho', 'Zoho Mail', 'oauth2,imap', 'imap.zoho.com', 993, 'smtp.zoho.com', 587],
            ['icloud', 'iCloud', 'imap', 'imap.mail.me.com', 993, 'smtp.mail.me.com', 587],
            ['lainnya', 'Penyedia Lainnya', 'imap', null, null, null, null],
        ];

        foreach ($daftar as [$kode, $nama, $autentikasi, $hostImap, $portImap, $hostSmtp, $portSmtp]) {
            PenyediaSurat::updateOrCreate(['kode' => $kode], [
                'nama' => $nama,
                'autentikasi_didukung' => $autentikasi,
                'host_imap' => $hostImap,
                'port_imap' => $portImap,
                'host_smtp' => $hostSmtp,
                'port_smtp' => $portSmtp,
                'aktif' => true,
            ]);
        }
    }
}
