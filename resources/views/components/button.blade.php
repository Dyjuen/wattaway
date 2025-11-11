@props(['type' => 'button', 'variant' => 'primary'])

@php
$baseClasses = 'px-4 py-2 rounded-lg transition-colors';

$variantClasses = [
    'primary' => 'bg-blue-500 hover:bg-blue-600 text-white',
    'secondary' => 'bg-white/10 hover:bg-white/20 text-white',
    'danger' => 'bg-red-500 hover:bg-red-600 text-white',
    'success' => 'bg-green-600 hover:bg-green-700 text-white',
];

$classes = $baseClasses . ' ' . $variantClasses[$variant];
@endphp

<button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</button>
