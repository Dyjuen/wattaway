@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-6">Style Guide</h1>

    <!-- Alerts -->
    <h2 class="text-2xl font-bold mb-4">Alerts</h2>
    <x-alert type="success" message="This is a success message." />
    <x-alert type="error" message="This is an error message." />

    <!-- Buttons -->
    <h2 class="text-2xl font-bold mt-8 mb-4">Buttons</h2>
    <div class="flex space-x-4">
        <x-button>Primary</x-button>
        <x-button variant="secondary">Secondary</x-button>
        <x-button variant="danger">Danger</x-button>
    </div>

    <!-- Cards -->
    <h2 class="text-2xl font-bold mt-8 mb-4">Cards</h2>
    <x-glass-card class="mb-4">
        This is a glass card.
    </x-glass-card>

    <!-- Device Card -->
    <h2 class="text-2xl font-bold mt-8 mb-4">Device Card</h2>
    @php
        $device = new \App\Models\Device([
            'name' => 'Living Room Socket',
            'description' => 'Smart socket for the living room lamp',
            'status' => 'online',
            'last_seen_at' => now(),
        ]);
    @endphp
    <x-device-card :device="$device" />

    <!-- Inputs -->
    <h2 class="text-2xl font-bold mt-8 mb-4">Inputs</h2>
    <div class="space-y-4">
        <x-input type="text" name="text" id="text" placeholder="Text input" />
        <x-input type="email" name="email" id="email" placeholder="Email input" />
        <x-input type="password" name="password" id="password" placeholder="Password input" />
    </div>

    <!-- Page Header -->
    <h2 class="text-2xl font-bold mt-8 mb-4">Page Header</h2>
    <x-page-header title="Page Title" subtitle="This is a subtitle for the page." />

    <!-- Stat Card -->
    <h2 class="text-2xl font-bold mt-8 mb-4">Stat Card</h2>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <x-stat-card title="Total Devices" value="3" change="+2">
            <x-slot name="icon">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
            </x-slot>
        </x-stat-card>
    </div>
</div>
@endsection
