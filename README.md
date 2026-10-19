# Pusat Surat Elektronik

Aplikasi Laravel untuk menyimpan dan mengelola hingga 10.000 data akun surat elektronik
(Gmail, Outlook, Yahoo, Proton Mail, Zoho, iCloud, dan lainnya). Seluruh kode buatan sendiri
(folder, berkas, tabel, kolom, variabel, fungsi, antarmuka) berbahasa Indonesia.

> **Status: kerangka awal (skeleton), BELUM dijalankan atau diuji.**
> Lihat bagian "Status verifikasi" di bawah.

## Prasyarat

PHP (sesuai versi Laravel terbaru), Composer, Node.js + npm, rsync, PostgreSQL, Redis.

## Pemasangan

Repositori ini hanya berisi berkas buatan sendiri. Kerangka Laravel dipasang oleh skrip:

```bash
git clone https://github.com/kuro-myths/emano.git && cd emano
bash skrip/pasang.sh          # membuat kerangka Laravel, memasang Livewire, membuat .env
# ubah DB_* di .env, lalu:
php artisan migrate --seed
npm install && npm run build
php artisan serve
```

Pekerja antrean dan penjadwal (terminal terpisah):

```bash
php artisan queue:work --tries=3 --backoff=30
php artisan schedule:work
```

Dengan Docker: salin `.env.example` ke `.env`, set `DB_HOST=basis_data` dan `REDIS_HOST=redis`,
lalu `docker compose up --build`.

## Pengujian

```bash
php artisan test
```

## Yang sudah ada

- Basis data: 13 tabel berbahasa Indonesia (migrasi), indeks unik `(pengguna_id, alamat_surat_elektronik)`.
- Pendaftaran, masuk (dengan pembatasan percobaan), keluar.
- Beranda: total akun, akun aktif, akun bermasalah, jumlah per penyedia.
- Tambah, ubah, hapus, cari, filter, pagination, ekspor CSV akun surat; otorisasi per pengguna (kebijakan).
- Impor CSV (dibaca bertahap, 500 baris per sisipan) dengan validasi, deteksi duplikat, dan laporan.
- Token OAuth disimpan terenkripsi (cast `encrypted`) dan disembunyikan dari serialisasi.
- Catatan audit untuk aksi utama (tanpa token atau isi pesan).

## Keputusan desain

- Impor hanya menyimpan **alamat**. Kolom kata sandi pada CSV diabaikan; akun baru berstatus
  "belum terhubung" sampai dihubungkan lewat OAuth2/IMAP yang sah.
- Waktu disimpan UTC (`app.timezone` dibiarkan UTC) dan dikonversi ke Asia/Jakarta saat tampil
  (`App\Support\FormatWaktu`). Lokal aplikasi: `id`.

## Penyimpangan dari konvensi yang diwajibkan framework

- Model di `app/Models/` (bukan `app/Model/`) agar sesuai konvensi autoload Laravel.
- Berkas bahasa validasi bernama `lang/id/validation.php` (nama wajib Laravel), bukan `validasi.php`;
  Laravel 11+ memakai folder `lang/` di akar proyek.
- Rute bernama `login` (wajib framework untuk pengalihan tamu).
- Kunci kredensial `password` pada `Auth::attempt` (wajib penyedia autentikasi Laravel).

## Status verifikasi

Belum diverifikasi (belum pernah dijalankan): migrasi, seluruh pengujian, build aset, Docker,
dan skrip `skrip/pasang.sh`. Belum dibuat: integrasi penyedia (Gmail API, Microsoft Graph, IMAP/SMTP),
pembacaan/pengiriman pesan, sinkronisasi latar belakang, halaman Pengaturan/Riwayat/Catatan Aktivitas,
komponen Livewire (pencarian saat ini memakai formulir GET biasa).
