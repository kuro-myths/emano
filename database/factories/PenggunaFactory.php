<?php

namespace Database\Factories;

use App\Models\Pengguna;
use Illuminate\Database\Eloquent\Factories\Factory;

class PenggunaFactory extends Factory
{
    protected $model = Pengguna::class;

    public function definition(): array
    {
        return [
            'nama' => fake()->name(),
            'alamat_surat_elektronik' => fake()->unique()->safeEmail(),
            'kata_sandi' => 'kata-sandi-uji-123',
        ];
    }
}
