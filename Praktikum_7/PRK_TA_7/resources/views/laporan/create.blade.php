<x-guest-layout>
    <h2 class="font-semibold text-xl text-gray-800 leading-tight mb-4">Form Pelaporan Banjir</h2>

    {{-- Pesan berhasil dari controller --}}
    @if (session('success'))
        <div class="mb-4 p-3 rounded bg-green-100 text-green-700">{{ session('success') }}</div>
    @endif

    {{-- enctype wajib ada agar foto ikut terkirim --}}
    <form action="{{ route('laporan.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="mb-4">
            <x-input-label for="nama_pelapor" value="Nama Pelapor" />
            <x-text-input id="nama_pelapor" name="nama_pelapor" type="text" class="mt-1 block w-full"
                          :value="old('nama_pelapor')" required autofocus />
            <x-input-error :messages="$errors->get('nama_pelapor')" class="mt-2" />
        </div>

        <div class="mb-4">
            <x-input-label for="no_hp" value="No. HP" />
            <x-text-input id="no_hp" name="no_hp" type="text" class="mt-1 block w-full"
                          :value="old('no_hp')" required />
            <x-input-error :messages="$errors->get('no_hp')" class="mt-2" />
        </div>

        <div class="mb-4">
            <x-input-label for="lokasi" value="Lokasi" />
            <x-text-input id="lokasi" name="lokasi" type="text" class="mt-1 block w-full"
                          :value="old('lokasi')" required />
            <x-input-error :messages="$errors->get('lokasi')" class="mt-2" />
        </div>

        <div class="mb-4">
            <x-input-label for="kecamatan" value="Kecamatan" />
            <x-text-input id="kecamatan" name="kecamatan" type="text" class="mt-1 block w-full"
                          :value="old('kecamatan')" required />
            <x-input-error :messages="$errors->get('kecamatan')" class="mt-2" />
        </div>

        <div class="mb-4">
            <x-input-label for="tinggi_air" value="Tinggi Air (cm)" />
            <x-text-input id="tinggi_air" name="tinggi_air" type="number" min="0" class="mt-1 block w-full"
                          :value="old('tinggi_air')" required />
            <x-input-error :messages="$errors->get('tinggi_air')" class="mt-2" />
        </div>

        <div class="mb-4">
            <x-input-label for="keterangan" value="Keterangan (opsional)" />
            <textarea id="keterangan" name="keterangan" rows="3"
                      class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">{{ old('keterangan') }}</textarea>
            <x-input-error :messages="$errors->get('keterangan')" class="mt-2" />
        </div>

        <div class="mb-4">
            <x-input-label for="foto" value="Foto Bukti (jpg, jpeg, png - maks. 2 MB)" />
            <input id="foto" name="foto" type="file" accept=".jpg,.jpeg,.png" class="mt-1 block w-full" required>
            <x-input-error :messages="$errors->get('foto')" class="mt-2" />
        </div>

        <x-primary-button>Kirim Laporan</x-primary-button>
    </form>
</x-guest-layout>
