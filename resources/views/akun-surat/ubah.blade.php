@extends('tata-letak.utama')
@section('judul', 'Ubah Akun')
@section('isi')
    @include('akun-surat._formulir', ['aksi' => route('akun-surat.perbarui', $akun), 'metode' => 'PUT'])
@endsection
