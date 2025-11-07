@props(['title', 'value', 'icon', 'change', 'changeType' => 'increase'])

<x-glass-card class="hover:scale-105 transition-transform duration-300">
    <div class="flex items-center justify-between">
        <div>
            <p class="text-gray-300 text-sm font-medium">{{ $title }}</p>
            <p class="text-3xl font-bold mt-2">{{ $value }}</p>
        </div>
        <div class="p-3 bg-blue-500/20 rounded-full">
            <svg class="w-8 h-8 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                {{ $icon }}
            </svg>
        </div>
    </div>
    @if(isset($change))
    <div class="mt-4 flex items-center text-sm">
        <span class="{{ $changeType === 'increase' ? 'text-green-400' : 'text-red-400' }}">{{ $change }}</span>
        <span class="text-gray-400 ml-1">from last month</span>
    </div>
    @endif
</x-glass-card>
