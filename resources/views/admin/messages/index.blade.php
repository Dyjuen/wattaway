@extends('layouts.admin')

@section('title', 'Message Logs')

@section('content')
<div class="bg-white rounded-lg shadow p-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold">Message Logs</h2>
        <button onclick="location.reload()" class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">
            <i class="fas fa-refresh mr-2"></i>Refresh
        </button>
    </div>

    <!-- Filters -->
    <form method="GET" class="mb-6 grid grid-cols-1 md:grid-cols-5 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Device</label>
            <select name="device_id" class="w-full border rounded px-3 py-2">
                <option value="">All Devices</option>
                @foreach($devices as $device)
                    <option value="{{ $device->id }}" {{ request('device_id') == $device->id ? 'selected' : '' }}>
                        {{ $device->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Direction</label>
            <select name="direction" class="w-full border rounded px-3 py-2">
                <option value="">All</option>
                <option value="incoming" {{ request('direction') == 'incoming' ? 'selected' : '' }}>Incoming</option>
                <option value="outgoing" {{ request('direction') == 'outgoing' ? 'selected' : '' }}>Outgoing</option>
            </select>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Type</label>
            <select name="type" class="w-full border rounded px-3 py-2">
                <option value="">All</option>
                <option value="data" {{ request('type') == 'data' ? 'selected' : '' }}>Data</option>
                <option value="command" {{ request('type') == 'command' ? 'selected' : '' }}>Command</option>
                <option value="status" {{ request('type') == 'status' ? 'selected' : '' }}>Status</option>
                <option value="error" {{ request('type') == 'error' ? 'selected' : '' }}>Error</option>
            </select>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
            <select name="status" class="w-full border rounded px-3 py-2">
                <option value="">All</option>
                <option value="success" {{ request('status') == 'success' ? 'selected' : '' }}>Success</option>
                <option value="error" {{ request('status') == 'error' ? 'selected' : '' }}>Error</option>
            </select>
        </div>

        <div class="flex items-end">
            <button type="submit" class="w-full px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">
                <i class="fas fa-filter mr-2"></i>Filter
            </button>
        </div>
    </form>

    <!-- Messages Table -->
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">Time</th>
                    <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">Device</th>
                    <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">Direction</th>
                    <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">Type</th>
                    <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">Status</th>
                    <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">Action</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($messages as $message)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-2 text-sm">{{ $message->created_at->diffForHumans() }}</td>
                        <td class="px-4 py-2 text-sm">{{ $message->device ? $message->device->name : 'N/A' }}</td>
                        <td class="px-4 py-2 text-sm">
                            @if($message->direction === 'incoming')
                                <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded text-xs"><i class="fas fa-arrow-down"></i> Incoming</span>
                            @else
                                <span class="px-2 py-1 bg-purple-100 text-purple-800 rounded text-xs"><i class="fas fa-arrow-up"></i> Outgoing</span>
                            @endif
                        </td>
                        <td class="px-4 py-2 text-sm uppercase">{{ $message->type }}</td>
                        <td class="px-4 py-2 text-sm">
                            @if($message->status === 'success')
                                <span class="px-2 py-1 bg-green-100 text-green-800 rounded text-xs">Success</span>
                            @else
                                <span class="px-2 py-1 bg-red-100 text-red-800 rounded text-xs">Error</span>
                            @endif
                        </td>
                        <td class="px-4 py-2 text-sm">
                            <a href="{{ route('admin.messages.show', $message->id) }}" class="text-indigo-600 hover:text-indigo-800"><i class="fas fa-eye"></i> View</a>
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
