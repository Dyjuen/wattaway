@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <!-- Stats Cards -->
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm">Total Devices</p>
                <p class="text-3xl font-bold text-indigo-600">{{ $stats['total_devices'] }}</p>
            </div>
            <div class="bg-indigo-100 rounded-full p-3">
                <i class="fas fa-plug text-indigo-600 text-2xl"></i>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm">Online Devices</p>
                <p class="text-3xl font-bold text-green-600">{{ $stats['online_devices'] }}</p>
            </div>
            <div class="bg-green-100 rounded-full p-3">
                <i class="fas fa-circle-check text-green-600 text-2xl"></i>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm">Messages Today</p>
                <p class="text-3xl font-bold text-blue-600">{{ $stats['messages_today'] }}</p>
            </div>
            <div class="bg-blue-100 rounded-full p-3">
                <i class="fas fa-envelope text-blue-600 text-2xl"></i>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm">Errors Today</p>
                <p class="text-3xl font-bold text-red-600">{{ $stats['errors_today'] }}</p>
            </div>
            <div class="bg-red-100 rounded-full p-3">
                <i class="fas fa-triangle-exclamation text-red-600 text-2xl"></i>
            </div>
        </div>
    </div>
</div>

<!-- Recent Messages -->
<div class="bg-white rounded-lg shadow p-6">
    <h2 class="text-xl font-bold mb-4">Recent Messages (Last 10)</h2>
    <div class="overflow-x-auto">
        <table class="min-w-full">
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
            <tbody class="divide-y divide-gray-200">
                @foreach($recentMessages as $message)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-2 text-sm">{{ $message->created_at->format('H:i:s') }}</td>
                    <td class="px-4 py-2 text-sm">
                        {{ $message->device ? $message->device->name : 'N/A' }}
                    </td>
                    <td class="px-4 py-2 text-sm">
                        @if($message->direction === 'incoming')
                            <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded text-xs">
                                <i class="fas fa-arrow-down"></i> Incoming
                            </span>
                        @else
                            <span class="px-2 py-1 bg-purple-100 text-purple-800 rounded text-xs">
                                <i class="fas fa-arrow-up"></i> Outgoing
                            </span>
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
                        <a href="{{ route('admin.messages.show', $message->id) }}" 
                           class="text-indigo-600 hover:text-indigo-800">
                            <i class="fas fa-eye"></i> View
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-4 text-center">
        <a href="{{ route('admin.messages.index') }}" 
           class="text-indigo-600 hover:text-indigo-800 font-medium">
            View All Messages →
        </a>
    </div>
</div>
@endsection
