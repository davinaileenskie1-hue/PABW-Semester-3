@props(['type' => 'success', 'message' => ''])

@php
    $color = match ($type) {
        'error' => '#dc3545',
        'warning' => '#f0ad4e',
        'info' => '#0d6efd',
        default => '#198754',
    };
@endphp

<div class="alert" style="border-left: 5px solid {{ $color }};">
    <strong>{{ ucfirst($type) }}:</strong> {{ $message }}
</div>
