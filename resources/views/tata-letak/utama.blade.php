<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('judul', 'Beranda') — Pusat Surat Elektronik</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-50 text-slate-800">
<div class="min-h-screen md:flex">
    <aside class="hidden w-64 shrink-0 border-r bg-white p-4 md:block">
        <p class="mb-4 px-3 text-lg font-semibold">Pusat Surat Elektronik</p>
        @include('tata-letak.menu')
    </aside>

    <div class="flex-1">
        <details class="border-b bg-white p-3 md:hidden">
            <summary class="cursor-pointer font-semibold">Menu</summary>
            <div class="mt-3">@include('tata-letak.menu')</div>
        </details>

        <main class="mx-auto max-w-6xl p-4 md:p-8">
            @if (session('berhasil'))
                <div class="mb-4 rounded border border-green-300 bg-green-50 p-3 text-green-800">{{ session('berhasil') }}</div>
            @endif
            @if (session('gagal'))
                <div class="mb-4 rounded border border-red-300 bg-red-50 p-3 text-red-800">{{ session('gagal') }}</div>
            @endif

            <h1 class="mb-6 text-2xl font-semibold">@yield('judul', 'Beranda')</h1>
            @yield('isi')

            <p class="mt-10 text-xs text-slate-400">{{ \App\Support\FormatWaktu::lengkapDenganJam(now()) }}</p>
        </main>
    </div>
</div>
</body>
</html>
