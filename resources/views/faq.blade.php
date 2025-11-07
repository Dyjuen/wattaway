@extends('layouts.base')

@section('title', 'Wattaway - FAQ')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/animations.css') }}">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            scroll-behavior: smooth;
            background-color: #070C27; /* Base background */
        }
        .font-brand {
            font-family: 'Playfair Display', serif;
        }
        .has-radial-gradient {
            background-image: radial-gradient(circle at top, #84145C, #070c27 70%);
        }
        /* Small style tweaks for accordion */
        [x-cloak] { display: none !important; }

        /* FAQ Item Stagger Animation */
        .faq-item.stagger-item {
            opacity: 0;
            transform: translateY(30px);
            transition: all 0.6s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .faq-item.stagger-item.animate-in {
            opacity: 1;
            transform: translateY(0);
        }

        /* Enhanced stagger timing for FAQ items */
        .faq-item.stagger-item:nth-child(1) { transition-delay: 0.1s; }
        .faq-item.stagger-item:nth-child(2) { transition-delay: 0.2s; }
        .faq-item.stagger-item:nth-child(3) { transition-delay: 0.3s; }
        .faq-item.stagger-item:nth-child(4) { transition-delay: 0.4s; }
        .faq-item.stagger-item:nth-child(5) { transition-delay: 0.5s; }
        .faq-item.stagger-item:nth-child(6) { transition-delay: 0.6s; }
        .faq-item.stagger-item:nth-child(7) { transition-delay: 0.7s; }
    </style>
@endpush

@section('content')
    <!-- Background Gradient -->
    <div class="absolute top-0 left-0 w-full h-[900px] has-radial-gradient z-0"></div>
    
    <!-- Navigation  Bar -->
    <x-navbar />
    
    <!-- Main Content -->
    <div class="relative z-10 max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-24 sm:py-32">
        
        <!-- Introduction Section -->
        <section id="top" class="max-w-3xl mx-auto text-center mb-16 animate-slide-up">
            <div class="bg-white/5 backdrop-blur-md rounded-3xl p-8 md:p-12 shadow-lg border border-white/10">
                <h1 class="font-brand text-4xl sm:text-5xl md:text-6xl font-black text-white mb-4">Wattaway</h1>
                <p class="text-base sm:text-lg leading-relaxed text-gray-200">
                    Born from the spirit of innovation and collaboration through the <span class="text-yellow-300 font-semibold">P2MW (Student Entrepreneur Development Program)</span>, our flagship product, the <span class="text-yellow-300 font-semibold">Wattaway Smart Socket</span>, is designed to <span class="text-purple-300 font-bold">help you become more mindful of your electronic usage</span>. With features like real-time voltage detection and customizable timers, Wattaway empowers you to manage your electricity safely and efficiently.
                </p>
            </div>
        </section>

        <!-- FAQ Section -->
        <section id="faq" class="max-w-3xl mx-auto">
            <h2 class="text-center text-3xl sm:text-4xl font-bold text-white mb-2 animate-slide-up">Frequently Asked Questions</h2>
            <p class="text-center text-gray-400 mb-10 animate-slide-up">Find answers to common questions about the Wattaway Smart Socket.</p>
            
            <div class="space-y-4" x-data="{ open: '' }">
                <!-- FAQ Item 1 -->
                <div class="faq-item stagger-item bg-white/10 backdrop-blur-sm rounded-xl border border-white/20 shadow-lg overflow-hidden">
                    <button @click="open = open === 'faq-1' ? '' : 'faq-1'" class="faq-button w-full text-left p-5 flex justify-between items-center focus:outline-none">
                        <span class="text-lg font-semibold text-white">What can Wattaway do?</span>
                        <svg class="w-6 h-6 text-gray-300 faq-chevron transition-transform duration-300" :class="{ 'rotated': open === 'faq-1' }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div x-show="open === 'faq-1'" x-cloak x-transition class="faq-content p-5 pt-0 text-gray-300">
                        <ul class="list-disc list-inside space-y-2">
                            <li><strong>Monitor voltage in real time:</strong> Stay protected from electrical risks like short circuits or unstable voltage.</li>
                            <li><strong>Set timers for each plug:</strong> Want your lamp to turn off at 10 PM? Or your charger to run for just 2 hours? You're in control.</li>
                            <li><strong>Smart control, simple interface:</strong> Just tap to select a plug, set the time, and let Wattaway handle the rest.</li>
                        </ul>
                    </div>
                </div>

                <!-- FAQ Item 2 -->
                <div class="faq-item stagger-item bg-white/10 backdrop-blur-sm rounded-xl border border-white/20 shadow-lg overflow-hidden">
                    <button @click="open = open === 'faq-2' ? '' : 'faq-2'" class="faq-button w-full text-left p-5 flex justify-between items-center focus:outline-none">
                        <span class="text-lg font-semibold text-white">Why choose Wattaway?</span>
                        <svg class="w-6 h-6 text-gray-300 faq-chevron transition-transform duration-300" :class="{ 'rotated': open === 'faq-2' }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div x-show="open === 'faq-2'" x-cloak x-transition class="faq-content p-5 pt-0 text-gray-300">
                        <ul class="list-disc list-inside space-y-2">
                            <li><strong>Save energy:</strong> Cut down on unnecessary electricity use and support a more eco-friendly lifestyle.</li>
                            <li><strong>Protect your devices:</strong> Avoid damage from unstable power with built-in voltage detection.</li>
                            <li><strong>Build awareness:</strong> Understand your electricity habits and adjust them to fit your routine.</li>
                        </ul>
                    </div>
                </div>

                <div class="faq-item stagger-item bg-white/10 backdrop-blur-sm rounded-xl border border-white/20 shadow-lg overflow-hidden">
                    <button @click="open = open === 'faq-3' ? '' : 'faq-3'" class="faq-button w-full text-left p-5 flex justify-between items-center focus:outline-none">
                        <span class="text-lg font-semibold text-white">How do I set up my WattAway smart socket?</span>
                        <svg class="w-6 h-6 text-gray-300 faq-chevron transition-transform duration-300" :class="{ 'rotated': open === 'faq-3' }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div x-show="open === 'faq-3'" x-cloak x-transition class="faq-content p-5 pt-0 text-gray-300">
                        <p>Setting up your WattAway smart socket is easy! First, plug the socket into a power outlet. Then, download the WattAway app and create an account. Use the app to scan for new devices and follow the on-screen instructions to connect your socket to your Wi-Fi network.</p>
                    </div>
                </div>

                <div class="faq-item stagger-item bg-white/10 backdrop-blur-sm rounded-xl border border-white/20 shadow-lg overflow-hidden">
                    <button @click="open = open === 'faq-4' ? '' : 'faq-4'" class="faq-button w-full text-left p-5 flex justify-between items-center focus:outline-none">
                        <span class="text-lg font-semibold text-white">Why is my device showing as offline?</span>
                        <svg class="w-6 h-6 text-gray-300 faq-chevron transition-transform duration-300" :class="{ 'rotated': open === 'faq-4' }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div x-show="open === 'faq-4'" x-cloak x-transition class="faq-content p-5 pt-0 text-gray-300">
                        <p>If your device shows as offline, check the following: 1) Ensure your Wi-Fi network is working properly, 2) Check if the socket is properly plugged in and has power, 3) Try power cycling the socket by unplugging it for 10 seconds and plugging it back in, 4) Make sure the socket is within range of your Wi-Fi router.</p>
                    </div>
                </div>

                <div class="faq-item stagger-item bg-white/10 backdrop-blur-sm rounded-xl border border-white/20 shadow-lg overflow-hidden">
                    <button @click="open = open === 'faq-5' ? '' : 'faq-5'" class="faq-button w-full text-left p-5 flex justify-between items-center focus:outline-none">
                        <span class="text-lg font-semibold text-white">How do I monitor my energy usage?</span>
                        <svg class="w-6 h-6 text-gray-300 faq-chevron transition-transform duration-300" :class="{ 'rotated': open === 'faq-5' }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div x-show="open === 'faq-5'" x-cloak x-transition class="faq-content p-5 pt-0 text-gray-300">
                        <p>You can monitor your energy usage through the WattAway dashboard. Once logged in, you'll see real-time energy consumption data, daily/weekly/monthly reports, and historical usage patterns. The dashboard also provides insights and recommendations for energy optimization.</p>
                    </div>
                </div>

                <div class="faq-item stagger-item bg-white/10 backdrop-blur-sm rounded-xl border border-white/20 shadow-lg overflow-hidden">
                    <button @click="open = open === 'faq-6' ? '' : 'faq-6'" class="faq-button w-full text-left p-5 flex justify-between items-center focus:outline-none">
                        <span class="text-lg font-semibold text-white">Can I control multiple devices at once?</span>
                        <svg class="w-6 h-6 text-gray-300 faq-chevron transition-transform duration-300" :class="{ 'rotated': open === 'faq-6' }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div x-show="open === 'faq-6'" x-cloak x-transition class="faq-content p-5 pt-0 text-gray-300">
                        <p>Yes! You can create device groups in the app to control multiple sockets simultaneously. You can also set up schedules and automation rules that apply to multiple devices, making it easy to manage your entire smart home ecosystem.</p>
                    </div>
                </div>

                <div class="faq-item stagger-item bg-white/10 backdrop-blur-sm rounded-xl border border-white/20 shadow-lg overflow-hidden">
                    <button @click="open = open === 'faq-7' ? '' : 'faq-7'" class="faq-button w-full text-left p-5 flex justify-between items-center focus:outline-none">
                        <span class="text-lg font-semibold text-white">What should I do if my device isn't responding?</span>
                        <svg class="w-6 h-6 text-gray-300 faq-chevron transition-transform duration-300" :class="{ 'rotated': open === 'faq-7' }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div x-show="open === 'faq-7'" x-cloak x-transition class="faq-content p-5 pt-0 text-gray-300">
                        <p>If your device isn't responding: 1) Check the app and try refreshing, 2) Restart the socket by unplugging it for 30 seconds, 3) Check your internet connection, 4) Try removing and re-adding the device in the app. If the problem persists, contact our support team.</p>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection

