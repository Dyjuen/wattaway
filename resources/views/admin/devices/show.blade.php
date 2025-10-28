@extends('layouts.admin')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto bg-white dark:bg-gray-800 shadow-md rounded-lg p-6">
        <h1 class="text-3xl font-bold mb-6">Device Created Successfully</h1>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
            <div>
                <h3 class="text-xl font-semibold mb-2">Device Details</h3>
                <p class="text-gray-600 dark:text-gray-400"><strong>Name:</strong> {{ $device->name }}</p>
                <p class="text-gray-600 dark:text-gray-400"><strong>Serial Number:</strong> {{ $device->serial_number }}</p>
                <p class="text-gray-600 dark:text-gray-400"><strong>Hardware ID:</strong> {{ $device->hardware_id }}</p>
                <p class="text-gray-600 dark:text-gray-400"><strong>Provisioning Token:</strong> {{ $device->provisioningToken->token }}</p>
            </div>
            <div class="flex flex-col items-center justify-center">
                <h3 class="text-xl font-semibold mb-2">Pairing QR Code</h3>
                <img src="{{ $qrCodeDataUri }}" alt="QR Code for {{ $device->provisioningToken->token }}" class="w-48 h-48 border border-gray-300 p-2 rounded-lg">
                <a href="{{ $qrCodeDataUri }}" download="{{ $device->serial_number }}_qr.png" class="mt-4 px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600">Download QR Code</a>
            </div>
        </div>

        <div class="mt-6 flex justify-end">
            <a href="{{ route('admin.devices.create') }}" class="px-4 py-2 bg-gray-300 text-gray-800 rounded-md hover:bg-gray-400 mr-2">Create Another Device</a>
            <a href="{{ route('admin.provisioning-tokens.index') }}" class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600">View All Tokens</a>
        </div>
    </div>
</div>
@endsection
