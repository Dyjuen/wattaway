@extends('layouts.admin')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto bg-white dark:bg-gray-800 shadow-md rounded-lg p-6">
        <h1 class="text-3xl font-bold mb-6">Token Details: {{ $token->token }}</h1>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
            <div>
                <p class="text-gray-600 dark:text-gray-400"><strong>Serial Number:</strong> {{ $token->serial_number }}</p>
                <p class="text-gray-600 dark:text-gray-400"><strong>Hardware ID:</strong> {{ $token->hardware_id }}</p>
                <p class="text-gray-600 dark:text-gray-400"><strong>Status:</strong> {{ ucfirst($token->status) }}</p>
                <p class="text-gray-600 dark:text-gray-400"><strong>Expires At:</strong> {{ $token->expires_at?->format('Y-m-d H:i') ?? 'N/A' }}</p>
                <p class="text-gray-600 dark:text-gray-400"><strong>Paired At:</strong> {{ $token->paired_at?->format('Y-m-d H:i') ?? 'N/A' }}</p>
                @if ($token->device)
                    <p class="text-gray-600 dark:text-gray-400"><strong>Paired Device:</strong> <a href="#" class="text-blue-500">{{ $token->device->name }} ({{ $token->device->serial_number }})</a></p>
                @endif
                @if ($token->pairedByAccount)
                    <p class="text-gray-600 dark:text-gray-400"><strong>Paired By:</strong> <a href="#" class="text-blue-500">{{ $token->pairedByAccount->username }}</a></p>
                @endif
                <p class="text-gray-600 dark:text-gray-400"><strong>Created At:</strong> {{ $token->created_at->format('Y-m-d H:i') }}</p>
                <p class="text-gray-600 dark:text-gray-400"><strong>Updated At:</strong> {{ $token->updated_at->format('Y-m-d H:i') }}</p>
            </div>
            <div class="flex flex-col items-center justify-center">
                <h3 class="text-xl font-semibold mb-2">QR Code</h3>
                <img src="{{ route('admin.provisioning-tokens.qr', $token) }}" alt="QR Code for {{ $token->token }}" class="w-48 h-48 border border-gray-300 p-2 rounded-lg">
                <a href="{{ route('admin.provisioning-tokens.qr', $token) }}" download="{{ $token->token }}_qr.png" class="mt-4 px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600">Download QR</a>
            </div>
        </div>

        <h3 class="text-xl font-semibold mb-2">Metadata</h3>
        <pre class="bg-gray-100 dark:bg-gray-700 p-4 rounded-md text-sm overflow-x-auto">{{ json_encode($token->metadata, JSON_PRETTY_PRINT) }}</pre>

        <div class="mt-6 flex justify-end">
            <a href="{{ route('admin.provisioning-tokens.index') }}" class="px-4 py-2 bg-gray-300 text-gray-800 rounded-md hover:bg-gray-400 mr-2">Back to List</a>
            @if ($token->status === 'pending')
                <form action="{{ route('admin.provisioning-tokens.revoke', $token) }}" method="POST" class="inline-block">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-red-500 text-white rounded-md hover:bg-red-600" onclick="return confirm('Are you sure you want to revoke this token?');">Revoke Token</button>
                </form>
            @endif
        </div>
    </div>
</div>
@endsection
