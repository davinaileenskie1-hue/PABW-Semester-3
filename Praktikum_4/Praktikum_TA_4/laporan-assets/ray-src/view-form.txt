@extends('layouts.app')

@section('title', 'Buat Laporan')

@section('content')
    <div class="card">
        <h2>Form Pelaporan Banjir</h2>

        <x-alert type="info" message="Isi data di bawah ini sesuai kondisi banjir di wilayah Anda." />

        <form action="{{ route('laporan.store') }}" method="POST">
            @csrf

            <label for="nama_pelapor">Nama Pelapor</label>
            <input type="text" id="nama_pelapor" name="nama_pelapor" value="{{ old('nama_pelapor') }}" placeholder="Contoh: Ahmad Fauzi">
            @error('nama_pelapor')
                <div class="error-text">{{ $message }}</div>
            @enderror

            <label for="lokasi">Lokasi Kejadian</label>
            <input type="text" id="lokasi" name="lokasi" value="{{ old('lokasi') }}" placeholder="Contoh: Kp. Cieunteung, Kec. Baleendah">
            @error('lokasi')
                <div class="error-text">{{ $message }}</div>
            @enderror

            <label for="tinggi_genangan">Tinggi Genangan Air (cm)</label>
            <input type="number" id="tinggi_genangan" name="tinggi_genangan" value="{{ old('tinggi_genangan') }}" min="0" placeholder="Contoh: 45">
            @error('tinggi_genangan')
                <div class="error-text">{{ $message }}</div>
            @enderror

            <label for="tanggal_kejadian">Tanggal Kejadian</label>
            <input type="date" id="tanggal_kejadian" name="tanggal_kejadian" value="{{ old('tanggal_kejadian', date('Y-m-d')) }}">
            @error('tanggal_kejadian')
                <div class="error-text">{{ $message }}</div>
            @enderror

            <button type="submit">Kirim Laporan</button>
        </form>
    </div>
@endsection
