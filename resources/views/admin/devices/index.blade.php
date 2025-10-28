@extends('layouts.admin')

@section('title', 'Devices')

@section('content')
<div class="bg-white rounded-lg shadow p-6">
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-2xl font-bold">All Devices</h2>
        <a href="{{ route('admin.devices.create') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">Create New Device</a>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">ID</th>
                    <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">Name</th>
                    <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">Status</th>
                    <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">Last Seen</th>
                    <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($devices as $device)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-2 text-sm">{{ $device->id }}</td>
                        <td class="px-4 py-2 text-sm">{{ $device->name }}</td>
                        <td class="px-4 py-2 text-sm">{{ $device->status }}</td>
                        <td class="px-4 py-2 text-sm">{{ $device->last_seen_at ? $device->last_seen_at->diffForHumans() : 'Never' }}</td>
                        <td class="px-4 py-2 text-sm">
                            <a href="{{ route('admin.devices.show', $device) }}" class="text-indigo-600 hover:text-indigo-900">View</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center py-8 text-gray-500">No devices found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-6">
        {{ $devices->links() }}
    </div>
</div>
@endsection
