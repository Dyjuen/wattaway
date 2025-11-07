@props(['type' => 'success', 'message'])

@php
$typeClasses = [
    'success' => 'bg-green-500/20 text-green-300 border-green-500/30',
    'error' => 'bg-red-500/20 text-red-300 border-red-500/30',
];

$classes = 'p-4 rounded-lg mb-6 border ' . $typeClasses[$type];
@endphp

<div class="{{ $classes }}">
    {{ $message }}
</div>
