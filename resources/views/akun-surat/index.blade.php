@extends('tata-letak.utama')
@section('judul', 'Semua Akun Surat')

@section('isi')
    <form method="GET" class="mb-4 flex flex-wrap gap-2">
        <input type="search" name="kata_pencarian" value="{{ request('kata_pencarian') }}" placeholder="Cari alamat atau nama…" class="min-w-0 flex-1 rounded border p-2">
        <select name="penyedia" class="rounded border p-2">
            <option value="">Semua penyedia</option>
            @foreach ($daftarPenyedia as $penyedia)
                <option value="{{ $penyedia->id }}" @selected((int) request('penyedia') === $penyedia->id)>{{ $penyedia->nama }}</option>
            @endforeach
        </select>
        <select name="status" class="rounded border p-2">
            <option value="">Semua status</option>
            @foreach ($daftarStatus as $kode => $label)
                <option value="{{ $kode }}" @selected(request('status') === $kode)>{{ $label }}</option>
            @endforeach
        </select>
        <button class="rounded bg-indigo-600 px-4 py-2 text-white">Cari</button>
        <a href="{{ route('akun-surat.ekspor') }}" class="rounded border px-4 py-2">Ekspor CSV</a>
        <a href="{{ route('akun-surat.tambah') }}" class="rounded border px-4 py-2">Tambah</a>
    </form>

    <div class="overflow-x-auto rounded-lg border bg-white">
        <table class="w-full text-sm">
            <thead class="bg-slate-100 text-left">
            <tr>
                <th class="p-3">Alamat</th><th class="p-3">Nama</th><th class="p-3">Penyedia</th>
                <th class="p-3">Status</th><th class="p-3">Sinkronisasi terakhir</th><th class="p-3"></th>
            </tr>
            </thead>
            <tbody>
            @forelse ($akun as $baris)
                <tr class="border-t">
                    <td class="p-3">{{ $baris->alamat_surat_elektronik }}</td>
                    <td class="p-3">{{ $baris->nama_tampilan ?? '—' }}</td>
                    <td class="p-3">{{ $baris->penyedia?->nama }}</td>
                    <td class="p-3">
                        @php
                            $warna = match ($baris->status_koneksi) {
                                'terhubung' => 'bg-green-100 text-green-800',
                                'bermasalah' => 'bg-red-100 text-red-800',
                                'diputuskan' => 'bg-amber-100 text-amber-800',
                                default => 'bg-slate-100 text-slate-700',
                            };
                        @endphp
                        <span class="rounded px-2 py-1 text-xs {{ $warna }}">{{ $baris->labelStatus() }}</span>
                    </td>
                    <td class="p-3">{{ \App\Support\FormatWaktu::ringkas($baris->waktu_sinkronisasi_terakhir) }}</td>
                    <td class="flex gap-2 p-3">
                        <a class="text-indigo-600 underline" href="{{ route('akun-surat.ubah', $baris) }}">Ubah</a>
                        <form method="POST" action="{{ route('akun-surat.hapus', $baris) }}"
                              onsubmit="return confirm('Hapus akun ini? Tindakan tidak dapat dibatalkan.')">
                            @csrf @method('DELETE')
                            <button class="text-red-600 underline">Hapus</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="p-6 text-center text-slate-500">Tidak ada akun yang cocok.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $akun->links() }}</div>
@endsection
