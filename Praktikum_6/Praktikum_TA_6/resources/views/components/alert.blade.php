@props(['type' => 'success', 'message' => ''])

@php
    $classes = match ($type) {
        'error' => 'border-red-200 bg-red-50 text-red-800',
        'warning' => 'border-yellow-200 bg-yellow-50 text-yellow-800',
        'info' => 'border-blue-200 bg-blue-50 text-blue-800',
        default => 'border-green-200 bg-green-50 text-green-800',
    };
@endphp

<div {{ $attributes->merge(['class' => "rounded-md border p-4 text-sm $classes"]) }}>
    <strong class="font-semibold">{{ $type === 'success' ? 'Berhasil' : ucfirst($type) }}:</strong> {{ $message }}
</div>
