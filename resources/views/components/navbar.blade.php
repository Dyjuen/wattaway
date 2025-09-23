        <header class="w-full max-w-7xl mx-auto z-20 sticky top-4 px-6">
            <nav class="bg-black/30 backdrop-blur-sm border border-white/10 rounded-full py-3 px-8 shadow-2xl">
                <div class="flex items-center justify-between">
                    <!-- Logo and Brand Name -->
                    <div class="flex items-center space-x-4">
                        <a href="{{ url('/') }}"><img src="{{ asset('images/logo.png') }}" alt="WattAway Logo" class="h-10 w-10 rounded-full"></a>
                        <a href="{{ url('/') }}" class="text-2xl font-bold text-white">WattAway</a>
                    </div>

                    <!-- Navigation Links -->
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
                    @endif

                    <!-- Action Buttons -->
                    <div class="flex items-center space-x-4">
                        <button class="hidden sm:block bg-white text-black font-semibold px-6 py-2 rounded-full action-button">
                            Start Now
                        </button>
                        <a href="{{ url('/faq') }}" class="bg-white/20 p-2 rounded-full hover:bg-white/30 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </a>
                    </div>
                </div>
            </nav>
        </header>
