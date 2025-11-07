@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <x-glass-card>
        <h1 class="text-3xl font-bold mb-6">{{ $device->name }}</h1>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
            <div>
                <h3 class="text-xl font-semibold mb-2">Device Details</h3>
                <p class="text-gray-600 dark:text-gray-400"><strong>Serial Number:</strong> {{ $device->serial_number }}</p>
                <p class="text-gray-600 dark:text-gray-400"><strong>Hardware ID:</strong> {{ $device->hardware_id }}</p>
                <p class="text-gray-600 dark:text-gray-400"><strong>Status:</strong> {{ ucfirst($device->status) }}</p>
                <p class="text-gray-600 dark:text-gray-400"><strong>Last Seen:</strong> {{ $device->last_seen_at?->diffForHumans() ?? 'Never' }}</p>
                <p class="text-gray-600 dark:text-gray-400"><strong>Activated At:</strong> {{ $device->activated_at?->format('Y-m-d H:i') ?? 'Not Activated' }}</p>
            </div>
            @if ($device->provisioningToken)
            <div class="flex flex-col items-center justify-center">
                <h3 class="text-xl font-semibold mb-2">Pairing QR Code</h3>
                <img src="{{ route('admin.provisioning-tokens.qr', $device->provisioningToken) }}" alt="QR Code for {{ $device->provisioningToken->token }}" class="w-48 h-48 border border-gray-300 p-2 rounded-lg">
                <x-button tag="a" href="{{ route('admin.provisioning-tokens.qr', $device->provisioningToken) }}" download="{{ $device->serial_number }}_qr.png" class="mt-4">Download QR Code</x-button>
            </div>
            @endif
        </div>

        <div class="mt-6 flex justify-end">
            <x-button tag="a" href="{{ route('dashboard') }}" variant="secondary">Back to Dashboard</x-button>
        </div>
    </x-glass-card>
</div>
@endsection
