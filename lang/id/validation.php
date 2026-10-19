<?php

// Nama berkas "validation.php" diwajibkan Laravel untuk pesan validasi.
return [
    'required' => ':attribute wajib diisi.',
    'string' => ':attribute harus berupa teks.',
    'integer' => ':attribute harus berupa angka bulat.',
    'email' => ':attribute harus berupa alamat surat elektronik yang valid.',
    'unique' => ':attribute sudah terdaftar.',
    'confirmed' => 'Konfirmasi :attribute tidak cocok.',
    'exists' => ':attribute yang dipilih tidak valid.',
    'file' => ':attribute harus berupa berkas.',
    'mimes' => ':attribute harus berupa berkas bertipe: :values.',
    'max' => [
        'string' => ':attribute maksimal :max karakter.',
        'file' => ':attribute maksimal :max kilobita.',
    ],
    'min' => [
        'string' => ':attribute minimal :min karakter.',
    ],
    'attributes' => [
        'nama' => 'nama',
        'alamat_surat_elektronik' => 'alamat surat elektronik',
        'nama_tampilan' => 'nama tampilan',
        'penyedia_surat_id' => 'penyedia surat',
        'kata_sandi' => 'kata sandi',
        'berkas_csv' => 'berkas CSV',
    ],
];
