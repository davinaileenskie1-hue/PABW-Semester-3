@extends('layouts.app')

@section('title', 'Konfirmasi Laporan')

@section('content')
    <div class="card">
        <h2>Konfirmasi Laporan</h2>

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

        <table class="detail-table">
            <tr>
                <th>Nama Pelapor</th>
                <td>{{ $laporan->nama_pelapor }}</td>
            </tr>
            <tr>
                <th>Lokasi Kejadian</th>
                <td>{{ $laporan->lokasi }}</td>
            </tr>
            <tr>
                <th>Tinggi Genangan Air</th>
                <td>{{ $tinggi }} cm</td>
            </tr>
            <tr>
                <th>Tanggal Kejadian</th>
                <td>{{ \Illuminate\Support\Carbon::parse($laporan->tanggal_kejadian)->format('d-m-Y') }}</td>
            </tr>
            <tr>
                <th>Status Genangan</th>
                <td>{{ $status }}</td>
            </tr>
        </table>
    </div>

    <a href="{{ route('laporan.index') }}">&larr; Lihat Daftar Laporan</a>
@endsection
