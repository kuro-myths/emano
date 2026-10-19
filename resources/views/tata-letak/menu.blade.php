@php
    $daftarMenu = [
        ['Beranda', 'beranda'],
        ['Semua Akun Surat', 'akun-surat.index'],
        ['Tambah Akun', 'akun-surat.tambah'],
        ['Impor Data', 'impor.formulir'],
        ['Kotak Masuk', null],
        ['Pesan Terkirim', null],
        ['Draf', null],
        ['Folder Surat', null],
        ['Penyedia Surat', null],
        ['Riwayat Sinkronisasi', null],
        ['Catatan Aktivitas', null],
        ['Pengaturan', null],
    ];
@endphp
<ul class="space-y-1 text-sm">
    @foreach ($daftarMenu as [$label, $rute])
        <li>
            @if ($rute)
                <a href="{{ route($rute) }}"
                   class="block rounded px-3 py-2 {{ request()->routeIs($rute) ? 'bg-indigo-600 text-white' : 'hover:bg-slate-200' }}">{{ $label }}</a>
            @else
                <span class="block rounded px-3 py-2 text-slate-400" title="Segera hadir">{{ $label }}</span>
            @endif
        </li>
    @endforeach
    <li>
        <form method="POST" action="{{ route('keluar') }}">
            @csrf
            <button type="submit" class="w-full rounded px-3 py-2 text-left hover:bg-slate-200">Keluar</button>
        </form>
    </li>
</ul>
