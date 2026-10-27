<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Daftar Laporan Banjir</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                {{-- Pesan berhasil dari controller --}}
                @if (session('success'))
                    <div class="mb-4 p-3 rounded bg-green-100 text-green-700">{{ session('success') }}</div>
                @endif

                <table class="w-full border text-left text-sm">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="border p-2">No</th>
                            <th class="border p-2">Foto</th>
                            <th class="border p-2">Nama Pelapor</th>
                            <th class="border p-2">Lokasi</th>
                            <th class="border p-2">Kecamatan</th>
                            <th class="border p-2">Tinggi Air</th>
                            <th class="border p-2">Status</th>
                            <th class="border p-2">Tanggal</th>
                            <th class="border p-2">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($laporan as $item)
                            <tr>
                                <td class="border p-2">{{ $laporan->firstItem() + $loop->index }}</td>
                                <td class="border p-2">
                                    {{-- Thumbnail foto bukti --}}
                                    <img src="{{ asset('storage/' . $item->foto) }}"
                                         alt="Foto laporan {{ $item->nama_pelapor }}"
                                         class="h-16 w-16 object-cover rounded border">
                                </td>
                                <td class="border p-2">{{ $item->nama_pelapor }}</td>
                                <td class="border p-2">{{ $item->lokasi }}</td>
                                <td class="border p-2">{{ $item->kecamatan }}</td>
                                <td class="border p-2">{{ $item->tinggi_air }} cm</td>
                                <td class="border p-2">
                                    @if ($item->status === 'Menunggu')
                                        <span class="px-2 py-1 rounded bg-yellow-100 text-yellow-700">{{ $item->status }}</span>
                                    @elseif ($item->status === 'Diproses')
                                        <span class="px-2 py-1 rounded bg-blue-100 text-blue-700">{{ $item->status }}</span>
                                    @else
                                        <span class="px-2 py-1 rounded bg-green-100 text-green-700">{{ $item->status }}</span>
                                    @endif
                                </td>
                                <td class="border p-2">{{ $item->created_at->format('d-m-Y H:i') }}</td>
                                <td class="border p-2 whitespace-nowrap">
                                    <a href="{{ route('laporan.show', $item) }}" class="text-sky-600">Lihat</a> |
                                    <a href="{{ route('laporan.edit', $item) }}" class="text-yellow-600">Edit</a> |
                                    <form action="{{ route('laporan.destroy', $item) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600"
                                                onclick="return confirm('Yakin ingin menghapus laporan ini?')">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="border p-2 text-center">Belum ada laporan yang dikirim.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="mt-4">{{ $laporan->links() }}</div>
            </div>
        </div>
    </div>
</x-app-layout>
