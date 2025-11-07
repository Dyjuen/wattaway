<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Settings - WattAway</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Playfair+Display:wght@900&display=swap" rel="stylesheet">

    <!-- Preload critical background image -->
    <link rel="preload" as="image" href="{{ asset('images/bg-main.png') }}">

    <!-- Animations -->
    <link rel="stylesheet" href="{{ asset('css/animations.css') }}">
    <link rel="icon" href="{{ asset('images/logo.png') }}" type="image/x-icon">

    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body {
            font-family: 'Inter', sans-serif;
            scroll-behavior: smooth;
            /* Immediate fallback background color to prevent white flash */
            background-color: #0B0F2A;
        }
        .settings-bg {
            background-image: url("{{ asset('images/bg-main.png') }}");
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            /* Ensure smooth transition from fallback color */
            transition: opacity 0.3s ease-in-out;
        }
        .settings-bg.bg-loaded {
            opacity: 1;
        }
        body:not(.bg-loaded) .settings-bg {
            opacity: 0.8;
        }

        .gradient-text {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .setting-item {
            transition: all 0.3s ease;
        }
        .setting-item:hover {
            background: rgba(255, 255, 255, 0.05);
            transform: translateX(4px);
        }
        /* Ensure proper layout with navbar */
        .relative {
            position: relative !important;
        }
        .sticky {
            position: sticky !important;
        }
        main {
            position: relative !important;
            z-index: 1 !important;
        }

        /* Animation CSS - ensure these take precedence */
        .section-hidden {
            opacity: 0 !important;
            transform: translateY(50px) !important;
            transition: all 0.8s cubic-bezier(0.4, 0, 0.2, 1) !important;
        }

        .section-visible {
            opacity: 1 !important;
            transform: translateY(0) !important;
            transition: all 0.8s cubic-bezier(0.4, 0, 0.2, 1) !important;
        }

        .stagger-item {
            opacity: 0 !important;
            transform: translateY(30px) !important;
            transition: all 0.6s cubic-bezier(0.4, 0, 0.2, 1) !important;
        }

        .stagger-item.stagger-visible {
            opacity: 1 !important;
            transform: translateY(0) !important;
            transition: all 0.6s cubic-bezier(0.4, 0, 0.2, 1) !important;
        }
    </style>
</head>

<body class="antialiased text-white settings-bg min-h-screen">
    <script>
        // Immediate background image preloader
        document.addEventListener('DOMContentLoaded', function() {
            // Preload the background image immediately
            const bgImage = new Image();
            bgImage.onload = function() {
                document.body.classList.add('bg-loaded');
            };
            bgImage.src = "{{ asset('images/bg-main.png') }}";

            // Immediate animation debug
            console.log('Settings page loading...');

            // Force animations immediately
            setTimeout(() => {
                console.log('Triggering animations now...');

                // Force main section visible
                const main = document.getElementById('main-content');
                if (main) {
                    main.classList.add('section-visible');
                    main.classList.remove('section-hidden');
                    console.log('Main section made visible');
                }

                // Force all stagger items visible
                const staggerItems = document.querySelectorAll('.stagger-item');
                console.log('Found stagger items:', staggerItems.length);

                staggerItems.forEach((item, index) => {
                    setTimeout(() => {
                        item.classList.add('stagger-visible');
                        console.log('Animated item:', index);
                    }, index * 50);
                });
            }, 100);
        });
    </script>
        <!--Navbar -->
        <x-navbar />

        <!-- Main Settings Content -->
        <main class="container mx-auto mt-12 px-6 py-8 stagger-container" id="main-content">
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
                <!-- Settings Sidebar -->
                <div class="lg:col-span-1 stagger-item">
                    <div class="glass-card rounded-2xl p-6 sticky top-24 stagger-item">
                        <h2 class="text-xl font-bold mb-6">Settings Menu</h2>
                        <nav class="space-y-2">
                            <button data-section="profile" class="setting-item w-full text-left p-3 rounded-lg active stagger-item">
                                <div class="flex items-center space-x-3">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                    <span>Profile</span>
                                </div>
                            </button>
                            <button data-section="devices" class="setting-item w-full text-left p-3 rounded-lg stagger-item">
                                <div class="flex items-center space-x-3">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                    </svg>
                                    <span>Devices</span>
                                </div>
                            </button>
                            <button data-section="security" class="setting-item w-full text-left p-3 rounded-lg stagger-item">
                                <div class="flex items-center space-x-3">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                    </svg>
                                    <span>Security</span>
                                </div>
                            </button>
                        </nav>
                    </div>
                </div>

                <!-- Settings Content -->
                <div class="lg:col-span-3 stagger-item">
                    <!-- Profile Settings -->
                    <x-glass-card id="profile-section" class="mb-6 stagger-item">
                        <h2 class="text-2xl font-bold mb-6">Profile Settings</h2>
                        <form class="space-y-6">
                            <div class="stagger-item">
                                <label class="block text-sm font-medium mb-2">Username</label>
                                <x-input type="text" name="username" id="username" value="{{ $account->username }}" readonly />
                            </div>
                            <div class="stagger-item">
                                <label class="block text-sm font-medium mb-2">Email Address</label>
                                <x-input type="email" name="email" id="email" value="{{ $account->email }}" />
                            </div>
                        </form>
                    </x-glass-card>

                    <!-- Device Settings -->
                    <x-glass-card id="devices-section" class="mb-6 hidden">
                        <h2 class="text-2xl font-bold mb-6">Device Management</h2>

                        @if($devices->count() > 0)
                            <div class="space-y-4">
                                @foreach($devices as $device)
                                    <div class="bg-white/5 rounded-lg p-4 border border-white/10">
                                        <div class="flex items-center justify-between mb-3">
                                            <div>
                                                <h3 class="font-semibold">{{ $device->name }}</h3>
                                                <p class="text-sm text-gray-400">{{ $device->type }} - {{ $device->device_id }}</p>
                                            </div>
                                            <div class="flex items-center space-x-3">
                                                <span class="text-sm {{ $device->status === 'online' ? 'text-green-400' : 'text-gray-400' }}">
                                                    {{ ucfirst($device->status) }}
                                                </span>
                                                <x-button variant="danger" onclick="deleteDevice('{{ $device->device_id }}')">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                    </svg>
                                                </x-button>
                                            </div>
                                        </div>

                                        <!-- Device Configuration Tabs -->
                                        <div class="border-t border-white/10 pt-4">
                                            <div class="flex space-x-1 mb-4" role="tablist">
                                                <x-button variant="secondary" id="scheduler-tab-{{ $device->device_id }}" class="config-tab flex-1 py-2 px-3 text-sm" data-tab="scheduler-{{ $device->device_id }}" data-device="{{ $device->device_id }}">
                                                    Scheduler
                                                </x-button>
                                                <x-button variant="secondary" id="timer-tab-{{ $device->device_id }}" class="config-tab flex-1 py-2 px-3 text-sm" data-tab="timer-{{ $device->device_id }}" data-device="{{ $device->device_id }}">
                                                    Timer
                                                </x-button>
                                                <x-button variant="secondary" id="wattlimit-tab-{{ $device->device_id }}" class="config-tab flex-1 py-2 px-3 text-sm" data-tab="wattlimit-{{ $device->device_id }}" data-device="{{ $device->device_id }}">
                                                    Watt Limit
                                                </x-button>
                                            </div>

                                            <!-- Scheduler Configuration -->
                                            <div id="scheduler-{{ $device->device_id }}" class="config-content hidden">
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
                                                    <div class="flex items-center justify-between mt-6 p-4 bg-blue-500/10 rounded-lg border border-blue-500/20">
                                                        <div>
                                                            <div class="flex items-center">
                                                                <input type="checkbox" id="scheduler-active-{{ $device->device_id }}" class="mr-3 w-4 h-4 text-blue-500 bg-white/10 border-white/20 rounded focus:ring-blue-500 focus:ring-2" {{ isset($device->configurations['scheduler']['is_active']) && $device->configurations['scheduler']['is_active'] ? 'checked' : '' }}>
                                                                <label for="scheduler-active-{{ $device->device_id }}" class="text-sm font-medium text-blue-300">Enable Automatic Scheduling</label>
                                                            </div>
                                                            <p class="text-xs text-gray-400 mt-1">Device will turn on/off automatically based on schedule</p>
                                                        </div>
                                                        <div class="w-10 h-6 bg-blue-500/20 rounded-full relative">
                                                            <div class="w-4 h-4 bg-blue-400 rounded-full absolute top-1 right-1 transition-all duration-200"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Timer Configuration -->
                                            <div id="timer-{{ $device->device_id }}" class="config-content hidden">
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

                                                    <!-- Timer toggle -->
                                                    <div class="flex items-center justify-center p-4 bg-blue-500/10 rounded-lg border border-blue-500/20">
                                                        <div class="flex items-center">
                                                            <input type="checkbox"
                                                                   id="timer-active-{{ $device->device_id }}"
                                                                   class="mr-3 w-4 h-4 text-blue-500 bg-white/10 border-white/20 rounded focus:ring-blue-500 focus:ring-2"
                                                                   {{ isset($device->configurations['timer']['is_active']) && $device->configurations['timer']['is_active'] ? 'checked' : '' }}>
                                                            <div>
                                                                <label for="timer-active-{{ $device->device_id }}" class="text-sm font-medium text-blue-300">Enable Auto-Shutoff Timer</label>
                                                                <p class="text-xs text-gray-400 mt-1">Device will automatically turn off after set duration</p>
                                                            </div>
                                                        </div>
                                                        <div class="ml-4 w-10 h-6 bg-blue-500/20 rounded-full relative">
                                                            <div class="w-4 h-4 bg-blue-400 rounded-full absolute top-1 right-1 transition-all duration-200"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Watt Limit Configuration -->
                                            <div id="wattlimit-{{ $device->device_id }}" class="config-content hidden">
                                                <div class="bg-white/5 rounded-lg p-4">
                                                    <h4 class="font-semibold mb-3">Power Limit Settings</h4>
                                                    <div>
                                                        <label class="block text-sm font-medium mb-2">Maximum Watt-hours (Wh)</label>
                                                        <x-input type="number" id="watt-limit-{{ $device->device_id }}" name="watt-limit-{{ $device->device_id }}" min="1" max="10000" placeholder="1000" value="{{ $device->configurations['watt_limit']['limit'] ?? 1000 }}" />
                                                    </div>
                                                    <div class="flex items-center mt-3">
                                                        <input type="checkbox" id="wattlimit-active-{{ $device->device_id }}" class="mr-2" {{ isset($device->configurations['watt_limit']['is_active']) && $device->configurations['watt_limit']['is_active'] ? 'checked' : '' }}>
                                                        <label for="wattlimit-active-{{ $device->device_id }}" class="text-sm">Enable Watt Limit</label>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="flex justify-end space-x-3 mt-4 pt-4 border-t border-white/10">
                                                <x-button variant="primary" onclick="saveDeviceConfiguration('{{ $device->device_id }}')">Save Configuration</x-button>
                                                <x-button variant="secondary" onclick="loadDeviceConfiguration('{{ $device->device_id }}')">Load Current Settings</x-button>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <!-- Demo Device Configuration (Static) -->
                            <div class="bg-white/5 rounded-lg p-4 border border-white/10">
                                <div class="flex items-center justify-between mb-3">
                                    <div>
                                        <h3 class="font-semibold">Demo Smart Socket</h3>
                                        <p class="text-sm text-gray-400">esp32 - 192.168.1.100</p>
                                    </div>
                                    <div class="flex items-center space-x-3">
                                        <span class="text-sm text-green-400">Online</span>
                                        <x-button variant="danger" onclick="deleteDevice('192.168.1.100')">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </x-button>
                                    </div>
                                </div>

                                <!-- Device Configuration Tabs -->
                                <div class="border-t border-white/10 pt-4">
                                    <div class="flex space-x-1 mb-4" role="tablist">
                                        <x-button id="scheduler-tab-demo" class="config-tab flex-1 py-2 px-3 text-sm" data-tab="scheduler-demo" data-device="demo">
                                            Scheduler
                                        </x-button>
                                        <x-button id="timer-tab-demo" class="config-tab flex-1 py-2 px-3 text-sm" data-tab="timer-demo" data-device="demo">
                                            Timer
                                        </x-button>
                                        <x-button id="wattlimit-tab-demo" class="config-tab flex-1 py-2 px-3 text-sm" data-tab="wattlimit-demo" data-device="demo">
                                            Watt Limit
                                        </x-button>
                                    </div>

                                    <!-- Scheduler Configuration -->
                                    <div id="scheduler-demo" class="config-content">
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
                                                        <x-input type="time" id="scheduler-start-demo" name="scheduler-start-demo" value="08:00" />
                                                    </div>
                                                </div>
                                                <div class="space-y-2">
                                                    <label class="block text-sm font-medium text-gray-300">End Time</label>
                                                    <div class="relative">
                                                        <x-input type="time" id="scheduler-end-demo" name="scheduler-end-demo" value="18:00" />
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="flex items-center justify-between mt-6 p-4 bg-blue-500/10 rounded-lg border border-blue-500/20">
                                                <div>
                                                    <div class="flex items-center">
                                                        <input type="checkbox" id="scheduler-active-demo" class="mr-3 w-4 h-4 text-blue-500 bg-white/10 border-white/20 rounded focus:ring-blue-500 focus:ring-2" checked>
                                                        <label for="scheduler-active-demo" class="text-sm font-medium text-blue-300">Enable Automatic Scheduling</label>
                                                    </div>
                                                    <p class="text-xs text-gray-400 mt-1">Device will turn on/off automatically based on schedule</p>
                                                </div>
                                                <div class="w-10 h-6 bg-blue-500/20 rounded-full relative">
                                                    <div class="w-4 h-4 bg-blue-400 rounded-full absolute top-1 right-1 transition-all duration-200"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Timer Configuration -->
                                    <div id="timer-demo" class="config-content hidden">
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
                                                        <circle id="timer-arc-demo"
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
                                                            <div id="timer-display-demo" class="text-2xl font-bold text-blue-400 mb-1">30m</div>
                                                            <div class="text-xs text-gray-400">Duration</div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Timer controls -->
                                                <div class="flex items-center space-x-6">
                                                    <button onclick="adjustTimer('demo', -5)"
                                                            class="w-10 h-10 rounded-full bg-white/10 hover:bg-white/20 flex items-center justify-center transition-all duration-200 hover:scale-105">
                                                        <svg class="w-5 h-5 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                                                        </svg>
                                                    </button>

                                                    <div class="w-64">
                                                        <input type="range"
                                                               id="timer-duration-demo"
                                                               min="5"
                                                               max="120"
                                                               value="30"
                                                               step="5"
                                                               class="w-full h-2 bg-white/10 rounded-lg appearance-none cursor-pointer slider"
                                                               oninput="updateTimerDisplay('demo', this.value)"
                                                               style="background: linear-gradient(to right, rgba(59, 130, 246, 0.8) 0%, rgba(59, 130, 246, 0.8) 25%, rgba(255,255,255,0.1) 25%, rgba(255,255,255,0.1) 100%);">
                                                    </div>

                                                    <button onclick="adjustTimer('demo', 5)"
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

                                            <!-- Timer toggle -->
                                            <div class="flex items-center justify-center p-4 bg-blue-500/10 rounded-lg border border-blue-500/20">
                                                <div class="flex items-center">
                                                    <input type="checkbox"
                                                           id="timer-active-demo"
                                                           class="mr-3 w-4 h-4 text-blue-500 bg-white/10 border-white/20 rounded focus:ring-blue-500 focus:ring-2"
                                                           checked>
                                                    <div>
                                                        <label for="timer-active-demo" class="text-sm font-medium text-blue-300">Enable Auto-Shutoff Timer</label>
                                                        <p class="text-xs text-gray-400 mt-1">Device will automatically turn off after set duration</p>
                                                    </div>
                                                </div>
                                                <div class="ml-4 w-10 h-6 bg-blue-500/20 rounded-full relative">
                                                    <div class="w-4 h-4 bg-blue-400 rounded-full absolute top-1 right-1 transition-all duration-200"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Watt Limit Configuration -->
                                    <div id="wattlimit-demo" class="config-content hidden">
                                        <div class="bg-white/5 rounded-lg p-4">
                                            <h4 class="font-semibold mb-3">Power Limit Settings</h4>
                                            <div>
                                                <label class="block text-sm font-medium mb-2">Maximum Watt-hours (Wh)</label>
                                                <x-input type="number" id="watt-limit-demo" name="watt-limit-demo" min="1" max="10000" placeholder="1000" value="1000" />
                                            </div>
                                            <div class="flex items-center mt-3">
                                                <input type="checkbox" id="wattlimit-active-demo" class="mr-2" checked>
                                                <label for="wattlimit-active-demo" class="text-sm">Enable Watt Limit</label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="flex justify-end space-x-3 mt-4 pt-4 border-t border-white/10">
                                        <x-button variant="primary" onclick="saveDeviceConfiguration('demo')">Save Configuration</x-button>
                                        <x-button variant="secondary" onclick="loadDeviceConfiguration('demo')">Load Current Settings</x-button>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <div class="mt-6">
                            <x-button variant="primary" class="w-full">Add New Device</x-button>
                        </div>
                    </x-glass-card>

                    <!-- Security Settings -->
                    <x-glass-card id="security-section" class="mb-6 hidden">
                        <h2 class="text-2xl font-bold mb-6">Security Settings</h2>
                        <form method="POST" action="{{ route('password.update') }}" class="space-y-6">
                            @csrf
                            <div>
                                <label class="block text-sm font-medium mb-2">Current Password</label>
                                <x-input type="password" name="current_password" id="current_password" placeholder="Enter current password" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-2">New Password</label>
                                <x-input type="password" name="new_password" id="new_password" placeholder="Enter new password" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-2">Confirm New Password</label>
                                <x-input type="password" name="new_password_confirmation" id="new_password_confirmation" placeholder="Confirm new password" />
                            </div>
                            <div class="flex justify-end">
                                <x-button type="submit" variant="primary">Save</x-button>
                            </div>
                        </form>
                    </x-glass-card>
                </div>
            </div>
        </main>
    </div>

    <script>
        // Add custom slider styling
        const style = document.createElement('style');
        style.textContent = `
            /* Custom slider styling */
            .slider::-webkit-slider-thumb {
                appearance: none;
                height: 20px;
                width: 20px;
                border-radius: 50%;
                background: rgba(59, 130, 246, 0.9);
                cursor: pointer;
                border: 2px solid rgba(255, 255, 255, 0.2);
                box-shadow: 0 0 10px rgba(59, 130, 246, 0.3);
            }

            .slider::-moz-range-thumb {
                height: 20px;
                width: 20px;
                border-radius: 50%;
                background: rgba(59, 130, 246, 0.9);
                cursor: pointer;
                border: 2px solid rgba(255, 255, 255, 0.2);
                box-shadow: 0 0 10px rgba(59, 130, 246, 0.3);
            }

            .slider::-webkit-slider-track {
                background: transparent;
            }

            .slider::-moz-range-track {
                background: transparent;
            }
        `;
        document.head.appendChild(style);

        document.addEventListener('DOMContentLoaded', function() {
            // Settings navigation
            const navButtons = document.querySelectorAll('[data-section]');
            const sections = document.querySelectorAll('[id$="-section"]');

            navButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const sectionName = this.getAttribute('data-section');

                    // Remove active class from all buttons
                    navButtons.forEach(btn => btn.classList.remove('active', 'bg-white/10'));

                    // Add active class to clicked button
                    this.classList.add('active', 'bg-white/10');

                    // Hide all sections
                    sections.forEach(section => section.classList.add('hidden'));

                    // Show selected section
                    document.getElementById(sectionName + '-section').classList.remove('hidden');
                });
            });

            // Device configuration tabs
            const configTabs = document.querySelectorAll('.config-tab');
            const configContents = document.querySelectorAll('.config-content');

            configTabs.forEach(tab => {
                tab.addEventListener('click', function() {
                    const tabId = this.getAttribute('data-tab');
                    const deviceId = this.getAttribute('data-device');

                    // Remove active state from all tabs for this device
                    document.querySelectorAll(`[data-device="${deviceId}"]`).forEach(t => {
                        t.classList.remove('bg-blue-500', 'text-white');
                        t.classList.add('bg-white/10', 'text-gray-300');
                    });

                    // Add active state to clicked tab
                    this.classList.remove('bg-white/10', 'text-gray-300');
                    this.classList.add('bg-blue-500', 'text-white');

                    // Hide all config contents for this device
                    document.querySelectorAll(`[id$="-${deviceId}"]`).forEach(content => {
                        if (content.id.startsWith(tabId.split('-')[0])) {
                            content.classList.remove('hidden');
                        } else {
                            content.classList.add('hidden');
                        }
                    });
                });
            });

            // Timer functionality
            window.adjustTimer = function(deviceId, adjustment) {
                const slider = document.getElementById(`timer-duration-${deviceId}`);
                const currentValue = parseInt(slider.value);
                const newValue = Math.max(5, Math.min(120, currentValue + adjustment));
                slider.value = newValue;
                updateTimerDisplay(deviceId, newValue);
            };

            window.updateTimerDisplay = function(deviceId, minutes) {
                const display = document.getElementById(`timer-display-${deviceId}`);
                const arc = document.getElementById(`timer-arc-${deviceId}`);

                if (minutes >= 60) {
                    const hours = Math.floor(minutes / 60);
                    const remainingMinutes = minutes % 60;
                    display.textContent = `${hours}h ${remainingMinutes}m`;
                } else {
                    display.textContent = `${minutes}m`;
                }

                // Update the SVG arc based on the percentage (0-120 minutes = 0-100%)
                const percentage = Math.min((minutes / 120) * 100, 100);
                const circumference = 2 * Math.PI * 19; // radius is 19
                const dashArray = `${(percentage / 100) * circumference} ${circumference}`;

                if (arc) {
                    arc.setAttribute('stroke-dasharray', dashArray);
                }
            };

            // Save device configuration
            window.saveDeviceConfiguration = async function(deviceId) {
                const saveBtn = document.querySelector(`#save-config-${deviceId}`);
                const originalText = saveBtn ? saveBtn.textContent : 'Save Configuration';

                try {
                    // Show loading state
                    if (saveBtn) {
                        saveBtn.textContent = 'Saving...';
                        saveBtn.disabled = true;
                    }

                    // Collect configuration data
                    const configData = {
                        timer: {
                            duration: parseInt(document.getElementById(`timer-duration-${deviceId}`).value),
                            is_active: document.getElementById(`timer-active-${deviceId}`).checked
                        },
                        scheduler: {
                            start_time: document.getElementById(`scheduler-start-${deviceId}`).value,
                            end_time: document.getElementById(`scheduler-end-${deviceId}`).value,
                            is_active: document.getElementById(`scheduler-active-${deviceId}`).checked
                        },
                        watt_limit: {
                            limit: parseInt(document.getElementById(`watt-limit-${deviceId}`).value),
                            is_active: document.getElementById(`wattlimit-active-${deviceId}`).checked
                        }
                    };

                    // Send to server
                    const response = await fetch(`/esp32/${deviceId}/configuration`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                        },
                        body: JSON.stringify(configData)
                    });

                    if (!response.ok) {
                        throw new Error('Failed to save configuration');
                    }

                    const result = await response.json();

                    // Show success message
                    showNotification('Configuration saved successfully!', 'success');

                } catch (error) {
                    console.error('Error saving configuration:', error);
                    showNotification('Failed to save configuration. Please try again.', 'error');
                } finally {
                    // Reset button state
                    if (saveBtn) {
                        saveBtn.textContent = originalText;
                        saveBtn.disabled = false;
                    }
                }
            };

            // Load device configuration
            window.loadDeviceConfiguration = async function(deviceId) {
                try {
                    const response = await fetch(`/esp32/${deviceId}/configuration`, {
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                        }
                    });

                    if (!response.ok) {
                        throw new Error('Failed to load configuration');
                    }

                    const config = await response.json();

                    // Apply timer settings
                    if (config.timer) {
                        document.getElementById(`timer-duration-${deviceId}`).value = config.timer.value?.duration || 30;
                        document.getElementById(`timer-active-${deviceId}`).checked = config.timer.is_active;
                        updateTimerDisplay(deviceId, config.timer.value?.duration || 30);
                    }

                    // Apply scheduler settings
                    if (config.scheduler) {
                        document.getElementById(`scheduler-start-${deviceId}`).value = config.scheduler.value?.start_time || '08:00';
                        document.getElementById(`scheduler-end-${deviceId}`).value = config.scheduler.value?.end_time || '18:00';
                        document.getElementById(`scheduler-active-${deviceId}`).checked = config.scheduler.is_active;
                    }

                    // Apply watt limit settings
                    if (config.watt_limit) {
                        document.getElementById(`watt-limit-${deviceId}`).value = config.watt_limit.value?.limit || 1000;
                        document.getElementById(`wattlimit-active-${deviceId}`).checked = config.watt_limit.is_active;
                    }

                    showNotification('Configuration loaded successfully!', 'success');

                } catch (error) {
                    console.error('Error loading configuration:', error);
                    showNotification('Failed to load configuration. Please try again.', 'error');
                }
            };

            // Delete device
            window.deleteDevice = function(deviceId) {
                if (confirm('Are you sure you want to delete this device?')) {
                    // TODO: Implement device deletion
                    showNotification('Device deletion not implemented yet', 'info');
                }
            };

            // Save button functionality
            document.getElementById('saveBtn').addEventListener('click', function() {
                // Add saving animation
                this.textContent = 'Saving...';
                this.disabled = true;

                setTimeout(() => {
                    this.textContent = 'Save Changes';
                    this.disabled = false;

                    // Show success message
                    showNotification('Settings saved successfully!', 'success');
                }, 2000);
            });

            // Notification system
            function showNotification(message, type = 'info') {
                const notification = document.createElement('div');
                notification.className = `fixed top-4 right-4 px-6 py-3 rounded-lg z-50 ${
                    type === 'success' ? 'bg-green-500' :
                    type === 'error' ? 'bg-red-500' : 'bg-blue-500'
                } text-white`;
                notification.textContent = message;

                document.body.appendChild(notification);

                setTimeout(() => {
                    notification.remove();
                }, 3000);
            }

            // Initialize timer displays
            document.querySelectorAll('[id^="timer-duration-"]').forEach(slider => {
                const deviceId = slider.id.replace('timer-duration-', '');
                updateTimerDisplay(deviceId, slider.value);
            });

            // Animation System - Initialize after DOM is loaded
            document.addEventListener('DOMContentLoaded', function() {
                console.log('Settings animation system initializing...');

                // Check if IntersectionObserver is supported
                if (!window.IntersectionObserver) {
                    console.log('IntersectionObserver not supported, using fallback');
                    document.body.classList.add('animations-disabled');
                    return;
                }

                const sectionObserver = new IntersectionObserver((entries) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            console.log('Settings section intersecting:', entry.target);
                            entry.target.classList.add('section-visible');
                            entry.target.classList.remove('section-hidden');
                        }
                    });
                }, {
                    threshold: 0.1,
                    rootMargin: '0px 0px -50px 0px'
                });

                // Observe main content for appear animations
                const animatedSections = document.querySelectorAll('main');
                console.log('Settings found main sections:', animatedSections.length);
                animatedSections.forEach(section => {
                    section.classList.add('section-hidden');
                    sectionObserver.observe(section);
                    console.log('Settings observing section:', section);
                });

                // Staggered animation for child elements
                const staggerObserver = new IntersectionObserver((entries) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            console.log('Settings stagger container intersecting:', entry.target);
                            const children = entry.target.querySelectorAll('.stagger-item');
                            console.log('Settings found stagger children:', children.length);
                            children.forEach((child, index) => {
                                setTimeout(() => {
                                    child.classList.add('stagger-visible');
                                    console.log('Settings animating child:', index, child);
                                }, index * 80);
                            });
                        }
                    });
                }, {
                    threshold: 0.1
                });

                // Observe elements with staggered children
                const staggerContainers = document.querySelectorAll('.stagger-container');
                console.log('Settings found stagger containers:', staggerContainers.length);
                staggerContainers.forEach(container => {
                    staggerObserver.observe(container);
                    console.log('Settings observing stagger container:', container);
                });

                console.log('Settings animation system initialized');

                // Trigger animation manually after a short delay as fallback
                setTimeout(() => {
                    console.log('Settings fallback animation trigger');
                    document.querySelectorAll('main').forEach(section => {
                        section.classList.add('section-visible');
                        section.classList.remove('section-hidden');
                    });
                    document.querySelectorAll('.stagger-item').forEach((item, index) => {
                        setTimeout(() => {
                            item.classList.add('stagger-visible');
                        }, index * 50);
                    });
                }, 500);
            });

            // Simple animation trigger - runs immediately
            document.addEventListener('DOMContentLoaded', function() {
                setTimeout(() => {
                    console.log('Settings simple animation trigger');
                    // Trigger main section animation
                    document.querySelector('main').classList.add('section-visible');
                    document.querySelector('main').classList.remove('section-hidden');

                    // Trigger staggered animations with a simple delay
                    const staggerItems = document.querySelectorAll('.stagger-item');
                    staggerItems.forEach((item, index) => {
                        setTimeout(() => {
                            item.classList.add('stagger-visible');
                        }, index * 100);
                    });
                }, 100);
            });

            // Debug function to check for conflicts
            window.debugAnimations = function() {
                console.log('=== Animation Debug Info ===');
                console.log('Main sections:', document.querySelectorAll('main').length);
                console.log('Stagger containers:', document.querySelectorAll('.stagger-container').length);
                console.log('Stagger items:', document.querySelectorAll('.stagger-item').length);
                console.log('Section hidden elements:', document.querySelectorAll('.section-hidden').length);
                console.log('Section visible elements:', document.querySelectorAll('.section-visible').length);
                console.log('Stagger visible elements:', document.querySelectorAll('.stagger-visible').length);
            };

            // Force show content after page load
            window.addEventListener('load', function() {
                console.log('Settings page fully loaded');
                setTimeout(() => {
                    document.querySelectorAll('main').forEach(section => {
                        section.classList.add('section-visible');
                        section.classList.remove('section-hidden');
                    });
                    document.querySelectorAll('.stagger-item').forEach((item, index) => {
                        setTimeout(() => {
                            item.classList.add('stagger-visible');
                        }, index * 100);
                    });
                    console.log('Settings content forced visible');
                }, 1000);
            });
        });
    </script>
</body>
</html>
