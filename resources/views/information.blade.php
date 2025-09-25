<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Support & Information - WattAway</title>

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
        .info-bg {
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
        .support-card {
            transition: all 0.3s ease;
        }
        .support-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
        }
        .faq-item {
            transition: all 0.3s ease;
        }
        .faq-item:hover {
            background: rgba(255, 255, 255, 0.05);
        }
    </style>
</head>

<body class="antialiased text-white info-bg min-h-screen">
    <div class="relative min-h-screen">
        <!-- Information Navigation Bar -->
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
                        <h1 class="text-2xl font-bold">Support Center</h1>
                    </div>

                    <!-- Quick Actions -->
                    <div class="flex items-center space-x-3">
                        <button id="contactBtn" class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-2 rounded-lg transition-colors">
                            Contact Support
                        </button>
                        <a href="{{ route('dashboard') }}" class="bg-white/10 hover:bg-white/20 text-white px-4 py-2 rounded-lg transition-colors">
                            Dashboard
                        </a>
                    </div>
                </nav>
            </div>
        </header>

        <!-- Main Content -->
        <main class="container mx-auto px-6 py-8">
            <!-- Hero Section -->
            <div class="text-center mb-16">
                <h1 class="text-4xl md:text-6xl font-bold mb-4">
                    How can we help you <span class="gradient-text">today?</span>
                </h1>
                <p class="text-xl text-gray-300 max-w-3xl mx-auto">
                    Get the support you need for your WattAway smart devices. Browse our knowledge base, contact our support team, or explore our resources.
                </p>
            </div>

            <!-- Quick Support Options -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-16">
                <div class="glass-card rounded-2xl p-8 support-card text-center">
                    <div class="w-16 h-16 bg-blue-500/20 rounded-full flex items-center justify-center mx-auto mb-6">
                        <svg class="w-8 h-8 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold mb-3">FAQ & Guides</h3>
                    <p class="text-gray-300 mb-6">Find quick answers to common questions and step-by-step guides.</p>
                    <button onclick="scrollToSection('faq')" class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-2 rounded-lg transition-colors">
                        Browse FAQ
                    </button>
                </div>

                <div class="glass-card rounded-2xl p-8 support-card text-center">
                    <div class="w-16 h-16 bg-green-500/20 rounded-full flex items-center justify-center mx-auto mb-6">
                        <svg class="w-8 h-8 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold mb-3">Email Support</h3>
                    <p class="text-gray-300 mb-6">Get detailed help from our technical support team via email.</p>
                    <button onclick="scrollToSection('contact')" class="bg-green-500 hover:bg-green-600 text-white px-6 py-2 rounded-lg transition-colors">
                        Send Email
                    </button>
                </div>

                <div class="glass-card rounded-2xl p-8 support-card text-center">
                    <div class="w-16 h-16 bg-purple-500/20 rounded-full flex items-center justify-center mx-auto mb-6">
                        <svg class="w-8 h-8 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192L5.636 18.364M12 2.18l4.95 4.95M12 21.82l-4.95-4.95M12 12l4.95 4.95M12 12l-4.95-4.95"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold mb-3">Live Chat</h3>
                    <p class="text-gray-300 mb-6">Chat with our support team in real-time for instant assistance.</p>
                    <button onclick="openLiveChat()" class="bg-purple-500 hover:bg-purple-600 text-white px-6 py-2 rounded-lg transition-colors">
                        Start Chat
                    </button>
                </div>
            </div>

            <!-- FAQ Section -->
            <div id="faq" class="mb-16">
                <h2 class="text-3xl font-bold text-center mb-12">Frequently Asked Questions</h2>
                <div class="max-w-4xl mx-auto space-y-4">
                    <div class="glass-card rounded-xl p-6 faq-item">
                        <div class="flex items-center justify-between cursor-pointer" onclick="toggleFAQ(this)">
                            <h3 class="text-lg font-semibold">How do I set up my WattAway smart socket?</h3>
                            <svg class="w-6 h-6 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </div>
                        <div class="faq-content mt-4 hidden">
                            <p class="text-gray-300">Setting up your WattAway smart socket is easy! First, plug the socket into a power outlet. Then, download the WattAway app and create an account. Use the app to scan for new devices and follow the on-screen instructions to connect your socket to your Wi-Fi network.</p>
                        </div>
                    </div>

                    <div class="glass-card rounded-xl p-6 faq-item">
                        <div class="flex items-center justify-between cursor-pointer" onclick="toggleFAQ(this)">
                            <h3 class="text-lg font-semibold">Why is my device showing as offline?</h3>
                            <svg class="w-6 h-6 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </div>
                        <div class="faq-content mt-4 hidden">
                            <p class="text-gray-300">If your device shows as offline, check the following: 1) Ensure your Wi-Fi network is working properly, 2) Check if the socket is properly plugged in and has power, 3) Try power cycling the socket by unplugging it for 10 seconds and plugging it back in, 4) Make sure the socket is within range of your Wi-Fi router.</p>
                        </div>
                    </div>

                    <div class="glass-card rounded-xl p-6 faq-item">
                        <div class="flex items-center justify-between cursor-pointer" onclick="toggleFAQ(this)">
                            <h3 class="text-lg font-semibold">How do I monitor my energy usage?</h3>
                            <svg class="w-6 h-6 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </div>
                        <div class="faq-content mt-4 hidden">
                            <p class="text-gray-300">You can monitor your energy usage through the WattAway dashboard. Once logged in, you'll see real-time energy consumption data, daily/weekly/monthly reports, and historical usage patterns. The dashboard also provides insights and recommendations for energy optimization.</p>
                        </div>
                    </div>

                    <div class="glass-card rounded-xl p-6 faq-item">
                        <div class="flex items-center justify-between cursor-pointer" onclick="toggleFAQ(this)">
                            <h3 class="text-lg font-semibold">Can I control multiple devices at once?</h3>
                            <svg class="w-6 h-6 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </div>
                        <div class="faq-content mt-4 hidden">
                            <p class="text-gray-300">Yes! You can create device groups in the app to control multiple sockets simultaneously. You can also set up schedules and automation rules that apply to multiple devices, making it easy to manage your entire smart home ecosystem.</p>
                        </div>
                    </div>

                    <div class="glass-card rounded-xl p-6 faq-item">
                        <div class="flex items-center justify-between cursor-pointer" onclick="toggleFAQ(this)">
                            <h3 class="text-lg font-semibold">What should I do if my device isn't responding?</h3>
                            <svg class="w-6 h-6 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </div>
                        <div class="faq-content mt-4 hidden">
                            <p class="text-gray-300">If your device isn't responding: 1) Check the app and try refreshing, 2) Restart the socket by unplugging it for 30 seconds, 3) Check your internet connection, 4) Try removing and re-adding the device in the app. If the problem persists, contact our support team.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contact Support Section -->
            <div id="contact" class="mb-16">
                <h2 class="text-3xl font-bold text-center mb-12">Contact Our Support Team</h2>
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
                    <!-- Contact Form -->
                    <div class="glass-card rounded-2xl p-8">
                        <h3 class="text-2xl font-bold mb-6">Send us a message</h3>
                        <form id="contactForm" class="space-y-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium mb-2">First Name</label>
                                    <input type="text" id="firstName" class="w-full bg-white/10 border border-white/20 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium mb-2">Last Name</label>
                                    <input type="text" id="lastName" class="w-full bg-white/10 border border-white/20 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-2">Email Address</label>
                                <input type="email" id="email" class="w-full bg-white/10 border border-white/20 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-2">Subject</label>
                                <select id="subject" class="w-full bg-white/10 border border-white/20 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                                    <option value="">Select a topic</option>
                                    <option value="technical">Technical Support</option>
                                    <option value="billing">Billing & Account</option>
                                    <option value="feature">Feature Request</option>
                                    <option value="bug">Bug Report</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-2">Message</label>
                                <textarea id="message" rows="6" class="w-full bg-white/10 border border-white/20 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Describe your issue or question in detail..." required></textarea>
                            </div>
                            <button type="submit" class="w-full bg-blue-500 hover:bg-blue-600 text-white py-3 rounded-lg font-semibold transition-colors">
                                Send Message
                            </button>
                        </form>
                    </div>

                    <!-- Contact Information -->
                    <div class="space-y-8">
                        <div class="glass-card rounded-2xl p-8">
                            <h3 class="text-2xl font-bold mb-6">Get in Touch</h3>
                            <div class="space-y-6">
                                <div class="flex items-start space-x-4">
                                    <div class="w-12 h-12 bg-blue-500/20 rounded-full flex items-center justify-center flex-shrink-0">
                                        <svg class="w-6 h-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <h4 class="font-semibold mb-1">Email Support</h4>
                                        <p class="text-gray-300">support@wattaway.com</p>
                                        <p class="text-sm text-gray-400">We respond within 24 hours</p>
                                    </div>
                                </div>

                                <div class="flex items-start space-x-4">
                                    <div class="w-12 h-12 bg-green-500/20 rounded-full flex items-center justify-center flex-shrink-0">
                                        <svg class="w-6 h-6 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <h4 class="font-semibold mb-1">Phone Support</h4>
                                        <p class="text-gray-300">+1 (555) 123-WATT</p>
                                        <p class="text-sm text-gray-400">Mon-Fri, 9 AM - 6 PM PST</p>
                                    </div>
                                </div>

                                <div class="flex items-start space-x-4">
                                    <div class="w-12 h-12 bg-purple-500/20 rounded-full flex items-center justify-center flex-shrink-0">
                                        <svg class="w-6 h-6 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <h4 class="font-semibold mb-1">Office Address</h4>
                                        <p class="text-gray-300">123 Innovation Drive<br>Silicon Valley, CA 94043</p>
                                        <p class="text-sm text-gray-400">Visit us for in-person support</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Support Hours -->
                        <div class="glass-card rounded-2xl p-8">
                            <h3 class="text-2xl font-bold mb-6">Support Hours</h3>
                            <div class="space-y-3">
                                <div class="flex justify-between">
                                    <span class="text-gray-300">Monday - Friday</span>
                                    <span class="font-semibold">9:00 AM - 6:00 PM PST</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-300">Saturday</span>
                                    <span class="font-semibold">10:00 AM - 4:00 PM PST</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-300">Sunday</span>
                                    <span class="font-semibold">Closed</span>
                                </div>
                            </div>
                            <div class="mt-6 p-4 bg-blue-500/10 border border-blue-500/20 rounded-lg">
                                <p class="text-sm text-blue-400">Emergency support available 24/7 for critical issues</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Resources Section -->
            <div class="mb-16">
                <h2 class="text-3xl font-bold text-center mb-12">Helpful Resources</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div class="glass-card rounded-xl p-6 text-center support-card">
                        <div class="w-12 h-12 bg-orange-500/20 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-6 h-6 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                            </svg>
                        </div>
                        <h3 class="font-semibold mb-2">User Manual</h3>
                        <p class="text-sm text-gray-400 mb-4">Complete setup and usage guide</p>
                        <button class="text-orange-400 hover:text-orange-300 text-sm font-medium">Download PDF</button>
                    </div>

                    <div class="glass-card rounded-xl p-6 text-center support-card">
                        <div class="w-12 h-12 bg-cyan-500/20 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-6 h-6 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <h3 class="font-semibold mb-2">Video Tutorials</h3>
                        <p class="text-sm text-gray-400 mb-4">Step-by-step video guides</p>
                        <button class="text-cyan-400 hover:text-cyan-300 text-sm font-medium">Watch Videos</button>
                    </div>

                    <div class="glass-card rounded-xl p-6 text-center support-card">
                        <div class="w-12 h-12 bg-pink-500/20 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-6 h-6 text-pink-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.367 2.684 3 3 0 00-5.367-2.684z"></path>
                            </svg>
                        </div>
                        <h3 class="font-semibold mb-2">Community Forum</h3>
                        <p class="text-sm text-gray-400 mb-4">Connect with other users</p>
                        <button class="text-pink-400 hover:text-pink-300 text-sm font-medium">Join Forum</button>
                    </div>

                    <div class="glass-card rounded-xl p-6 text-center support-card">
                        <div class="w-12 h-12 bg-indigo-500/20 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-6 h-6 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                        <h3 class="font-semibold mb-2">API Documentation</h3>
                        <p class="text-sm text-gray-400 mb-4">For developers and integrations</p>
                        <button class="text-indigo-400 hover:text-indigo-300 text-sm font-medium">View Docs</button>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        // FAQ Toggle Functionality
        function toggleFAQ(element) {
            const content = element.parentElement.querySelector('.faq-content');
            const arrow = element.querySelector('svg');

            if (content.classList.contains('hidden')) {
                content.classList.remove('hidden');
                arrow.style.transform = 'rotate(180deg)';
            } else {
                content.classList.add('hidden');
                arrow.style.transform = 'rotate(0deg)';
            }
        }

        // Scroll to section function
        function scrollToSection(sectionId) {
            const element = document.getElementById(sectionId);
            if (element) {
                element.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        }

        // Live chat function
        function openLiveChat() {
            alert('Live chat feature would open here. For demo purposes, this is a placeholder.');
        }

        // Contact form submission
        document.getElementById('contactForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const submitBtn = e.target.querySelector('button[type="submit"]');
            const originalText = submitBtn.textContent;

            submitBtn.textContent = 'Sending...';
            submitBtn.disabled = true;

            setTimeout(() => {
                submitBtn.textContent = 'Message Sent!';
                submitBtn.classList.remove('bg-blue-500', 'hover:bg-blue-600');
                submitBtn.classList.add('bg-green-500');

                // Reset form
                e.target.reset();

                setTimeout(() => {
                    submitBtn.textContent = originalText;
                    submitBtn.disabled = false;
                    submitBtn.classList.remove('bg-green-500');
                    submitBtn.classList.add('bg-blue-500', 'hover:bg-blue-600');
                }, 3000);
            }, 2000);
        });

        // Smooth scroll for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
    </script>
</body>
</html>
