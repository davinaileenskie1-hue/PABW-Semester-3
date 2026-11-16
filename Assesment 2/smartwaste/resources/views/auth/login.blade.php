@extends('layouts.guest')

@section('title', 'Masuk')
@section('heading', 'Masuk ke smartwaste')
@section('subheading', 'Sistem Informasi smartwaste')

@section('content')
    @if ($errors->any())
        <div class="alert alert-danger py-2 small">
            <i class="bi bi-exclamation-circle me-1"></i>{{ $errors->first() }}
        </div>
    @endif

    <form action="{{ route('login.store') }}" method="POST" novalidate>
        @csrf

        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                <input type="email" name="email" id="email" value="{{ old('email') }}"
                       class="form-control @error('email') is-invalid @enderror"
                       placeholder="nama@email.com" required autofocus>
                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-lock"></i></span>
                <input type="password" name="password" id="password"
                       class="form-control @error('password') is-invalid @enderror"
                       placeholder="Masukkan password" required>
                <button class="btn btn-outline-secondary" type="button" id="toggle-password">
                    <i class="bi bi-eye"></i>
                </button>
                @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>

        <div class="form-check mb-4">
            <input class="form-check-input" type="checkbox" name="remember" id="remember">
            <label class="form-check-label small" for="remember">Ingat saya</label>
        </div>

        <button class="btn btn-primary w-100 py-2" type="submit">
            <i class="bi bi-box-arrow-in-right me-1"></i>Masuk
        </button>
    </form>

    <p class="text-center small mt-4 mb-3">
        Belum punya akun? <a href="{{ route('register') }}">Daftar sebagai mahasiswa</a>
    </p>

    <div class="border rounded-3 p-3 bg-body-tertiary small">
        <div class="fw-semibold mb-2"><i class="bi bi-info-circle me-1"></i>Akun demo (password: <code>password</code>)</div>
        <div class="d-flex flex-wrap gap-2">
            <span class="badge text-bg-warning">petugas@campuscare.test</span>
            <span class="badge text-bg-success">mahasiswa@campuscare.test</span>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.getElementById('toggle-password').addEventListener('click', function () {
        const input = document.getElementById('password');
        const icon = this.querySelector('i');
        const show = input.type === 'password';
        input.type = show ? 'text' : 'password';
        icon.className = show ? 'bi bi-eye-slash' : 'bi bi-eye';
    });
</script>
@endpush
