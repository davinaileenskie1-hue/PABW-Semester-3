<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <p class="text-lg font-medium">
                        Selamat datang, {{ Auth::user()->name }}.
                    </p>
                    <p class="mt-1 text-sm text-gray-600">
                        Anda berhasil login sebagai petugas BPBD Kabupaten Bandung.
                    </p>
                </div>
            </div>

            <div class="grid gap-6 sm:grid-cols-2">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <p class="text-sm font-medium text-gray-500">Total Laporan Banjir</p>
                        <p class="mt-2 text-3xl font-semibold text-gray-900">{{ $totalLaporan }}</p>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <p class="text-sm font-medium text-gray-500">Akses Cepat</p>
                        <div class="mt-3 flex flex-wrap gap-3">
                            <a href="{{ route('laporan.index') }}"
                               class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                Daftar Laporan
                            </a>
                            <a href="{{ route('laporan.create') }}"
                               class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
                                Buat Laporan
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900">Laporan Terbaru</h3>

                    @if ($laporanTerbaru->isEmpty())
                        <p class="mt-3 text-sm text-gray-600">Belum ada laporan banjir yang masuk.</p>
                    @else
                        <div class="mt-4 overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 text-sm">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Lokasi</th>
                                        <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Pelapor</th>
                                        <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Tinggi Genangan</th>
                                        <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 bg-white">
                                    @foreach ($laporanTerbaru as $laporan)
                                        <tr>
                                            <td class="px-4 py-3 text-gray-900">{{ $laporan->lokasi }}</td>
                                            <td class="px-4 py-3 text-gray-700">{{ $laporan->nama_pelapor }}</td>
                                            <td class="px-4 py-3 text-gray-700">{{ $laporan->tinggi_genangan }} cm</td>
                                            <td class="px-4 py-3 text-gray-700">
                                                {{ \Illuminate\Support\Carbon::parse($laporan->tanggal_kejadian)->format('d-m-Y') }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
