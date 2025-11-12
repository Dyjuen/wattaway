@props(['device'])

<div id="scheduler-{{ $device->device_id }}">
    <div class="bg-white/5 rounded-lg p-6 border border-white/10">
        <div class="flex items-center mb-4">
            <div class="w-8 h-8 rounded-full bg-blue-500/20 flex items-center justify-center mr-3">
                <svg class="w-4 h-4 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <h4 class="font-semibold text-lg">Schedule Settings</h4>
        </div>
        <div class="grid grid-cols-2 gap-6">
            <div class="space-y-2">
                <label class="block text-sm font-medium text-gray-300">Start Time</label>
                <div class="relative">
                    <x-input type="time" id="scheduler-start-{{ $device->device_id }}" name="scheduler-start-{{ $device->device_id }}" value="{{ $device->configurations['scheduler']['start_time'] ?? '08:00' }}" />
                </div>
            </div>
            <div class="space-y-2">
                <label class="block text-sm font-medium text-gray-300">End Time</label>
                <div class="relative">
                    <x-input type="time" id="scheduler-end-{{ $device->device_id }}" name="scheduler-end-{{ $device->device_id }}" value="{{ $device->configurations['scheduler']['end_time'] ?? '18:00' }}" />
                </div>
            </div>
        </div>
    </div>
</div>
