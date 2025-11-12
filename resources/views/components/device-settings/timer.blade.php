@props(['device'])

<div id="timer-{{ $device->device_id }}">
    <div class="bg-white/5 rounded-lg p-6 border border-white/10">
        <div class="flex items-center mb-6">
            <div class="w-8 h-8 rounded-full bg-blue-500/20 flex items-center justify-center mr-3">
                <svg class="w-4 h-4 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <div>
                <h4 class="font-semibold text-lg">Timer Settings</h4>
                <p class="text-xs text-gray-400 mt-1">Set duration for automatic shut-off</p>
            </div>
        </div>

        <div class="flex flex-col items-center mb-6">
            <div class="relative w-40 h-40 mb-4">
                <!-- Outer ring -->
                <svg class="w-40 h-40 transform -rotate-90" viewBox="0 0 42 42">
                    <circle cx="21" cy="21" r="19" fill="none" stroke="rgba(255,255,255,0.1)" stroke-width="2"/>
                    <!-- Progress arc -->
                    <circle id="timer-arc-{{ $device->device_id }}"
                            cx="21" cy="21" r="19"
                            fill="none"
                            stroke="rgba(59, 130, 246, 0.8)"
                            stroke-width="3"
                            stroke-linecap="round"
                            stroke-dasharray="30 90"
                            class="transition-all duration-300 ease-out"
                            transform="rotate(-90 21 21)"/>
                </svg>
                <!-- Center content -->
                <div class="absolute inset-0 flex items-center justify-center">
                    <div class="text-center">
                        <div id="timer-display-{{ $device->device_id }}" class="text-2xl font-bold text-blue-400 mb-1">30m</div>
                        <div class="text-xs text-gray-400">Duration</div>
                    </div>
                </div>
            </div>

            <!-- Timer controls -->
            <div class="flex items-center space-x-6">
                <button onclick="adjustTimer('{{ $device->device_id }}', -5)"
                        class="w-10 h-10 rounded-full bg-white/10 hover:bg-white/20 flex items-center justify-center transition-all duration-200 hover:scale-105">
                    <svg class="w-5 h-5 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                    </svg>
                </button>

                <div class="w-64">
                    <input type="range"
                           id="timer-duration-{{ $device->device_id }}"
                           min="5"
                           max="120"
                           value="{{ $device->configurations['timer']['duration'] ?? 30 }}"
                           step="5"
                           class="w-full h-2 bg-white/10 rounded-lg appearance-none cursor-pointer slider"
                           oninput="updateTimerDisplay('{{ $device->device_id }}', this.value)"
                           style="background: linear-gradient(to right, rgba(59, 130, 246, 0.8) 0%, rgba(59, 130, 246, 0.8) 25%, rgba(255,255,255,0.1) 25%, rgba(255,255,255,0.1) 100%);">
                </div>

                <button onclick="adjustTimer('{{ $device->device_id }}', 5)"
                        class="w-10 h-10 rounded-full bg-white/10 hover:bg-white/20 flex items-center justify-center transition-all duration-200 hover:scale-105">
                    <svg class="w-5 h-5 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                </button>
            </div>

            <!-- Time indicators -->
            <div class="flex justify-between w-64 mt-2 text-xs text-gray-400">
                <span>5m</span>
                <span>1h</span>
                <span>2h</span>
            </div>
        </div>
    </div>
</div>
