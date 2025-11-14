@extends('layouts.admin')

@section('title', 'Device Provisioning Tokens')

@section('content')
<div class="bg-white rounded-lg shadow p-6">
    <h2 class="text-2xl font-bold mb-4">Device Provisioning Tokens</h2>

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">Token</th>
                    <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">Device ID</th>
                    <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">Expires At</th>
                    <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">Created At</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($tokens as $token)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-2 text-sm font-mono">{{ $token->token }}</td>
                        <td class="px-4 py-2 text-sm">{{ $token->device_id ?? 'N/A' }}</td>
                        <td class="px-4 py-2 text-sm">{{ $token->expires_at->format('Y-m-d H:i:s') }}</td>
                        <td class="px-4 py-2 text-sm">{{ $token->created_at->format('Y-m-d H:i:s') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center py-8 text-gray-500">No provisioning tokens found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        {{ $tokens->links() }}
    </div>
</div>
@endsection