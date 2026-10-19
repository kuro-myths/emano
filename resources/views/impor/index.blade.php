@extends('tata-letak.utama')
@section('judul', 'Impor Data')

@section('isi')
    <div class="max-w-2xl space-y-4">
        <div class="rounded-lg border bg-white p-5 text-sm">
            <p class="mb-2">Unggah berkas CSV (maksimal 10 MB, ±10.000 baris) dengan baris judul:</p>
            <code class="block rounded bg-slate-100 p-2">alamat_surat_elektronik,nama_tampilan,penyedia</code>
            <p class="mt-2 text-slate-600">Hanya <strong>alamat</strong> yang disimpan. Kolom kata sandi, bila ada, diabaikan. Akun hasil impor berstatus
                “belum terhubung” sampai dihubungkan lewat OAuth2/IMAP yang sah.</p>
            <a class="mt-2 inline-block text-indigo-600 underline" href="{{ route('impor.templat') }}">Unduh templat CSV</a>
        </div>

        <form method="POST" action="{{ route('impor.proses') }}" enctype="multipart/form-data" class="space-y-3">
            @csrf
            <input type="file" name="berkas_csv" accept=".csv,.txt" required class="block w-full rounded border bg-white p-2">
            @error('berkas_csv')<p class="text-sm text-red-600">{{ $message }}</p>@enderror
            <button class="rounded bg-indigo-600 px-4 py-2 text-white">Impor</button>
        </form>

        @if ($laporan = session('laporan_impor'))
            <div class="rounded-lg border bg-white p-5 text-sm">
                <h2 class="mb-2 font-semibold">Laporan impor</h2>
                <ul class="space-y-1">
                    <li>Total baris dibaca: <strong>{{ number_format($laporan['total_baris'], 0, ',', '.') }}</strong></li>
                    <li>Berhasil diimpor: <strong class="text-green-700">{{ number_format($laporan['berhasil_diimpor'], 0, ',', '.') }}</strong></li>
                    <li>Duplikat dalam berkas: {{ number_format($laporan['duplikat_dalam_berkas'], 0, ',', '.') }}</li>
                    <li>Sudah ada di daftar akun: {{ number_format($laporan['duplikat_di_basis_data'], 0, ',', '.') }}</li>
                    <li>Tidak valid: <span class="text-red-700">{{ number_format($laporan['tidak_valid'], 0, ',', '.') }}</span></li>
                    @if ($laporan['kolom_diabaikan'])
                        <li>Kolom diabaikan: {{ implode(', ', $laporan['kolom_diabaikan']) }}</li>
                    @endif
                </ul>
                @if ($laporan['contoh_kesalahan'])
                    <p class="mb-1 mt-3 font-medium">Contoh baris bermasalah (maks. 50)</p>
                    <div class="max-h-64 overflow-auto">
                        <table class="w-full text-xs">
                            @foreach ($laporan['contoh_kesalahan'] as $k)
                                <tr class="border-t"><td class="p-1">Baris {{ $k['baris'] }}</td><td class="p-1">{{ $k['nilai'] }}</td><td class="p-1">{{ $k['alasan'] }}</td></tr>
                            @endforeach
                        </table>
                    </div>
                @endif
            </div>
        @endif
    </div>
@endsection
