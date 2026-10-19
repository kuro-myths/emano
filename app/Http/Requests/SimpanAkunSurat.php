<?php

namespace App\Http\Requests;

use App\Models\AkunSuratElektronik;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class SimpanAkunSurat extends FormRequest
{
    public function authorize(): bool
    {
        $akun = $this->route('akun');

        return ! $akun instanceof AkunSuratElektronik || Gate::allows('ubah', $akun);
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'alamat_surat_elektronik' => mb_strtolower(trim((string) $this->input('alamat_surat_elektronik'))),
        ]);
    }

    public function rules(): array
    {
        $akun = $this->route('akun');

        return [
            'alamat_surat_elektronik' => [
                'required', 'string', 'email', 'max:254',
                Rule::unique('akun_surat', 'alamat_surat_elektronik')
                    ->where('pengguna_id', $this->user()->id)
                    ->ignore($akun instanceof AkunSuratElektronik ? $akun->id : null),
            ],
            'nama_tampilan' => ['nullable', 'string', 'max:100'],
            'penyedia_surat_id' => ['required', 'integer', Rule::exists('penyedia_surat', 'id')->where('aktif', true)],
        ];
    }

    public function messages(): array
    {
        return [
            'alamat_surat_elektronik.unique' => 'Alamat surat elektronik ini sudah ada di daftar akun Anda.',
        ];
    }
}
