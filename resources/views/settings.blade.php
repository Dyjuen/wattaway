<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Settings - WattAway</title>

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
        .settings-bg {
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
        .glass-nav {
            background: rgba(0, 0, 0, 0.3);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
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
    </style>
</head>

<body class="antialiased text-white settings-bg min-h-screen">
    <div class="relative min-h-screen">
        <!-- Settings Navigation Bar -->
        <header class="glass-nav sticky top-0 z-50">
            <div class="container mx-auto px-6 py-4">
                <nav class="flex items-center justify-between">
                    <!-- Logo and Back Button -->
                    <div class="flex items-center space-x-4">
                        <a href="{{ route('dashboard') }}" class="p-2 rounded-full hover:bg-white/10 transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                            </svg>
                        </a>
                        <img src="{{ asset('images/logo.png') }}" alt="WattAway Logo" class="h-10 w-10 rounded-full">
                        <h1 class="text-2xl font-bold">Settings</h1>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex items-center space-x-3">
                        <button id="saveBtn" class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-2 rounded-lg transition-colors">
                            Save Changes
                        </button>
                        <a href="{{ route('dashboard') }}" class="bg-white/10 hover:bg-white/20 text-white px-4 py-2 rounded-lg transition-colors">
                            Cancel
                        </a>
                    </div>
                </nav>
            </div>
        </header>

        <!-- Main Settings Content -->
        <main class="container mx-auto px-6 py-8">
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
                <!-- Settings Sidebar -->
                <div class="lg:col-span-1">
                    <div class="glass-card rounded-2xl p-6 sticky top-24">
                        <h2 class="text-xl font-bold mb-6">Settings Menu</h2>
                        <nav class="space-y-2">
                            <button data-section="profile" class="setting-item w-full text-left p-3 rounded-lg active">
                                <div class="flex items-center space-x-3">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                    <span>Profile</span>
                                </div>
                            </button>
                            <button data-section="devices" class="setting-item w-full text-left p-3 rounded-lg">
                                <div class="flex items-center space-x-3">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                    </svg>
                                    <span>Devices</span>
                                </div>
                            </button>
                            <button data-section="notifications" class="setting-item w-full text-left p-3 rounded-lg">
                                <div class="flex items-center space-x-3">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                                    </svg>
                                    <span>Notifications</span>
                                </div>
                            </button>
                            <button data-section="security" class="setting-item w-full text-left p-3 rounded-lg">
                                <div class="flex items-center space-x-3">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                    </svg>
                                    <span>Security</span>
                                </div>
                            </button>
                            <button data-section="preferences" class="setting-item w-full text-left p-3 rounded-lg">
                                <div class="flex items-center space-x-3">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                    <span>Preferences</span>
                                </div>
                            </button>
                        </nav>
                    </div>
                </div>

                <!-- Settings Content -->
                <div class="lg:col-span-3">
                    <!-- Profile Settings -->
                    <div id="profile-section" class="glass-card rounded-2xl p-6 mb-6">
                        <h2 class="text-2xl font-bold mb-6">Profile Settings</h2>
                        <form class="space-y-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium mb-2">First Name</label>
                                    <input type="text" class="w-full bg-white/10 border border-white/20 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500" value="John">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium mb-2">Last Name</label>
                                    <input type="text" class="w-full bg-white/10 border border-white/20 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500" value="Doe">
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-2">Email Address</label>
                                <input type="email" class="w-full bg-white/10 border border-white/20 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500" value="john.doe@example.com">
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-2">Phone Number</label>
                                <input type="tel" class="w-full bg-white/10 border border-white/20 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500" value="+1 (555) 123-4567">
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-2">Bio</label>
                                <textarea class="w-full bg-white/10 border border-white/20 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 h-24" placeholder="Tell us about yourself...">Smart home enthusiast and energy efficiency advocate.</textarea>
                            </div>
                        </form>
                    </div>

                    <!-- Device Settings -->
                    <div id="devices-section" class="glass-card rounded-2xl p-6 mb-6 hidden">
                        <h2 class="text-2xl font-bold mb-6">Device Management</h2>
                        <div class="space-y-4">
                            <div class="bg-white/5 rounded-lg p-4 border border-white/10">
                                <div class="flex items-center justify-between mb-3">
                                    <div>
                                        <h3 class="font-semibold">Living Room Socket</h3>
                                        <p class="text-sm text-gray-400">Smart Socket #1</p>
                                    </div>
                                    <div class="flex items-center space-x-3">
                                        <span class="text-sm text-green-400">Online</span>
                                        <button class="text-red-400 hover:text-red-300">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                                <div class="flex space-x-4">
                                    <button class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg text-sm">Configure</button>
                                    <button class="bg-white/10 hover:bg-white/20 text-white px-4 py-2 rounded-lg text-sm">View Logs</button>
                                </div>
                            </div>
                            <div class="bg-white/5 rounded-lg p-4 border border-white/10">
                                <div class="flex items-center justify-between mb-3">
                                    <div>
                                        <h3 class="font-semibold">Kitchen Socket</h3>
                                        <p class="text-sm text-gray-400">Smart Socket #2</p>
                                    </div>
                                    <div class="flex items-center space-x-3">
                                        <span class="text-sm text-green-400">Online</span>
                                        <button class="text-red-400 hover:text-red-300">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                                <div class="flex space-x-4">
                                    <button class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg text-sm">Configure</button>
                                    <button class="bg-white/10 hover:bg-white/20 text-white px-4 py-2 rounded-lg text-sm">View Logs</button>
                                </div>
                            </div>
                        </div>
                        <div class="mt-6">
                            <button class="w-full bg-green-500 hover:bg-green-600 text-white px-6 py-3 rounded-lg transition-colors">
                                Add New Device
                            </button>
                        </div>
                    </div>

                    <!-- Notification Settings -->
                    <div id="notifications-section" class="glass-card rounded-2xl p-6 mb-6 hidden">
                        <h2 class="text-2xl font-bold mb-6">Notification Preferences</h2>
                        <div class="space-y-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h3 class="font-semibold">Device Status Alerts</h3>
                                    <p class="text-sm text-gray-400">Get notified when devices go online/offline</p>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" class="sr-only peer" checked>
                                    <div class="w-11 h-6 bg-gray-600 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                </label>
                            </div>
                            <div class="flex items-center justify-between">
                                <div>
                                    <h3 class="font-semibold">Energy Usage Reports</h3>
                                    <p class="text-sm text-gray-400">Daily and weekly energy consumption summaries</p>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" class="sr-only peer" checked>
                                    <div class="w-11 h-6 bg-gray-600 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                </label>
                            </div>
                            <div class="flex items-center justify-between">
                                <div>
                                    <h3 class="font-semibold">Security Alerts</h3>
                                    <p class="text-sm text-gray-400">Critical security notifications and updates</p>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" class="sr-only peer" checked>
                                    <div class="w-11 h-6 bg-gray-600 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                </label>
                            </div>
                            <div class="flex items-center justify-between">
                                <div>
                                    <h3 class="font-semibold">Maintenance Reminders</h3>
                                    <p class="text-sm text-gray-400">Scheduled maintenance and firmware updates</p>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" class="sr-only peer">
                                    <div class="w-11 h-6 bg-gray-600 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Security Settings -->
                    <div id="security-section" class="glass-card rounded-2xl p-6 mb-6 hidden">
                        <h2 class="text-2xl font-bold mb-6">Security Settings</h2>
                        <div class="space-y-6">
                            <div>
                                <label class="block text-sm font-medium mb-2">Current Password</label>
                                <input type="password" class="w-full bg-white/10 border border-white/20 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Enter current password">
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-2">New Password</label>
                                <input type="password" class="w-full bg-white/10 border border-white/20 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Enter new password">
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-2">Confirm New Password</label>
                                <input type="password" class="w-full bg-white/10 border border-white/20 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Confirm new password">
                            </div>
                            <div class="flex items-center justify-between p-4 bg-yellow-500/10 border border-yellow-500/20 rounded-lg">
                                <div>
                                    <h3 class="font-semibold text-yellow-400">Two-Factor Authentication</h3>
                                    <p class="text-sm text-gray-400">Add an extra layer of security to your account</p>
                                </div>
                                <button class="bg-yellow-500 hover:bg-yellow-600 text-black px-4 py-2 rounded-lg font-semibold">
                                    Enable 2FA
                                </button>
                            </div>
                            <div class="flex items-center justify-between p-4 bg-red-500/10 border border-red-500/20 rounded-lg">
                                <div>
                                    <h3 class="font-semibold text-red-400">Danger Zone</h3>
                                    <p class="text-sm text-gray-400">Permanently delete your account and all data</p>
                                </div>
                                <button class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg">
                                    Delete Account
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Preferences Settings -->
                    <div id="preferences-section" class="glass-card rounded-2xl p-6 mb-6 hidden">
                        <h2 class="text-2xl font-bold mb-6">App Preferences</h2>
                        <div class="space-y-6">
                            <div>
                                <label class="block text-sm font-medium mb-2">Theme</label>
                                <select class="w-full bg-white/10 border border-white/20 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="dark" selected>Dark Theme</option>
                                    <option value="light">Light Theme</option>
                                    <option value="auto">Auto (System)</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-2">Language</label>
                                <select class="w-full bg-white/10 border border-white/20 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="en" selected>English</option>
                                    <option value="es">Español</option>
                                    <option value="fr">Français</option>
                                    <option value="de">Deutsch</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-2">Timezone</label>
                                <select class="w-full bg-white/10 border border-white/20 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="UTC-8" selected>Pacific Time (UTC-8)</option>
                                    <option value="UTC-5">Eastern Time (UTC-5)</option>
                                    <option value="UTC+0">GMT (UTC+0)</option>
                                    <option value="UTC+1">Central European Time (UTC+1)</option>
                                </select>
                            </div>
                            <div class="flex items-center justify-between">
                                <div>
                                    <h3 class="font-semibold">Auto Energy Reports</h3>
                                    <p class="text-sm text-gray-400">Automatically generate energy usage reports</p>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" class="sr-only peer" checked>
                                    <div class="w-11 h-6 bg-gray-600 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                </label>
                            </div>
                            <div class="flex items-center justify-between">
                                <div>
                                    <h3 class="font-semibold">Smart Scheduling</h3>
                                    <p class="text-sm text-gray-400">Automatically schedule devices based on usage patterns</p>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" class="sr-only peer">
                                    <div class="w-11 h-6 bg-gray-600 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

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
                    type === 'success' ? 'bg-green-500' : 'bg-blue-500'
                } text-white`;
                notification.textContent = message;

                document.body.appendChild(notification);

                setTimeout(() => {
                    notification.remove();
                }, 3000);
            }
        });
    </script>
</body>
</html>
