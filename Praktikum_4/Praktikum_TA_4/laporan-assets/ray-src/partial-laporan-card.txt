@php
    $tinggi = $laporan->tinggi_genangan;

    // Menentukan status genangan berdasarkan tinggi air (cm).
    if ($tinggi < 30) {
        $status = 'Waspada';
        $badge = 'badge-waspada';
    } elseif ($tinggi <= 70) {
        $status = 'Siaga';
        $badge = 'badge-siaga';
    } else {
        $status = 'Awas';
        $badge = 'badge-awas';
    }
@endphp

<div class="card">
    <h2>
        {{ $laporan->lokasi }}
        <span class="badge {{ $badge }}">{{ $status }}</span>
    </h2>
    <p><strong>Pelapor:</strong> {{ $laporan->nama_pelapor }}</p>
    <p><strong>Tinggi Genangan:</strong> {{ $tinggi }} cm</p>
    <p><strong>Tanggal Kejadian:</strong> {{ \Illuminate\Support\Carbon::parse($laporan->tanggal_kejadian)->format('d-m-Y') }}</p>
    <p><strong>Dilaporkan:</strong> {{ $laporan->created_at->format('d-m-Y H:i') }}</p>
</div>
