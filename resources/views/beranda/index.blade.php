@extends('tata-letak.utama')
@section('judul', 'Beranda')

@section('isi')
    <div class="grid gap-4 sm:grid-cols-3">
        <div class="rounded-lg border bg-white p-5">
            <p class="text-sm text-slate-500">Total akun</p>
            <p class="text-3xl font-semibold">{{ number_format($total, 0, ',', '.') }}</p>
        </div>
        <div class="rounded-lg border bg-white p-5">
            <p class="text-sm text-slate-500">Akun aktif (terhubung)</p>
            <p class="text-3xl font-semibold text-green-700">{{ number_format($aktif, 0, ',', '.') }}</p>
        </div>
        <div class="rounded-lg border bg-white p-5">
            <p class="text-sm text-slate-500">Akun bermasalah</p>
            <p class="text-3xl font-semibold text-red-700">{{ number_format($bermasalah, 0, ',', '.') }}</p>
        </div>
    </div>

    <h2 class="mb-3 mt-8 text-lg font-semibold">Jumlah akun berdasarkan penyedia</h2>
    @if ($perPenyedia->isEmpty())
        <div class="rounded-lg border bg-white p-6 text-slate-600">
            Belum ada akun. <a class="text-indigo-600 underline" href="{{ route('akun-surat.tambah') }}">Tambah akun</a>
            atau <a class="text-indigo-600 underline" href="{{ route('impor.formulir') }}">impor dari CSV</a>.
        </div>
    @else
        <div class="overflow-x-auto rounded-lg border bg-white">
            <table class="w-full text-sm">
                <thead class="bg-slate-100 text-left"><tr><th class="p-3">Penyedia</th><th class="p-3 text-right">Jumlah akun</th></tr></thead>
                <tbody>
                @foreach ($perPenyedia as $baris)
                    <tr class="border-t"><td class="p-3">{{ $baris->nama }}</td><td class="p-3 text-right">{{ number_format($baris->jumlah_akun, 0, ',', '.') }}</td></tr>
                @endforeach
                </tbody>
            </table>
        </div>
    @endif
@endsection