@push('scripts')
    <!-- Navigation Highlighting Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Set FAQ as active on FAQ page
            const faqLink = document.querySelector('nav a[data-section="faq"]');
            const homeLink = document.querySelector('nav a[data-section="home"]');

            if (faqLink) {
                faqLink.classList.remove('text-gray-300', 'hover:text-white');
                faqLink.classList.add('text-white', 'font-semibold');
            }

            if (homeLink) {
                homeLink.classList.remove('text-white', 'font-semibold');
                homeLink.classList.add('text-gray-300', 'hover:text-white');
            }

            // Simple timeout-based animation for visible elements
            setTimeout(() => {
                const animatedElements = document.querySelectorAll('.animate-slide-up');
                animatedElements.forEach(element => {
                    element.style.opacity = '1';
                    element.style.transform = 'translateY(0)';
                });
            }, 500);

            // Staggered animation for FAQ items - improved timing
            setTimeout(() => {
                const faqItems = document.querySelectorAll('.faq-item.stagger-item');
                console.log('Found FAQ items for staggering:', faqItems.length);

                faqItems.forEach((item, index) => {
                    setTimeout(() => {
                        // Add visible class to trigger CSS animation
                        item.classList.add('animate-in');
                        console.log('Animating FAQ item:', index + 1, 'after', index * 150, 'ms');
                    }, index * 150); // Faster, more consistent staggering
                });
            }, 800); // Start earlier than before
        });
    </script>
@endpush
