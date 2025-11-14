@extends('layouts.admin')

@section('title', 'Firmware Management')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <!-- Upload Form -->
    <div class="lg:col-span-1">
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-2xl font-bold mb-4">Upload New Firmware</h2>

            @if (session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            @if ($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('admin.firmware.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-4">
                    <label for="version" class="block text-sm font-medium text-gray-700">Version (e.g., 1.0.2)</label>
                    <input type="text" name="version" id="version" value="{{ old('version') }}" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                </div>

                <div class="mb-4">
                    <label for="description" class="block text-sm font-medium text-gray-700">Description / Changelog</label>
                    <textarea name="description" id="description" rows="4"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">{{ old('description') }}</textarea>
                </div>

                <div class="mb-6">
                    <label for="firmware_file" class="block text-sm font-medium text-gray-700">Firmware File (.bin)</label>
                    <input type="file" name="firmware_file" id="firmware_file" required accept=".bin"
                        class="mt-1 block w-full text-sm text-gray-500
                               file:mr-4 file:py-2 file:px-4
                               file:rounded-full file:border-0
                               file:text-sm file:font-semibold
                               file:bg-indigo-50 file:text-indigo-700
                               hover:file:bg-indigo-100">
                </div>

                <div class="flex justify-end">
                    <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                        <i class="fas fa-upload mr-2"></i>Upload
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Firmware List -->
    <div class="lg:col-span-2">
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-2xl font-bold mb-4">Available Firmware</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">Version</th>
                            <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">Description</th>
                            <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">Uploaded At</th>
                            <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($firmwares as $firmware)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-2 text-sm font-mono">{{ $firmware->version }}</td>
                                <td class="px-4 py-2 text-sm">{{ Str::limit($firmware->description, 50) }}</td>
                                <td class="px-4 py-2 text-sm">{{ $firmware->created_at->diffForHumans() }}</td>
                                <td class="px-4 py-2 text-sm">
                                    <form action="{{ route('admin.firmware.destroy', $firmware) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete version {{ $firmware->version }}?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900 focus:outline-none">
                                            Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-8 text-gray-500">No firmware versions uploaded yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
