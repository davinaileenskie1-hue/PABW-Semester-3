<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Konfirmasi Laporan') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <x-alert type="success" message="Laporan berhasil disimpan ke database. Terima kasih telah melaporkan!" />

                    @php
                        $tinggi = $laporan->tinggi_genangan;

                        // Menentukan status genangan berdasarkan tinggi air (cm).
                        if ($tinggi < 30) {
                            $status = 'Waspada';
                        } elseif ($tinggi <= 70) {
                            $status = 'Siaga';
                        } else {
                            $status = 'Awas';
                        }
                    @endphp

                    <dl class="mt-4 divide-y divide-gray-200 text-sm">
                        <div class="flex justify-between gap-4 py-3">
                            <dt class="font-medium text-gray-500">Nama Pelapor</dt>
                            <dd class="text-right text-gray-900">{{ $laporan->nama_pelapor }}</dd>
                        </div>
                        <div class="flex justify-between gap-4 py-3">
                            <dt class="font-medium text-gray-500">Lokasi Kejadian</dt>
                            <dd class="text-right text-gray-900">{{ $laporan->lokasi }}</dd>
                        </div>
                        <div class="flex justify-between gap-4 py-3">
                            <dt class="font-medium text-gray-500">Tinggi Genangan Air</dt>
                            <dd class="text-right text-gray-900">{{ $tinggi }} cm</dd>
                        </div>
                        <div class="flex justify-between gap-4 py-3">
                            <dt class="font-medium text-gray-500">Tanggal Kejadian</dt>
                            <dd class="text-right text-gray-900">
                                {{ \Illuminate\Support\Carbon::parse($laporan->tanggal_kejadian)->format('d-m-Y') }}
                            </dd>
                        </div>
                        <div class="flex justify-between gap-4 py-3">
                            <dt class="font-medium text-gray-500">Status Genangan</dt>
                            <dd class="text-right text-gray-900">{{ $status }}</dd>
                        </div>
                    </dl>

                    <div class="mt-6 flex flex-wrap items-center justify-between gap-4">
                        <a href="{{ route('laporan.create') }}" class="text-sm text-gray-600 underline hover:text-gray-900">
                            &larr; Kirim laporan lain
                        </a>

                        @auth
                            <a href="{{ route('laporan.index') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-800">
                                Lihat Daftar Laporan
                            </a>
                        @endauth
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
