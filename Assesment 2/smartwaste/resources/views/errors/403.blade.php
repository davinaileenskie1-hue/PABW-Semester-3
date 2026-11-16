@extends('layouts.app')

@section('title', '403 Forbidden')

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm text-center">
                <div class="card-body p-5">
                    <div class="display-1 text-danger mb-2"><i class="bi bi-shield-lock"></i></div>
                    <h2 class="fw-bold">403 &mdash; Forbidden</h2>
                    <p class="text-secondary mb-1">
                        {{ $message ?? 'Anda tidak memiliki hak akses ke halaman ini.' }}
                    </p>
                    <p class="text-secondary small mb-4">
                        Role Anda saat ini: <span class="badge text-bg-primary">{{ auth()->user()->role_label ?? '-' }}</span>
                    </p>
                    <div class="d-flex justify-content-center gap-2">
                        <a href="{{ route('dashboard') }}" class="btn btn-primary">
                            <i class="bi bi-speedometer2 me-1"></i>Kembali ke Dashboard
                        </a>
                        <a href="{{ url()->previous() }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left me-1"></i>Halaman Sebelumnya
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
