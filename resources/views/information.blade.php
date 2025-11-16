@extends('layouts.base')

@section('title', 'Support & Information - WattAway')

@push('styles')
    <link rel="preload" as="image" href="{{ asset('images/bg-main.png') }}">
    <link rel="stylesheet" href="{{ asset('css/animations.css') }}">
    <style>
        .info-bg {
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            transition: opacity 0.3s ease-in-out;
        }
        .info-bg > img {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            z-index: -1;
        }
        .info-bg.bg-loaded {
            opacity: 1;
        }
        body:not(.bg-loaded) .info-bg {
            opacity: 0.8;
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
        main {
            position: relative !important;
            z-index: 1 !important;
        }
    </style>
@endpush

@section('body-class', 'antialiased text-white info-bg min-h-screen')

@section('content')
    <img data-src="{{ asset('images/dist/bg-main.png') }}" src="{{ asset('images/dist/placeholders/bg-main.png') }}" alt="Background" class="lazyload">
    <div class="relative min-h-screen">
        <!--Navbar -->
        <x-navbar />

        <!-- Main Content -->
        <main class="container mx-auto px-6 py-8 stagger-container">
            <!-- Hero Section -->
            <div class="text-center mb-16 stagger-item">
                <h1 class="text-4xl md:text-6xl mt-12 font-bold mb-4">
                    How can we help you <span class="gradient-text">today?</span>
                </h1>
                <p class="text-xl text-gray-300 max-w-3xl mx-auto">
                    Get the support you need for your WattAway smart devices. Browse our knowledge base or contact our support team.
                </p>
            </div>

            <!-- FAQ Section -->
            <div id="faq" class="mb-16 stagger-item">
                <h2 class="text-3xl font-bold text-center mb-12">Frequently Asked Questions</h2>
                <div class="max-w-4xl mx-auto space-y-4">
                    <div class="glass-card rounded-xl p-6 faq-item stagger-item">
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

                    <div class="glass-card rounded-xl p-6 faq-item stagger-item">
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

                    <div class="glass-card rounded-xl p-6 faq-item stagger-item">
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

                    <div class="glass-card rounded-xl p-6 faq-item stagger-item">
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

                    <div class="glass-card rounded-xl p-6 faq-item stagger-item">
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
            <div id="contact" class="mb-16 stagger-item">
                <h2 class="text-3xl font-bold text-center mb-12">Contact Our Support Team</h2>
                    <!-- Contact Information -->
                    <div class="space-y-8 stagger-item">
                        <div class="glass-card rounded-2xl p-8 stagger-item">
                            <h3 class="text-2xl font-bold mb-6">Get in Touch</h3>
                            <div class="space-y-6">
                                <div class="flex items-start space-x-4 stagger-item">
                                    <div class="w-12 h-12 bg-blue-500/20 rounded-full flex items-center justify-center flex-shrink-0">
                                        <svg class="w-6 h-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <h4 class="font-semibold mb-1">Email Support</h4>
                                        <a href="mailto:wattaway.project@gmail.com" class="text-gray-300">wattaway.project@gmail.com</a>
                                        <p class="text-sm text-gray-400">We respond within 24 hours</p>
                                    </div>
                                </div>

                                <div class="flex items-start space-x-4 stagger-item">
                                    <div class="w-12 h-12 bg-green-500/20 rounded-full flex items-center justify-center flex-shrink-0">
                                    <svg class="w-6 h-6 text-green-400" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893A11.821 11.821 0 0020.051 3.488"/></svg>
                                    </div>
                                    <div>
                                        <h4 class="font-semibold mb-1">Whatsapp Support</h4>
                                        <a href="https://wa.me/6285119882237" class="text-gray-300">+62 851-1988-2237</a>
                                        <p class="text-sm text-gray-400">Mon-Fri, 9 AM - 6 PM PST</p>
                                    </div>
                                </div>

                                <div class="flex items-start space-x-4 stagger-item">
                                    <div class="w-12 h-12 bg-purple-500/20 rounded-full flex items-center justify-center flex-shrink-0">
                                        <svg class="w-6 h-6 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <h4 class="font-semibold mb-1">Office Address</h4>
                                        <p class="text-gray-300">Politeknik Negeri Jakarta<br>Universitas Indonesia, Jl. Prof. DR. G.A. Siwabessy, Kukusan, Kecamatan Beji, Kota Depok, Jawa Barat 16425</p>
                                        <p class="text-sm text-gray-400">Visit us for in-person support</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Support Hours -->
                        <div class="glass-card rounded-2xl p-8 stagger-item">
                            <h3 class="text-2xl font-bold mb-6">Support Hours</h3>
                            <div class="space-y-3">
                                <div class="flex justify-between stagger-item">
                                    <span class="text-gray-300">Monday - Friday</span>
                                    <span class="font-semibold">9:00 AM - 6:00 PM PST</span>
                                </div>
                                <div class="flex justify-between stagger-item">
                                    <span class="text-gray-300">Saturday</span>
                                    <span class="font-semibold">10:00 AM - 4:00 PM PST</span>
                                </div>
                                <div class="flex justify-between stagger-item">
                                    <span class="text-gray-300">Sunday</span>
                                    <span class="font-semibold">Closed</span>
                                </div>
                            </div>
                            <div class="mt-6 p-4 bg-blue-500/10 border border-blue-500/20 rounded-lg stagger-item">
                                <p class="text-sm text-blue-400">Emergency support available 24/7 for critical issues</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
@endsection

@push('scripts')
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

        // Animation System - Initialize after DOM is loaded
        document.addEventListener('DOMContentLoaded', function() {
            // Immediate background image preloader
            const bgImage = new Image();
            bgImage.onload = function() {
                document.body.classList.add('bg-loaded');
            };
            bgImage.src = "{{ asset('images/bg-main.png') }}";

            console.log('Animation system initializing...');

            const sectionObserver = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        console.log('Section intersecting:', entry.target);
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
            console.log('Found main sections:', animatedSections.length);
            animatedSections.forEach(section => {
                section.classList.add('section-hidden');
                sectionObserver.observe(section);
                console.log('Observing section:', section);
            });

            // Staggered animation for child elements
            const staggerObserver = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        console.log('Stagger container intersecting:', entry.target);
                        const children = entry.target.querySelectorAll('.stagger-item');
                        console.log('Found stagger children:', children.length);
                        children.forEach((child, index) => {
                            setTimeout(() => {
                                child.classList.add('stagger-visible');
                                console.log('Animating child:', index, child);
                            }, index * 80);
                        });
                    }
                });
            }, {
                threshold: 0.1
            });

            // Observe elements with staggered children
            const staggerContainers = document.querySelectorAll('.stagger-container');
            console.log('Found stagger containers:', staggerContainers.length);
            staggerContainers.forEach(container => {
                staggerObserver.observe(container);
                console.log('Observing stagger container:', container);
            });

            console.log('Animation system initialized');
        });
    </script>
@endpush
