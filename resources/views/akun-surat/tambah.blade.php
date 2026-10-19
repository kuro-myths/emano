@extends('tata-letak.utama')
@section('judul', 'Tambah Akun')
@section('isi')
    @include('akun-surat._formulir', ['aksi' => route('akun-surat.simpan'), 'metode' => 'POST', 'akun' => null])
@endsection
