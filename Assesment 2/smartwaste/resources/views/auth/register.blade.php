@extends('layouts.guest')

@section('title', 'Registrasi')
@section('heading', 'Buat Akun SmartWaste')
@section('subheading', 'Registrasi otomatis mendapat role <strong>Warga</strong>')

@section('content')
    @if ($errors->any())
        <div class="alert alert-danger py-2 small">
            <ul class="mb-0 ps-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('register.store') }}" method="POST" enctype="multipart/form-data" novalidate>
        @csrf

        <div class="mb-3">
            <label for="name" class="form-label">Nama Lengkap</label>
            <input type="text" name="name" id="name" value="{{ old('name') }}"
                   class="form-control @error('name') is-invalid @enderror" required autofocus>
            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" name="email" id="email" value="{{ old('email') }}"
                   class="form-control @error('email') is-invalid @enderror" required>
            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="row g-3">
            <div class="col-md-6">
                <label for="password" class="form-label">Password</label>
                <input type="password" name="password" id="password"
                       class="form-control @error('password') is-invalid @enderror" required>
                @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
                <label for="password_confirmation" class="form-label">Ulangi Password</label>
                <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" required>
            </div>
        </div>

        <div class="mt-3 mb-4">
            <label for="foto" class="form-label">Foto Profil <span class="text-secondary small">(opsional)</span></label>
            <input type="file" name="foto" id="foto" accept="image/*"
                   class="form-control @error('foto') is-invalid @enderror">
            @error('foto')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <button class="btn btn-primary w-100 py-2" type="submit">
            <i class="bi bi-person-plus me-1"></i>Daftar
        </button>
    </form>

    <p class="text-center small mt-4 mb-0">
        Sudah punya akun? <a href="{{ route('login') }}">Masuk di sini</a>
    </p>
@endsection
