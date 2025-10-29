@extends('layouts.admin')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto bg-white dark:bg-gray-800 shadow-md rounded-lg p-6">
        <h1 class="text-3xl font-bold mb-6">Device Details</h1>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
            <div>
                <h3 class="text-xl font-semibold mb-2">Device Details</h3>
                <p class="text-gray-600 dark:text-gray-400"><strong>Name:</strong> {{ $device->name }}</p>
                <p class="text-gray-600 dark:text-gray-400"><strong>Serial Number:</strong> {{ $device->serial_number }}</p>
                <p class="text-gray-600 dark:text-gray-400"><strong>Hardware ID:</strong> {{ $device->hardware_id }}</p>
                @if ($device->provisioningToken)
                    <p class="text-gray-600 dark:text-gray-400"><strong>Provisioning Token:</strong> {{ $device->provisioningToken->token }}</p>
                @endif
            </div>
            <div class="flex flex-col items-center justify-center">
                @if ($qrCodeDataUri)
                    <h3 class="text-xl font-semibold mb-2">Pairing QR Code</h3>
                    <img src="{{ $qrCodeDataUri }}" alt="QR Code" class="w-48 h-48 border border-gray-300 p-2 rounded-lg">
                    <a href="{{ $qrCodeDataUri }}" download="{{ $device->serial_number }}_qr.png" class="mt-4 px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600">Download QR Code</a>
                @endif
            </div>
        </div>

        <div>
            <h3 class="text-xl font-semibold mb-2">Relay Controls</h3>
            <div class="grid grid-cols-3 gap-4">
                <div>
                    <span class="text-gray-600 dark:text-gray-400">Relay 1:</span>
                    <button class="relay-btn px-4 py-2 bg-green-500 text-white rounded-md hover:bg-green-600" data-channel="1" data-state="on">On</button>
                    <button class="relay-btn px-4 py-2 bg-red-500 text-white rounded-md hover:bg-red-600" data-channel="1" data-state="off">Off</button>
                </div>
                <div>
                    <span class="text-gray-600 dark:text-gray-400">Relay 2:</span>
                    <button class="relay-btn px-4 py-2 bg-green-500 text-white rounded-md hover:bg-green-600" data-channel="2" data-state="on">On</button>
                    <button class="relay-btn px-4 py-2 bg-red-500 text-white rounded-md hover:bg-red-600" data-channel="2" data-state="off">Off</button>
                </div>
                <div>
                    <span class="text-gray-600 dark:text-gray-400">Relay 3:</span>
                    <button class="relay-btn px-4 py-2 bg-green-500 text-white rounded-md hover:bg-green-600" data-channel="3" data-state="on">On</button>
                    <button class="relay-btn px-4 py-2 bg-red-500 text-white rounded-md hover:bg-red-600" data-channel="3" data-state="off">Off</button>
                </div>
            </div>
        </div>

        <div class="mt-6 flex justify-end">
            <a href="{{ route('admin.devices.create') }}" class="px-4 py-2 bg-gray-300 text-gray-800 rounded-md hover:bg-gray-400 mr-2">Create Another Device</a>
            <a href="{{ route('admin.provisioning-tokens.index') }}" class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600">View All Tokens</a>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const apiToken = document.querySelector('meta[name="api-token"]').getAttribute('content');
    const deviceId = '{{ $device->id }}';

    document.querySelectorAll('.relay-btn').forEach(button => {
        button.addEventListener('click', function () {
            const channel = this.dataset.channel;
            const state = this.dataset.state;

            fetch(`/api/v1/devices/${deviceId}/relay`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${apiToken}`,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    channel: channel,
                    state: state
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Relay command sent successfully!');
                } else {
                    alert('Failed to send relay command: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while sending the relay command.');
            });
        });
    });
});
</script>
@endpush
