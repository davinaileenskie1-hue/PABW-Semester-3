@extends('layouts.app')
@section('title', 'Detail Kontak')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">Detail Kontak</div>
            <div class="card-body text-center">
                @if ($kontak->foto)
                    <img src="{{ asset('storage/' . $kontak->foto) }}" alt="{{ $kontak->nama }}" width="180" class="rounded mb-3">
                @endif

                <h5 class="card-title">{{ $kontak->nama }}</h5>
                <p class="card-text"><strong>Alamat:</strong> {{ $kontak->alamat }}</p>
                <p class="card-text"><strong>No HP:</strong> {{ $kontak->no_hp }}</p>
                <p class="card-text"><small class="text-muted">Ditambahkan {{ $kontak->created_at->diffForHumans() }}</small></p>

                <div class="mt-4">
                    <a href="{{ route('kontaks.edit', $kontak) }}" class="btn btn-warning">Edit</a>
                    
                    <form action="{{ route('kontaks.destroy', $kontak) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus?')">Hapus</button>
                    </form>

                    <a href="{{ route('kontaks.index') }}" class="btn btn-secondary">Kembali</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection