@extends('layouts.app')

@section('title', 'Edit TitikSampah')

@section('content')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb small">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('TitikSampah.index') }}">TitikSampah</a></li>
            <li class="breadcrumb-item active">Edit</li>
        </ol>
    </nav>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-semibold"><i class="bi bi-pencil-square me-1"></i>Edit Fasilitas</h5>
            <span class="badge text-bg-light border">ID #{{ $TitikSampah->id }}</span>
        </div>
        <div class="card-body">
            @include('TitikSampah::_form')
        </div>
    </div>
@endsection
