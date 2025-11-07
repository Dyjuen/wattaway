@props(['device'])

<div class="bg-white/5 rounded-xl p-4 border border-white/10">
    <div class="flex items-center justify-between mb-3">
        <div class="flex items-center space-x-3">
            <div class="w-3 h-3 {{ $device->status === 'online' ? 'bg-green-400' : 'bg-red-400' }} rounded-full"></div>
            <div>
                <h3 class="font-semibold">{{ $device->name }}</h3>
                <p class="text-sm text-gray-400">{{ $device->description }}</p>
            </div>
        </div>
        <div class="flex items-center space-x-2">
            <a href="{{ route('devices.show', $device) }}" class="text-sm text-blue-400 hover:text-blue-300">Details</a>
        </div>
    </div>
    <div class="flex justify-between items-center text-sm">
        <span class="text-gray-400">Status: {{ ucfirst($device->status) }}</span>
        <span class="text-gray-400">Last seen: {{ $device->last_seen_at?->diffForHumans() ?? 'Never' }}</span>
    </div>
</div>
