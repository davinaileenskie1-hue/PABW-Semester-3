<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Daftar Laporan Banjir') }}
            </h2>

            <a href="{{ route('laporan.create') }}"
               class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                Buat Laporan
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <p class="text-sm text-gray-600">
                        Halaman ini hanya dapat diakses oleh petugas BPBD yang sudah login.
                        Total laporan tersimpan: <strong>{{ $laporans->count() }}</strong>.
                    </p>
                </div>
            </div>

            @forelse ($laporans as $laporan)
                @include('partials.laporan-card', ['laporan' => $laporan])
            @empty
                <x-alert type="warning" message="Belum ada laporan banjir yang masuk." />
            @endforelse
        </div>
    </div>
</x-app-layout>
