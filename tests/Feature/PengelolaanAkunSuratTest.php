<?php

namespace Tests\Feature;

use App\Models\AkunSuratElektronik;
use App\Models\PenyediaSurat;
use App\Models\Pengguna;
use Database\Seeders\PenyediaSuratSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PengelolaanAkunSuratTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(PenyediaSuratSeeder::class);
    }

    private function buatAkun(Pengguna $pemilik, string $alamat): AkunSuratElektronik
    {
        return AkunSuratElektronik::create([
            'pengguna_id' => $pemilik->id,
            'penyedia_surat_id' => PenyediaSurat::where('kode', 'gmail')->value('id'),
            'alamat_surat_elektronik' => $alamat,
        ]);
    }

    #[Test]
    public function tamu_dialihkan_ke_halaman_masuk(): void
    {
        $this->get('/akun-surat')->assertRedirect('/masuk');
    }

    #[Test]
    public function pengguna_dapat_menambah_akun_dan_alamat_dinormalkan(): void
    {
        $pengguna = Pengguna::factory()->create();

        $this->actingAs($pengguna)->post('/akun-surat', [
            'alamat_surat_elektronik' => '  Budi@Gmail.COM ',
            'penyedia_surat_id' => PenyediaSurat::where('kode', 'gmail')->value('id'),
        ])->assertRedirect('/akun-surat');

        $this->assertDatabaseHas('akun_surat', ['pengguna_id' => $pengguna->id, 'alamat_surat_elektronik' => 'budi@gmail.com']);
    }

    #[Test]
    public function alamat_duplikat_pada_pengguna_yang_sama_ditolak(): void
    {
        $pengguna = Pengguna::factory()->create();
        $this->buatAkun($pengguna, 'budi@gmail.com');

        $this->actingAs($pengguna)->post('/akun-surat', [
            'alamat_surat_elektronik' => 'BUDI@gmail.com',
            'penyedia_surat_id' => PenyediaSurat::where('kode', 'gmail')->value('id'),
        ])->assertSessionHasErrors('alamat_surat_elektronik');

        $this->assertSame(1, AkunSuratElektronik::count());
    }

    #[Test]
    public function pengguna_tidak_dapat_mengubah_atau_menghapus_akun_milik_orang_lain(): void
    {
        $pemilik = Pengguna::factory()->create();
        $penyusup = Pengguna::factory()->create();
        $akun = $this->buatAkun($pemilik, 'milik@gmail.com');

        $this->actingAs($penyusup)->get("/akun-surat/{$akun->id}/ubah")->assertForbidden();
        $this->actingAs($penyusup)->put("/akun-surat/{$akun->id}", [
            'alamat_surat_elektronik' => 'baru@gmail.com',
            'penyedia_surat_id' => $akun->penyedia_surat_id,
        ])->assertForbidden();
        $this->actingAs($penyusup)->delete("/akun-surat/{$akun->id}")->assertForbidden();

        $this->assertDatabaseHas('akun_surat', ['id' => $akun->id, 'alamat_surat_elektronik' => 'milik@gmail.com']);
    }

    #[Test]
    public function daftar_akun_hanya_menampilkan_milik_sendiri_dengan_pagination(): void
    {
        $pengguna = Pengguna::factory()->create();
        $lain = Pengguna::factory()->create();
        for ($i = 1; $i <= 60; $i++) {
            $this->buatAkun($pengguna, "akun{$i}@gmail.com");
        }
        $this->buatAkun($lain, 'rahasia@gmail.com');

        $respons = $this->actingAs($pengguna)->get('/akun-surat')->assertOk()->assertDontSee('rahasia@gmail.com');
        $halaman = $respons->viewData('akun');

        $this->assertSame(60, $halaman->total());
        $this->assertSame(25, $halaman->perPage());
        $this->assertSame(3, $halaman->lastPage());
    }

    #[Test]
    public function pencarian_menyaring_berdasarkan_kata_pencarian(): void
    {
        $pengguna = Pengguna::factory()->create();
        $this->buatAkun($pengguna, 'cari.saya@gmail.com');
        $this->buatAkun($pengguna, 'lainnya@gmail.com');

        $halaman = $this->actingAs($pengguna)->get('/akun-surat?kata_pencarian=cari.saya')->viewData('akun');

        $this->assertSame(1, $halaman->total());
    }

    #[Test]
    public function impor_10000_baris_melaporkan_duplikat_dan_baris_tidak_valid(): void
    {
        $pengguna = Pengguna::factory()->create();

        $baris = ['alamat_surat_elektronik,nama_tampilan,kata_sandi'];
        for ($i = 1; $i <= 10000; $i++) {
            $baris[] = "pengguna{$i}@gmail.com,Nama {$i},rahasia{$i}";
        }
        for ($i = 1; $i <= 100; $i++) {
            $baris[] = 'PENGGUNA' . $i . '@GMAIL.COM,Ganda,x'; // duplikat dalam berkas (beda huruf besar)
        }
        for ($i = 1; $i <= 5; $i++) {
            $baris[] = "bukan-alamat-{$i},Rusak,x";
        }
        $berkas = UploadedFile::fake()->createWithContent('akun.csv', implode("\n", $baris) . "\n");

        $this->actingAs($pengguna)->post('/impor', ['berkas_csv' => $berkas])->assertRedirect('/impor');

        $laporan = session('laporan_impor');
        $this->assertSame(10105, $laporan['total_baris']);
        $this->assertSame(10000, $laporan['berhasil_diimpor']);
        $this->assertSame(100, $laporan['duplikat_dalam_berkas']);
        $this->assertSame(5, $laporan['tidak_valid']);
        $this->assertContains('kata_sandi', $laporan['kolom_diabaikan']);
        $this->assertSame(10000, AkunSuratElektronik::count());
        $this->assertDatabaseMissing('akun_surat', ['status_koneksi' => 'terhubung']);
    }

    #[Test]
    public function impor_ulang_tidak_membuat_data_ganda(): void
    {
        $pengguna = Pengguna::factory()->create();
        $isi = "alamat_surat_elektronik\na@gmail.com\nb@yahoo.com\n";

        $this->actingAs($pengguna)->post('/impor', ['berkas_csv' => UploadedFile::fake()->createWithContent('a.csv', $isi)]);
        $this->actingAs($pengguna)->post('/impor', ['berkas_csv' => UploadedFile::fake()->createWithContent('a.csv', $isi)]);

        $laporan = session('laporan_impor');
        $this->assertSame(0, $laporan['berhasil_diimpor']);
        $this->assertSame(2, $laporan['duplikat_di_basis_data']);
        $this->assertSame(2, AkunSuratElektronik::count());
    }

    #[Test]
    public function impor_menolak_berkas_tanpa_kolom_alamat(): void
    {
        $pengguna = Pengguna::factory()->create();

        $this->actingAs($pengguna)
            ->post('/impor', ['berkas_csv' => UploadedFile::fake()->createWithContent('x.csv', "nama\nBudi\n")])
            ->assertSessionHasErrors('berkas_csv');
    }

    #[Test]
    public function token_tersimpan_terenkripsi_dan_tidak_muncul_pada_serialisasi_model(): void
    {
        $pengguna = Pengguna::factory()->create();
        $akun = $this->buatAkun($pengguna, 'a@gmail.com');
        $akun->token_akses_terenkripsi = 'token-rahasia';
        $akun->save();

        $this->assertArrayNotHasKey('token_akses_terenkripsi', $akun->fresh()->toArray());
        $this->assertNotSame('token-rahasia', DB::table('akun_surat')->value('token_akses_terenkripsi'));
        $this->assertSame('token-rahasia', $akun->fresh()->token_akses_terenkripsi);
    }
}
