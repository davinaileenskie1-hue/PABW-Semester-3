@php
    $tinggi = $laporan->tinggi_genangan;

    // Menentukan status genangan berdasarkan tinggi air (cm).
    if ($tinggi < 30) {
        $status = 'Waspada';
        $badge = 'bg-yellow-100 text-yellow-800';
    } elseif ($tinggi <= 70) {
        $status = 'Siaga';
        $badge = 'bg-orange-100 text-orange-800';
    } else {
        $status = 'Awas';
        $badge = 'bg-red-100 text-red-800';
    }
@endphp

<div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
    <div class="p-6">
        <div class="flex items-start justify-between gap-4">
            <h3 class="text-lg font-semibold text-gray-900">{{ $laporan->lokasi }}</h3>
            <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $badge }}">
                {{ $status }}
            </span>
        </div>

        <dl class="mt-4 grid gap-2 text-sm sm:grid-cols-2">
            <div>
                <dt class="font-medium text-gray-500">Pelapor</dt>
                <dd class="text-gray-900">{{ $laporan->nama_pelapor }}</dd>
            </div>
            <div>
                <dt class="font-medium text-gray-500">Tinggi Genangan</dt>
                <dd class="text-gray-900">{{ $tinggi }} cm</dd>
            </div>
            <div>
                <dt class="font-medium text-gray-500">Tanggal Kejadian</dt>
                <dd class="text-gray-900">
                    {{ \Illuminate\Support\Carbon::parse($laporan->tanggal_kejadian)->format('d-m-Y') }}
                </dd>
            </div>
            <div>
                <dt class="font-medium text-gray-500">Dilaporkan</dt>
                <dd class="text-gray-900">{{ $laporan->created_at->format('d-m-Y H:i') }}</dd>
            </div>
        </dl>
    </div>
</div>
