<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Form Pelaporan Banjir') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <p class="text-sm text-gray-600">
                        Warga dapat mengirim laporan tanpa harus login. Isi data di bawah ini sesuai kondisi banjir di wilayah Anda.
                    </p>

                    <form action="{{ route('laporan.store') }}" method="POST" class="mt-6 space-y-4">
                        @csrf

                        <div>
                            <x-input-label for="nama_pelapor" :value="__('Nama Pelapor')" />
                            <x-text-input id="nama_pelapor" class="block mt-1 w-full" type="text" name="nama_pelapor"
                                          :value="old('nama_pelapor')" placeholder="Contoh: Ahmad Fauzi" required autofocus />
                            <x-input-error :messages="$errors->get('nama_pelapor')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="lokasi" :value="__('Lokasi Kejadian')" />
                            <x-text-input id="lokasi" class="block mt-1 w-full" type="text" name="lokasi"
                                          :value="old('lokasi')" placeholder="Contoh: Kp. Cieunteung, Kec. Baleendah" required />
                            <x-input-error :messages="$errors->get('lokasi')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="tinggi_genangan" :value="__('Tinggi Genangan Air (cm)')" />
                            <x-text-input id="tinggi_genangan" class="block mt-1 w-full" type="number" name="tinggi_genangan"
                                          :value="old('tinggi_genangan')" min="0" placeholder="Contoh: 45" required />
                            <x-input-error :messages="$errors->get('tinggi_genangan')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="tanggal_kejadian" :value="__('Tanggal Kejadian')" />
                            <x-text-input id="tanggal_kejadian" class="block mt-1 w-full" type="date" name="tanggal_kejadian"
                                          :value="old('tanggal_kejadian', date('Y-m-d'))" required />
                            <x-input-error :messages="$errors->get('tanggal_kejadian')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-between pt-2">
                            <p class="text-sm text-gray-500">
                                Tidak perlu login untuk mengirim laporan.
                            </p>

                            <x-primary-button>
                                {{ __('Kirim Laporan') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
