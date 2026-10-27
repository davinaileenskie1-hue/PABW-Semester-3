<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Detail Laporan Banjir</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6 space-y-2">
                <p><strong>Nama Pelapor:</strong> {{ $laporan->nama_pelapor }}</p>
                <p><strong>No. HP:</strong> {{ $laporan->no_hp }}</p>
                <p><strong>Lokasi:</strong> {{ $laporan->lokasi }}</p>
                <p><strong>Kecamatan:</strong> {{ $laporan->kecamatan }}</p>
                <p><strong>Tinggi Air:</strong> {{ $laporan->tinggi_air }} cm</p>
                <p><strong>Keterangan:</strong> {{ $laporan->keterangan ?? '-' }}</p>
                <p><strong>Status:</strong> {{ $laporan->status }}</p>
                <p><strong>Tanggal Laporan:</strong> {{ $laporan->created_at->format('d-m-Y H:i') }}</p>

                {{-- Foto bukti kondisi banjir --}}
                <img src="{{ asset('storage/' . $laporan->foto) }}" alt="Foto laporan {{ $laporan->nama_pelapor }}"
                     class="max-w-sm rounded border">

                <div class="pt-4">
                    <a href="{{ route('laporan.edit', $laporan) }}" class="text-yellow-600">Edit</a> |
                    <a href="{{ route('laporan.index') }}" class="text-gray-600">Kembali</a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
