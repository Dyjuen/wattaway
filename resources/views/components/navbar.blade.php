        <header class="w-full max-w-7xl mx-auto z-20 sticky top-4 px-6">
            <nav class="bg-black/30 backdrop-blur-sm border border-white/10 rounded-3xl py-3 px-8 shadow-2xl">
                <div class="flex items-center justify-between">
                    <!-- Logo and Brand Name -->
                    <div class="flex items-center space-x-4">
                        <a href="{{ url('/') }}"><img data-src="{{ asset('images/dist/logo.png') }}" src="{{ asset('images/dist/placeholders/logo.png') }}" alt="WattAway Logo" class="lazyload h-10 w-10 rounded-full"></a>
                        <a href="{{ url('/') }}" class="text-2xl font-bold text-white">WattAway</a>
                    </div>

                    <!-- Desktop Navigation Links -->
                    @if(request()->is('/'))
                    <div class="hidden md:flex items-center space-x-10 text-gray-300">
                        <a href="#top" data-section="top" class="nav-link hover:text-white transition-colors">Home Page</a>
                        <a href="#product" data-section="product" class="nav-link hover:text-white transition-colors">Product</a>
                        <a href="#about" data-section="about" class="nav-link hover:text-white transition-colors">About Us</a>
                        <a href="#contact" data-section="contact" class="nav-link hover:text-white transition-colors">Contact</a>
                    </div>
                    @elseif(request()->is('/faq'))
                    <div class="hidden md:flex items-center space-x-10 text-gray-300">
                        <a href="{{ url('/#top') }}" data-section="home" class="nav-link hover:text-white transition-colors">Home</a>
                        <a href="/faq#top" data-section="top" class="nav-link hover:text-white transition-colors">FAQ Intro</a>
                        <a href="/faq#faq" data-section="faq" class="nav-link hover:text-white transition-colors">FAQ</a>
                    </div>
                    @elseif(request()->routeIs('dashboard') || request()->routeIs('settings') || request()->routeIs('information') || request()->routeIs('devices.index') || request()->routeIs('devices.show'))
                    <div class="hidden md:flex items-center space-x-10 text-gray-300">
                        <a href="{{ route('dashboard') }}" class="nav-link hover:text-white transition-colors {{ request()->routeIs('dashboard') ? 'text-white font-bold' : '' }}">Dashboard</a>
                        <a href="{{ route('devices.index') }}" class="nav-link hover:text-white transition-colors {{ request()->routeIs('devices.index') ? 'text-white font-bold' : '' }}">My Devices</a>
                        <a href="{{ route('settings') }}" class="nav-link hover:text-white transition-colors {{ request()->routeIs('settings') ? 'text-white font-bold' : '' }}">Settings</a>
                        <a href="{{ route('information') }}" class="nav-link hover:text-white transition-colors {{ request()->routeIs('information') ? 'text-white font-bold' : '' }}">Support</a>
                    </div>
                    @endif

                    <!-- Desktop Action Buttons -->
                    <div class="hidden md:flex items-center space-x-4">
                        @if(request()->routeIs('dashboard') || request()->routeIs('settings') || request()->routeIs('information') || request()->routeIs('devices.index') || request()->routeIs('devices.show'))
                            <form method="POST" action="{{ route('logout') }}" class="inline">
                                @csrf
                                <button type="submit" class="bg-red-500 hover:bg-red-600 text-white font-semibold px-6 py-2 rounded-full transition-colors">
                                    Logout
                                </button>
                            </form>
                        @else
                            <a href="{{ route('login') }}" class="bg-white text-black font-semibold px-6 py-2 rounded-full action-button hover:bg-gray-100 transition-colors">
                                Start Now
                            </a>
                        @endif
                        <a href="{{ url('/faq') }}" class="bg-white/20 p-2 rounded-full hover:bg-white/30 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="white" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </a>
                    </div>

                    <!-- Mobile Menu Button -->
                    <button id="mobile-menu-button" class="md:hidden p-2 rounded-lg hover:bg-white/10 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </button>
                </div>

                <!-- Mobile Menu -->
                <div id="mobile-menu" class="hidden md:hidden mt-4 pb-4 transition-all duration-300 ease-in-out max-h-0 overflow-hidden">
                    <div class="bg-black/20 backdrop-blur-md rounded-2xl p-4 border border-white/10">
                        <!-- Mobile Navigation Links -->
                        @if(request()->is('/'))
                        <div class="space-y-3">
                            <a href="#top" data-section="top" class="block px-3 py-2 text-gray-300 hover:text-white hover:bg-white/10 rounded-lg transition-colors">Home Page</a>
                            <a href="#product" data-section="product" class="block px-3 py-2 text-gray-300 hover:text-white hover:bg-white/10 rounded-lg transition-colors">Product</a>
                            <a href="#about" data-section="about" class="block px-3 py-2 text-gray-300 hover:text-white hover:bg-white/10 rounded-lg transition-colors">About Us</a>
                            <a href="#contact" data-section="contact" class="block px-3 py-2 text-gray-300 hover:text-white hover:bg-white/10 rounded-lg transition-colors">Contact</a>
                        </div>
                        @elseif(request()->is('/faq'))
                        <div class="space-y-3">
                            <a href="{{ url('/#top') }}" data-section="home" class="block px-3 py-2 text-gray-300 hover:text-white hover:bg-white/10 rounded-lg transition-colors">Home</a>
                            <a href="/faq#top" data-section="top" class="block px-3 py-2 text-gray-300 hover:text-white hover:bg-white/10 rounded-lg transition-colors">FAQ Intro</a>
                            <a href="/faq#faq" data-section="faq" class="block px-3 py-2 text-gray-300 hover:text-white hover:bg-white/10 rounded-lg transition-colors">FAQ</a>
                        </div>
                        @elseif(request()->routeIs('dashboard') || request()->routeIs('settings') || request()->routeIs('information') || request()->routeIs('devices.index') || request()->routeIs('devices.show'))
                        <div class="space-y-3">
                            <a href="{{ route('dashboard') }}" class="block px-3 py-2 {{ request()->routeIs('dashboard') ? 'text-white font-bold bg-white/10' : 'text-gray-300 hover:text-white hover:bg-white/10' }} rounded-lg">Dashboard</a>
                            <a href="{{ route('devices.index') }}" class="block px-3 py-2 {{ request()->routeIs('devices.index') ? 'text-white font-bold bg-white/10' : 'text-gray-300 hover:text-white hover:bg-white/10' }} rounded-lg transition-colors">My Devices</a>
                            <a href="{{ route('settings') }}" class="block px-3 py-2 {{ request()->routeIs('settings') ? 'text-white font-bold bg-white/10' : 'text-gray-300 hover:text-white hover:bg-white/10' }} rounded-lg transition-colors">Settings</a>
                            <a href="{{ route('information') }}" class="block px-3 py-2 {{ request()->routeIs('information') ? 'text-white font-bold bg-white/10' : 'text-gray-300 hover:text-white hover:bg-white/10' }} rounded-lg transition-colors">Support</a>
                        </div>
                        @endif

                        <!-- Mobile Action Buttons -->
                        <div class="mt-4 pt-4 border-t border-white/10 space-y-3">
                            @if(request()->routeIs('dashboard') || request()->routeIs('settings') || request()->routeIs('information') || request()->routeIs('devices.index') || request()->routeIs('devices.show'))
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="w-full bg-red-500 hover:bg-red-600 text-white font-semibold py-3 px-4 rounded-lg transition-colors">
                                        Logout
                                    </button>
                                </form>
                            @else
                                <a href="{{ route('login') }}" class="block w-full bg-white text-black font-semibold py-3 px-4 rounded-lg text-center hover:bg-gray-100 transition-colors">
                                    Start Now
                                </a>
                            @endif
                            <a href="{{ url('/faq') }}" class="flex items-center justify-center space-x-3 w-full bg-white/10 hover:bg-white/20 text-white py-3 px-4 rounded-lg transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="white" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span>Help</span>
                            </a>
                        </div>
                    </div>
                </div>
            </nav>
        </header>

        <script>
            // Mobile menu toggle functionality
            document.addEventListener('DOMContentLoaded', function() {
                const mobileMenuButton = document.getElementById('mobile-menu-button');
                const mobileMenu = document.getElementById('mobile-menu');

                if (mobileMenuButton && mobileMenu) {
                    mobileMenuButton.addEventListener('click', function() {
                        const isHidden = mobileMenu.classList.contains('hidden');

                        if (isHidden) {
                            // Show menu
                            mobileMenu.classList.remove('hidden');
                            // Force reflow to ensure transition works
                            mobileMenu.offsetHeight;
                            mobileMenu.classList.remove('max-h-0');
                            mobileMenu.classList.add('max-h-screen');
                        } else {
                            // Hide menu
                            mobileMenu.classList.remove('max-h-screen');
                            mobileMenu.classList.add('max-h-0');
                            // Wait for animation to complete before hiding
                            setTimeout(() => {
                                mobileMenu.classList.add('hidden');
                            }, 300);
                        }

                        // Toggle hamburger icon
                        const svg = mobileMenuButton.querySelector('svg');
                        const path = svg.querySelector('path');

                        if (isHidden) {
                            // Show X icon
                            path.setAttribute('d', 'M6 18L18 6M6 6l12 12');
                        } else {
                            // Show hamburger icon
                            path.setAttribute('d', 'M4 6h16M4 12h16M4 18h16');
                        }
                    });

                    // Close mobile menu when clicking outside
                    document.addEventListener('click', function(event) {
                        if (!mobileMenuButton.contains(event.target) && !mobileMenu.contains(event.target)) {
                            if (!mobileMenu.classList.contains('hidden')) {
                                mobileMenu.classList.remove('max-h-screen');
                                mobileMenu.classList.add('max-h-0');
                                setTimeout(() => {
                                    mobileMenu.classList.add('hidden');
                                }, 300);
                                // Reset to hamburger icon
                                document.querySelector('#mobile-menu-button svg path').setAttribute('d', 'M4 6h16M4 12h16M4 18h16');
                            }
                        }
                    });

                    // Close mobile menu when clicking on a link
                    const mobileMenuLinks = mobileMenu.querySelectorAll('a, button');
                    mobileMenuLinks.forEach(link => {
                        link.addEventListener('click', function() {
                            mobileMenu.classList.remove('max-h-screen');
                            mobileMenu.classList.add('max-h-0');
                            setTimeout(() => {
                                mobileMenu.classList.add('hidden');
                            }, 300);
                            // Reset to hamburger icon
                            document.querySelector('#mobile-menu-button svg path').setAttribute('d', 'M4 6h16M4 12h16M4 18h16');
                        });
                    });
                }
            });
        </script>
