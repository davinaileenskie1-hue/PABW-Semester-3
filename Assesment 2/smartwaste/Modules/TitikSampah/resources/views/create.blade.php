@extends('layouts.app')

@section('title', 'Tambah Fasilitas')

@section('content')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb small">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('TitikSampah.index') }}">TitikSampah</a></li>
            <li class="breadcrumb-item active">Tambah</li>
        </ol>
    </nav>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-transparent">
            <h5 class="mb-0 fw-semibold"><i class="bi bi-plus-circle me-1"></i>Tambah TitikSampah Baru</h5>
        </div>
        <div class="card-body">
            @include('TitikSampah::_form')
        </div>
    </div>
@endsection
