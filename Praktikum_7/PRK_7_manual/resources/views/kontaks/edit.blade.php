@extends('layouts.app')
@section('title', 'Edit Kontak')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">Edit Kontak</div>
            <div class="card-body">
                <form action="{{ route('kontaks.update', $kontak) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="nama" class="form-label">Nama</label>
                        <input type="text" id="nama" name="nama" value="{{ old('nama', $kontak->nama) }}" class="form-control @error('nama') is-invalid @enderror" required>
                        @error('nama') 
                            <div class="invalid-feedback">{{ $message }}</div> 
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="alamat" class="form-label">Alamat</label>
                        <textarea id="alamat" name="alamat" rows="3" class="form-control @error('alamat') is-invalid @enderror" required>{{ old('alamat', $kontak->alamat) }}</textarea>
                        @error('alamat') 
                            <div class="invalid-feedback">{{ $message }}</div> 
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="no_hp" class="form-label">No HP</label>
                        <input type="text" id="no_hp" name="no_hp" value="{{ old('no_hp', $kontak->no_hp) }}" class="form-control @error('no_hp') is-invalid @enderror" required>
                        @error('no_hp') 
                            <div class="invalid-feedback">{{ $message }}</div> 
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label d-block">Foto Saat Ini</label>
                        @if ($kontak->foto)
                            <img src="{{ asset('storage/' . $kontak->foto) }}" alt="{{ $kontak->nama }}" width="120" class="rounded mb-2">
                        @else
                            <p class="text-muted">Belum ada foto.</p>
                        @endif

                        <input type="file" id="foto" name="foto" accept="image/*" class="form-control @error('foto') is-invalid @enderror">
                        <small class="text-muted">Kosongkan jika tidak ingin mengganti foto.</small>
                        @error('foto') 
                            <div class="invalid-feedback">{{ $message }}</div> 
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-primary">Update</button>
                    <a href="{{ route('kontaks.index') }}" class="btn btn-secondary">Batal</a>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection