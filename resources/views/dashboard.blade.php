<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Dashboard - WattAway</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Playfair+Display:wght@900&display=swap" rel="stylesheet">

    <!-- Animations -->
    <link rel="stylesheet" href="{{ asset('css/animations.css') }}">
    <link rel="icon" href="{{ asset('images/logo.png') }}" type="image/x-icon">

    <!-- Styles -->
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: 'Inter', sans-serif;
            scroll-behavior: smooth;
        }
        .font-brand {
            font-family: 'Playfair Display', serif;
        }
        .dashboard-bg {
            background-image: url("{{ asset('images/bg-main.png') }}");
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
        }
        .glass-card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        .gradient-text {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
    </style>
</head>

<body class="antialiased text-white dashboard-bg min-h-screen">
    <div class="relative min-h-screen">
        <!-- Navigation Bar -->
        <x-navbar />

        <!-- Main Dashboard Content -->
        <main class="container mx-auto px-6 py-8 pt-24">
            <!-- Welcome Header -->
            <div class="mb-8">
                <h1 class="text-4xl md:text-5xl font-bold mb-2">
                    Welcome back, <span class="gradient-text">{{ auth()->guard('account')->user()->name ?? 'User' }}</span>
                </h1>
                <p class="text-gray-300 text-lg">Manage your WattAway smart devices and monitor your energy usage</p>
            </div>

            <!-- Quick Stats Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <!-- Total Devices -->
                <div class="glass-card rounded-2xl p-6 hover:scale-105 transition-transform duration-300">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-300 text-sm font-medium">Total Devices</p>
                            <p class="text-3xl font-bold mt-2">3</p>
                        </div>
                        <div class="p-3 bg-blue-500/20 rounded-full">
                            <svg class="w-8 h-8 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="mt-4 flex items-center text-sm">
                        <span class="text-green-400">+2</span>
                        <span class="text-gray-400 ml-1">from last month</span>
                    </div>
                </div>

                <!-- Energy Saved -->
                <div class="glass-card rounded-2xl p-6 hover:scale-105 transition-transform duration-300">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-300 text-sm font-medium">Energy Saved</p>
                            <p class="text-3xl font-bold mt-2">127</p>
                            <p class="text-sm text-gray-400">kWh</p>
                        </div>
                        <div class="p-3 bg-green-500/20 rounded-full">
                            <svg class="w-8 h-8 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="mt-4 flex items-center text-sm">
                        <span class="text-green-400">+12%</span>
                        <span class="text-gray-400 ml-1">efficiency increase</span>
                    </div>
                </div>

                <!-- Active Sessions -->
                <div class="glass-card rounded-2xl p-6 hover:scale-105 transition-transform duration-300">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-300 text-sm font-medium">Active Now</p>
                            <p class="text-3xl font-bold mt-2">2</p>
                        </div>
                        <div class="p-3 bg-purple-500/20 rounded-full">
                            <svg class="w-8 h-8 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="mt-4 flex items-center text-sm">
                        <span class="text-blue-400">Living Room</span>
                        <span class="text-gray-400 ml-1">• Kitchen</span>
                    </div>
                </div>

                <!-- Monthly Usage -->
                <div class="glass-card rounded-2xl p-6 hover:scale-105 transition-transform duration-300">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-300 text-sm font-medium">This Month</p>
                            <p class="text-3xl font-bold mt-2">89</p>
                            <p class="text-sm text-gray-400">kWh used</p>
                        </div>
                        <div class="p-3 bg-orange-500/20 rounded-full">
                            <svg class="w-8 h-8 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="mt-4 flex items-center text-sm">
                        <span class="text-red-400">+5%</span>
                        <span class="text-gray-400 ml-1">vs last month</span>
                    </div>
                </div>
            </div>

            <!-- Main Content Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Device Control Panel -->
                <div class="lg:col-span-2">
                    <div class="glass-card rounded-2xl p-6 mb-6">
                        <div class="flex items-center justify-between mb-6">
                            <h2 class="text-2xl font-bold">Device Control</h2>
                            <button class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition-colors">
                                Add Device
                            </button>
                        </div>

                        <!-- Device Cards -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Living Room Socket -->
                            <div class="bg-white/5 rounded-xl p-4 border border-white/10">
                                <div class="flex items-center justify-between mb-3">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-3 h-3 bg-green-400 rounded-full"></div>
                                        <div>
                                            <h3 class="font-semibold">Living Room</h3>
                                            <p class="text-sm text-gray-400">Smart Socket #1</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        <button class="w-10 h-6 bg-green-500 rounded-full relative">
                                            <div class="w-5 h-5 bg-white rounded-full absolute right-0.5 top-0.5"></div>
                                        </button>
                                    </div>
                                </div>
                                <div class="flex justify-between items-center text-sm">
                                    <span class="text-gray-400">Power: 45W</span>
                                    <span class="text-gray-400">Today: 2.3kWh</span>
                                </div>
                            </div>

                            <!-- Kitchen Socket -->
                            <div class="bg-white/5 rounded-xl p-4 border border-white/10">
                                <div class="flex items-center justify-between mb-3">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-3 h-3 bg-green-400 rounded-full"></div>
                                        <div>
                                            <h3 class="font-semibold">Kitchen</h3>
                                            <p class="text-sm text-gray-400">Smart Socket #2</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        <button class="w-10 h-6 bg-green-500 rounded-full relative">
                                            <div class="w-5 h-5 bg-white rounded-full absolute right-0.5 top-0.5"></div>
                                        </button>
                                    </div>
                                </div>
                                <div class="flex justify-between items-center text-sm">
                                    <span class="text-gray-400">Power: 120W</span>
                                    <span class="text-gray-400">Today: 1.8kWh</span>
                                </div>
                            </div>

                            <!-- Bedroom Socket -->
                            <div class="bg-white/5 rounded-xl p-4 border border-white/10">
                                <div class="flex items-center justify-between mb-3">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-3 h-3 bg-red-400 rounded-full"></div>
                                        <div>
                                            <h3 class="font-semibold">Bedroom</h3>
                                            <p class="text-sm text-gray-400">Smart Socket #3</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        <button class="w-10 h-6 bg-gray-600 rounded-full relative">
                                            <div class="w-5 h-5 bg-white rounded-full absolute left-0.5 top-0.5"></div>
                                        </button>
                                    </div>
                                </div>
                                <div class="flex justify-between items-center text-sm">
                                    <span class="text-gray-400">Power: 0W</span>
                                    <span class="text-gray-400">Today: 0.5kWh</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Energy Usage Chart Placeholder -->
                    <div class="glass-card rounded-2xl p-6">
                        <h2 class="text-2xl font-bold mb-6">Energy Usage Analytics</h2>
                        <div class="bg-white/5 rounded-xl h-64 flex items-center justify-center border border-white/10">
                            <div class="text-center">
                                <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                                <p class="text-gray-400">Energy usage chart will be displayed here</p>
                                <p class="text-sm text-gray-500 mt-2">Connect your devices to see real-time analytics</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <!-- Quick Actions -->
                    <div class="glass-card rounded-2xl p-6">
                        <h3 class="text-xl font-bold mb-4">Quick Actions</h3>
                        <div class="space-y-3">
                            <a href="{{ route('settings') }}" class="block w-full bg-white/10 hover:bg-white/20 rounded-lg p-3 transition-colors">
                                <div class="flex items-center space-x-3">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                    <span>Settings</span>
                                </div>
                            </a>
                            <a href="{{ route('information') }}" class="block w-full bg-white/10 hover:bg-white/20 rounded-lg p-3 transition-colors">
                                <div class="flex items-center space-x-3">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <span>Support</span>
                                </div>
                            </a>
                            <a href="{{ route('esp32.control') }}" class="block w-full bg-white/10 hover:bg-white/20 rounded-lg p-3 transition-colors">
                                <div class="flex items-center space-x-3">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                    </svg>
                                    <span>ESP32 Control</span>
                                </div>
                            </a>
                        </div>
                    </div>

                    <!-- Recent Activity -->
                    <div class="glass-card rounded-2xl p-6">
                        <h3 class="text-xl font-bold mb-4">Recent Activity</h3>
                        <div class="space-y-4">
                            <div class="flex items-start space-x-3">
                                <div class="w-2 h-2 bg-blue-400 rounded-full mt-2"></div>
                                <div class="flex-1">
                                    <p class="text-sm">Living Room socket turned ON</p>
                                    <p class="text-xs text-gray-400">2 minutes ago</p>
                                </div>
                            </div>
                            <div class="flex items-start space-x-3">
                                <div class="w-2 h-2 bg-green-400 rounded-full mt-2"></div>
                                <div class="flex-1">
                                    <p class="text-sm">Energy usage report generated</p>
                                    <p class="text-xs text-gray-400">1 hour ago</p>
                                </div>
                            </div>
                            <div class="flex items-start space-x-3">
                                <div class="w-2 h-2 bg-orange-400 rounded-full mt-2"></div>
                                <div class="flex-1">
                                    <p class="text-sm">Kitchen socket scheduled OFF</p>
                                    <p class="text-xs text-gray-400">3 hours ago</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <!-- Logout Button -->
        <div class="fixed bottom-6 right-6">
            <form method="POST" action="{{ route('logout') }}" class="inline">
                @csrf
                <button type="submit" class="bg-red-500 hover:bg-red-600 text-white p-3 rounded-full shadow-lg transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                    </svg>
                </button>
            </form>
        </div>
    </div>

    <script>
        // Add some interactive functionality
        document.addEventListener('DOMContentLoaded', function() {
            // Toggle device switches
            const switches = document.querySelectorAll('button[class*="bg-green-500"], button[class*="bg-gray-600"]');
            switches.forEach(switchBtn => {
                switchBtn.addEventListener('click', function() {
                    const isOn = this.classList.contains('bg-green-500');
                    if (isOn) {
                        this.classList.remove('bg-green-500');
                        this.classList.add('bg-gray-600');
                        this.querySelector('div').classList.remove('right-0.5');
                        this.querySelector('div').classList.add('left-0.5');
                    } else {
                        this.classList.remove('bg-gray-600');
                        this.classList.add('bg-green-500');
                        this.querySelector('div').classList.remove('left-0.5');
                        this.querySelector('div').classList.add('right-0.5');
                    }
                });
            });
        });
    </script>
</body>
</html>
