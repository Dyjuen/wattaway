@props(['device'])

<div id="wattlimit-{{ $device->id }}">
    <div class="bg-white/5 rounded-lg p-4">
        <h4 class="font-semibold mb-3">Power Limit Settings</h4>
        <div>
            <label class="block text-sm font-medium mb-2">Maximum Watt-hours (Wh)</label>
            <x-input type="number" id="watt-limit-{{ $device->id }}" name="watt-limit-{{ $device->id }}" min="1" max="10000" placeholder="1000" value="{{ $device->configurations['watt_limit']['limit'] ?? 1000 }}" />
        </div>
    </div>
</div>
