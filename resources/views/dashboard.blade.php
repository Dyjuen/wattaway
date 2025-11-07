@extends('layouts.base')

@section('title', 'Dashboard - WattAway')

@section('body-class', 'antialiased text-white dashboard-bg min-h-screen')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/animations.css') }}">
@endpush

@section('content')
    <div class="relative min-h-screen">
        <!-- Navigation Bar -->
        <x-navbar />

        <!-- Main Dashboard Content -->
        <main class="container mx-auto px-6 py-8 pt-24 stagger-container">
            <!-- Session Status Messages -->
            @if (session('status'))
                <x-alert type="success" :message="session('status')" />
            @endif
            @if (session('error'))
                <x-alert type="error" :message="session('error')" />
            @endif

            <!-- Welcome Header -->
            <div class="mb-8 stagger-item">
                <x-page-header 
                    title="Welcome back, {{ auth()->guard('account')->user()->name ?? 'User' }}" 
                    subtitle="Manage your WattAway smart devices and monitor your energy usage" />
            </div>

            <!-- Quick Stats Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8 stagger-item">
                <x-stat-card title="Total Devices" value="3" change="+2">
                    <x-slot name="icon">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </x-slot>
                </x-stat-card>

                <x-stat-card title="Energy Saved" value="127 kWh" change="+12%" changeType="increase">
                    <x-slot name="icon">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </x-slot>
                </x-stat-card>

                <x-stat-card title="Active Now" value="2">
                    <x-slot name="icon">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                    </x-slot>
                </x-stat-card>

                <x-stat-card title="This Month" value="89 kWh" change="+5%" changeType="decrease">
                    <x-slot name="icon">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"></path>
                    </x-slot>
                </x-stat-card>
            </div>

            <!-- Main Content Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 stagger-item">
                <!-- Device Control Panel -->
                <div class="lg:col-span-2">
                    <x-glass-card class="mb-6 stagger-item">
                        <div class="flex items-center justify-between mb-6">
                            <h2 class="text-2xl font-bold">Device Control</h2>
                            <x-button>Add Device</x-button>
                        </div>

                        <!-- Device Cards -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 stagger-item">
                            @foreach ($devices as $device)
                                <x-device-card :device="$device" />
                            @endforeach
                        </div>
                    </x-glass-card>

                    <!-- Energy Usage Chart Placeholder -->
                    <x-glass-card class="stagger-item">
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
                    </x-glass-card>
                </div>

                <!-- Sidebar -->
                <div class="space-y-6 stagger-item">
                    <!-- Quick Actions -->
                    <x-glass-card class="stagger-item">
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
                    </x-glass-card>

                    <!-- Recent Activity -->
                    <x-glass-card class="stagger-item">
                        <h3 class="text-xl font-bold mb-4">Recent Activity</h3>
                        <div class="space-y-4">
                            <div class="flex items-start space-x-3 stagger-item">
                                <div class="w-2 h-2 bg-blue-400 rounded-full mt-2"></div>
                                <div class="flex-1">
                                    <p class="text-sm">Living Room socket turned ON</p>
                                    <p class="text-xs text-gray-400">2 minutes ago</p>
                                </div>
                            </div>
                            <div class="flex items-start space-x-3 stagger-item">
                                <div class="w-2 h-2 bg-green-400 rounded-full mt-2"></div>
                                <div class="flex-1">
                                    <p class="text-sm">Energy usage report generated</p>
                                    <p class="text-xs text-gray-400">1 hour ago</p>
                                </div>
                            </div>
                            <div class="flex items-start space-x-3 stagger-item">
                                <div class="w-2 h-2 bg-orange-400 rounded-full mt-2"></div>
                                <div class="flex-1">
                                    <p class="text-sm">Kitchen socket scheduled OFF</p>
                                    <p class="text-xs text-gray-400">3 hours ago</p>
                                </div>
                            </div>
                        </div>
                    </x-glass-card>
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
@endsection

@push('scripts')
    <script>
        // Immediate background image preloader
        document.addEventListener('DOMContentLoaded', function() {
            // Preload the background image immediately
            const bgImage = new Image();
            bgImage.onload = function() {
                document.body.classList.add('bg-loaded');
            };
            bgImage.src = "{{ asset('images/bg-main.png') }}";

            // Section Appear Animation Observer
            const sectionObserver = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('section-visible');
                        entry.target.classList.remove('section-hidden');
                    }
                });
            }, {
                threshold: 0.1,
                rootMargin: '0px 0px -50px 0px'
            });

            // Observe all sections for appear animations
            const animatedSections = document.querySelectorAll('main');
            animatedSections.forEach(section => {
                section.classList.add('section-hidden');
                sectionObserver.observe(section);
            });

            // Staggered animation for child elements
            const staggerObserver = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const children = entry.target.querySelectorAll('.stagger-item');
                        children.forEach((child, index) => {
                            setTimeout(() => {
                                child.classList.add('stagger-visible');
                            }, index * 80); // 80ms delay between each item - faster animation
                        });
                    }
                });
            }, {
                threshold: 0.1
            });

            // Observe elements with staggered children
            const staggerContainers = document.querySelectorAll('.stagger-container');
            staggerContainers.forEach(container => {
                staggerObserver.observe(container);
            });

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
@endpush
