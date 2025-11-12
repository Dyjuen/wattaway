@extends('layouts.base')

@section('title', 'Settings - WattAway')

@push('styles')
    <link rel="preload" as="image" href="{{ asset('images/bg-main.png') }}">
    <link rel="stylesheet" href="{{ asset('css/animations.css') }}">
    <style>
        .settings-bg {
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            transition: opacity 0.3s ease-in-out;
        }
        .settings-bg > img {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            z-index: -1;
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
        main {
            position: relative !important;
            z-index: 1 !important;
        }
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
@endpush

@section('body-class', 'antialiased text-white settings-bg min-h-screen')

@section('content')
    <img data-src="{{ asset('images/dist/bg-main.png') }}" src="{{ asset('images/dist/placeholders/bg-main.png') }}" alt="Background" class="lazyload">
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const bgImage = new Image();
            bgImage.onload = function() {
                document.body.classList.add('bg-loaded');
            };
            bgImage.src = "{{ asset('images/bg-main.png') }}";

            console.log('Settings page loading...');

            setTimeout(() => {
                console.log('Triggering animations now...');
                const main = document.getElementById('main-content');
                if (main) {
                    main.classList.add('section-visible');
                    main.classList.remove('section-hidden');
                    console.log('Main section made visible');
                }
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
@endsection

@push('scripts')
    <script>
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
        });
    </script>
@endpush
