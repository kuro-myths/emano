<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Masuk — Pusat Surat Elektronik</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="flex min-h-screen items-center justify-center bg-slate-50 p-4">
<form method="POST" action="{{ route('masuk.proses') }}" class="w-full max-w-sm space-y-4 rounded-lg border bg-white p-6">
    @csrf
    <h1 class="text-xl font-semibold">Masuk</h1>
    <div>
        <label class="block text-sm font-medium" for="alamat_surat_elektronik">Alamat surat elektronik</label>
        <input id="alamat_surat_elektronik" name="alamat_surat_elektronik" type="email" required value="{{ old('alamat_surat_elektronik') }}" class="mt-1 w-full rounded border p-2">
        @error('alamat_surat_elektronik')<p class="text-sm text-red-600">{{ $message }}</p>@enderror
    </div>
    <div>
        <label class="block text-sm font-medium" for="kata_sandi">Kata sandi</label>
        <input id="kata_sandi" name="kata_sandi" type="password" required class="mt-1 w-full rounded border p-2">
    </div>
    <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="ingat_saya" value="1"> Ingat saya</label>
    <button class="w-full rounded bg-indigo-600 py-2 text-white">Masuk</button>
    <p class="text-center text-sm">Belum punya akun? <a class="text-indigo-600 underline" href="{{ route('daftar') }}">Daftar</a></p>
</form>
</body>
</html>
