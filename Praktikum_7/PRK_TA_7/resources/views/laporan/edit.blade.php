<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Edit Laporan Banjir</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <form action="{{ route('laporan.update', $laporan) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT') {{-- Mengubah request menjadi PUT agar menuju method update() --}}

                    <div class="mb-4">
                        <x-input-label for="nama_pelapor" value="Nama Pelapor" />
                        <x-text-input id="nama_pelapor" name="nama_pelapor" type="text" class="mt-1 block w-full"
                                      :value="old('nama_pelapor', $laporan->nama_pelapor)" required />
                        <x-input-error :messages="$errors->get('nama_pelapor')" class="mt-2" />
                    </div>

                    <div class="mb-4">
                        <x-input-label for="no_hp" value="No. HP" />
                        <x-text-input id="no_hp" name="no_hp" type="text" class="mt-1 block w-full"
                                      :value="old('no_hp', $laporan->no_hp)" required />
                        <x-input-error :messages="$errors->get('no_hp')" class="mt-2" />
                    </div>

                    <div class="mb-4">
                        <x-input-label for="lokasi" value="Lokasi" />
                        <x-text-input id="lokasi" name="lokasi" type="text" class="mt-1 block w-full"
                                      :value="old('lokasi', $laporan->lokasi)" required />
                        <x-input-error :messages="$errors->get('lokasi')" class="mt-2" />
                    </div>

                    <div class="mb-4">
                        <x-input-label for="kecamatan" value="Kecamatan" />
                        <x-text-input id="kecamatan" name="kecamatan" type="text" class="mt-1 block w-full"
                                      :value="old('kecamatan', $laporan->kecamatan)" required />
                        <x-input-error :messages="$errors->get('kecamatan')" class="mt-2" />
                    </div>

                    <div class="mb-4">
                        <x-input-label for="tinggi_air" value="Tinggi Air (cm)" />
                        <x-text-input id="tinggi_air" name="tinggi_air" type="number" min="0" class="mt-1 block w-full"
                                      :value="old('tinggi_air', $laporan->tinggi_air)" required />
                        <x-input-error :messages="$errors->get('tinggi_air')" class="mt-2" />
                    </div>

                    <div class="mb-4">
                        <x-input-label for="keterangan" value="Keterangan (opsional)" />
                        <textarea id="keterangan" name="keterangan" rows="3"
                                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">{{ old('keterangan', $laporan->keterangan) }}</textarea>
                        <x-input-error :messages="$errors->get('keterangan')" class="mt-2" />
                    </div>

                    <div class="mb-4">
                        <x-input-label for="status" value="Status Penanganan" />
                        <select id="status" name="status"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                            @foreach (['Menunggu', 'Diproses', 'Selesai'] as $status)
                                <option value="{{ $status }}"
                                    {{ old('status', $laporan->status) === $status ? 'selected' : '' }}>
                                    {{ $status }}
                                </option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('status')" class="mt-2" />
                    </div>

                    <div class="mb-4">
                        <p class="text-sm text-gray-600">Foto saat ini:</p>
                        <img src="{{ asset('storage/' . $laporan->foto) }}" alt="Foto laporan {{ $laporan->nama_pelapor }}"
                             class="mt-2 h-32 w-32 object-cover rounded border">
                        <x-input-label for="foto" value="Ganti Foto (kosongkan jika tidak ingin mengganti)" class="mt-2" />
                        <input id="foto" name="foto" type="file" accept=".jpg,.jpeg,.png" class="mt-1 block w-full">
                        <x-input-error :messages="$errors->get('foto')" class="mt-2" />
                    </div>

                    <x-primary-button>Update</x-primary-button>
                    <a href="{{ route('laporan.index') }}" class="ms-3 text-gray-600">Batal</a>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
