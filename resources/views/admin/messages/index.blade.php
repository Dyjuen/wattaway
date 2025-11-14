@extends('layouts.admin')

@section('title', 'MQTT Message Logs')

@section('content')
<div class="bg-white rounded-lg shadow p-6">
    <h2 class="text-2xl font-bold mb-4">MQTT Message Logs</h2>

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">Timestamp</th>
                    <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">Direction</th>
                    <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">Device ID</th>
                    <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">Topic</th>
                    <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">Payload</th>
                    <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">Status</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($messages as $message)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-2 text-sm text-gray-500">{{ $message->created_at->format('Y-m-d H:i:s') }}</td>
                        <td class="px-4 py-2 text-sm">
                            @if($message->direction === 'incoming')
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                    IN
                                </span>
                            @else
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                    OUT
                                </span>
                            @endif
                        </td>
                        <td class="px-4 py-2 text-sm font-mono">{{ $message->device_id }}</td>
                        <td class="px-4 py-2 text-sm font-mono">{{ $message->topic }}</td>
                        <td class="px-4 py-2 text-sm">
                            <pre class="json-viewer">{{ json_encode($message->payload, JSON_PRETTY_PRINT) }}</pre>
                        </td>
                        <td class="px-4 py-2 text-sm">
                             @if($message->status === 'success')
                                <span class="text-green-600">Success</span>
                            @else
                                <span class="text-red-600">Error</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-8 text-gray-500">No messages found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        {{ $messages->links() }}
    </div>
</div>
@endsection