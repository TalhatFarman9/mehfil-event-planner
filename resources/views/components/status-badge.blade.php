@props(['status'])

@php
    $colors = [
        'pending' => 'bg-yellow-100 text-yellow-800',
        'confirmed' => 'bg-green-100 text-green-800',
        'cancelled' => 'bg-red-100 text-red-800',
    ];
@endphp

<span {{ $attributes->merge(['class' => 'px-2 py-1 text-xs font-semibold rounded-full '.($colors[$status] ?? 'bg-gray-100 text-gray-800')]) }}>
    {{ ucfirst($status) }}
</span>