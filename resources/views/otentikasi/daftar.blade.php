<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Daftar — Pusat Surat Elektronik</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="flex min-h-screen items-center justify-center bg-slate-50 p-4">
<form method="POST" action="{{ route('daftar.proses') }}" class="w-full max-w-sm space-y-4 rounded-lg border bg-white p-6">
    @csrf
    <h1 class="text-xl font-semibold">Daftar</h1>
    <div>
        <label class="block text-sm font-medium" for="nama">Nama</label>
        <input id="nama" name="nama" required value="{{ old('nama') }}" class="mt-1 w-full rounded border p-2">
        @error('nama')<p class="text-sm text-red-600">{{ $message }}</p>@enderror
    </div>
    <div>
        <label class="block text-sm font-medium" for="alamat_surat_elektronik">Alamat surat elektronik</label>
        <input id="alamat_surat_elektronik" name="alamat_surat_elektronik" type="email" required value="{{ old('alamat_surat_elektronik') }}" class="mt-1 w-full rounded border p-2">
        @error('alamat_surat_elektronik')<p class="text-sm text-red-600">{{ $message }}</p>@enderror
    </div>
    <div>
        <label class="block text-sm font-medium" for="kata_sandi">Kata sandi (min. 10 karakter)</label>
        <input id="kata_sandi" name="kata_sandi" type="password" required class="mt-1 w-full rounded border p-2">
        @error('kata_sandi')<p class="text-sm text-red-600">{{ $message }}</p>@enderror
    </div>
    <div>
        <label class="block text-sm font-medium" for="kata_sandi_confirmation">Ulangi kata sandi</label>
        <input id="kata_sandi_confirmation" name="kata_sandi_confirmation" type="password" required class="mt-1 w-full rounded border p-2">
    </div>
    <button class="w-full rounded bg-indigo-600 py-2 text-white">Daftar</button>
    <p class="text-center text-sm">Sudah punya akun? <a class="text-indigo-600 underline" href="{{ route('login') }}">Masuk</a></p>
</form>
</body>
</html>
