<form method="POST" action="{{ $aksi }}" class="max-w-xl space-y-4">
    @csrf
    @if (($metode ?? 'POST') === 'PUT')
        @method('PUT')
    @endif

    <div>
        <label class="block text-sm font-medium" for="alamat_surat_elektronik">Alamat surat elektronik</label>
        <input id="alamat_surat_elektronik" name="alamat_surat_elektronik" type="email" required
               value="{{ old('alamat_surat_elektronik', $akun->alamat_surat_elektronik ?? '') }}"
               class="mt-1 w-full rounded border p-2">
        @error('alamat_surat_elektronik')<p class="text-sm text-red-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="block text-sm font-medium" for="nama_tampilan">Nama tampilan (opsional)</label>
        <input id="nama_tampilan" name="nama_tampilan" type="text"
               value="{{ old('nama_tampilan', $akun->nama_tampilan ?? '') }}"
               class="mt-1 w-full rounded border p-2">
        @error('nama_tampilan')<p class="text-sm text-red-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="block text-sm font-medium" for="penyedia_surat_id">Penyedia surat</label>
        <select id="penyedia_surat_id" name="penyedia_surat_id" required class="mt-1 w-full rounded border p-2">
            @foreach ($daftarPenyedia as $penyedia)
                <option value="{{ $penyedia->id }}" @selected((int) old('penyedia_surat_id', $akun->penyedia_surat_id ?? 0) === $penyedia->id)>{{ $penyedia->nama }}</option>
            @endforeach
        </select>
        @error('penyedia_surat_id')<p class="text-sm text-red-600">{{ $message }}</p>@enderror
    </div>

    <p class="text-sm text-slate-500">Kata sandi tidak disimpan di sini. Akun dihubungkan lewat OAuth2/IMAP pada tahap integrasi.</p>

    <div class="flex gap-3">
        <button type="submit" class="rounded bg-indigo-600 px-4 py-2 text-white">Simpan</button>
        <a href="{{ route('akun-surat.index') }}" class="rounded border px-4 py-2">Batal</a>
    </div>
</form>
