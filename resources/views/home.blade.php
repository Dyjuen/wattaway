@extends('layouts.base')

@section('title', 'WattAway - Effortless Energy Management')

@section('content')
<div class="loading-overlay">
    <img src="{{ asset('images/wattaway.png') }}" alt="WattAway Logo" class="loading-logo h-28">
</div>
<div class="antialiased bg-[#070C27] text-white">
    <div class="relative min-h-screen flex flex-col items-center">
        <!-- Background Color -->
        <div class="absolute top-0 left-0 w-full h-full z-0"></div>

        <!-- Navigation Bar -->
        <x-navbar />

        <!-- Main content -->
        <main id="top" class="hero-section flex-grow w-full max-w-7xl mx-auto flex flex-col items-center justify-center text-center mt-16 z-10 px-6 stagger-container">
            <!-- Hero Section -->
            <h2 class="text-2xl md:text-3xl text-gray-300 hero-subtitle">Welcome!</h2>
            <h1 class="font-brand md:text-9xl font-black my-4 hero-title">
                <img data-src="{{ asset('images/dist/wattaway.png') }}" src="{{ asset('images/dist/placeholders/wattaway.png') }}" alt="WattAway" class="lazyload h-20 md:h-28 lg:h-36 w-auto inline-block" width="959" height="144" decoding="async">
            </h1>

            <div class="relative md:mt-4 w-full flex flex-col md:flex-row justify-center items-center">
                <!-- Mascot Image -->
                <img data-src="{{ asset('images/dist/mascot.png') }}" src="{{ asset('images/dist/placeholders/mascot.png') }}" alt="WattAway Mascot" class="lazyload w-[28rem] h-[28rem] md:w-[50rem] md:h-[50rem] object-contain z-0 hero-mascot stagger-item" width="800" height="800" decoding="async">

                <!-- Left Text Box -->
                <div class="hidden md:block absolute md:left-8 lg:left-[5%] top-1/2 -translate-y-1/4 bg-black/20 backdrop-blur-md border border-white/10 rounded-3xl p-6 max-w-xs shadow-2xl transform z-10 stagger-item hover:bg-black/30 hover:border-white/20 hover:shadow-3xl hover:scale-105 transition-all duration-300 ease-out">
                    <p class="text-xl leading-relaxed text-gray-200">
                        Which lets you control your <span class="text-pink-400 font-semibold">smart socket</span> directly from here-!
                    </p>
                </div>

                <!-- Right Text Box -->
                <div class="hidden md:block absolute md:right-8 lg:right-[5%] top-1/2 -translate-y-1/4 bg-black/20 backdrop-blur-md border border-white/10 rounded-3xl p-6 max-w-xs shadow-2xl transform z-10 stagger-item hover:bg-black/30 hover:border-white/20 hover:shadow-3xl hover:scale-105 transition-all duration-300 ease-out">
                     <p class="text-xl leading-relaxed text-gray-200">
                        <span class="text-pink-400 font-semibold">Effortless energy management</span>, right at your fingertips
                    </p>
                </div>

                 <!-- Mobile Text Boxes -->
                 <div class="md:hidden flex flex-col space-y-4 mt-8 w-full items-center">
                    <div class="bg-black/20 backdrop-blur-md border border-white/10 rounded-3xl p-6 max-w-xs shadow-2xl hover:scale-105 transition-all duration-300 ease-out">
                        <p class="text-lg leading-relaxed text-gray-200">
                            <span class="text-pink-400 font-semibold">Effortless energy management</span>, right at your fingertips
                        </p>
                    </div>
                    <div class="bg-black/20 backdrop-blur-md border border-white/10 rounded-3xl p-6 max-w-xs shadow-2xl hover:scale-105 transition-all duration-300 ease-out">
                        <p class="text-lg leading-relaxed text-gray-200">
                           which lets you control your <span class="text-pink-400 font-semibold">smart socket</span> directly from here-!
                        </p>
                    </div>
                    
                </div>
            </div>
        </main>

        <!-- Product Section -->
        <section id="product" class="has-radial-gradient relative w-full z-10 mt-16 py-20 overflow-hidden stagger-container">
            <div class="relative max-w-7xl mx-auto px-4">
                <div class="absolute top-8 right-20 text-black/40 opacity-50 transform -rotate-12 product-smiley-1"> &#9789; </div>
                <div class="absolute top-4 right-8 text-black/40 opacity-50 transform rotate-12 product-smiley-2">&#9789;</div>
                <div class="absolute bottom-24 left-16 text-black/40 opacity-50 transform -rotate-12 product-smiley-3">&#9789;</div>
                <div class="absolute bottom-8 right-32 text-black/40 opacity-50 transform rotate-12 product-smiley-4">&#9789;</div>

                <h2 class="text-3xl md:text-4xl font-bold text-white mb-12 relative z-10 stagger-item">Wattaway offers smart <br> solutions for modern living</h2>

                                <div x-data="{ currentSlide: 0, slides: 2 }" class="relative z-10 w-full">
                                    <!-- Desktop: Grid -->
                                    <div class="hidden md:grid md:grid-cols-2 gap-10 items-start">
                                        <!-- Card 1 -->
                                        <div class="flex flex-col items-center text-center space-y-6 stagger-item">
                                            <div class="bg-white/5 backdrop-blur-md w-full max-w-sm h-56 rounded-3xl flex items-center justify-center shadow-lg border border-white/10 overflow-hidden hover:bg-white/10 hover:border-white/20 hover:shadow-3xl hover:scale-105 transition-all duration-300 ease-out">
                                                <img data-src="{{ asset('images/dist/product.png') }}" src="{{ asset('images/dist/placeholders/product.png') }}" alt="Wattaway Product" class="lazyload w-full h-full object-cover" width="384" height="224" decoding="async">
                                            </div>
                                            <div class="relative w-36 h-36 flex items-center justify-center">
                                                <svg class="w-full h-full" viewBox="0 0 100 100">
                                                    <circle class="text-white/10" stroke-width="8" stroke="currentColor" fill="transparent" r="45" cx="50" cy="50" />
                                                    <circle class="text-purple-400" stroke-width="8" stroke="currentColor" fill="transparent" r="45" cx="50" cy="50" stroke-dasharray="283" stroke-dashoffset="206" stroke-linecap="round" transform="rotate(-90 50 50)" />
                                                </svg>
                                                <span class="absolute text-3xl font-bold text-white">27%</span>
                                            </div>
                                            <p class="max-w-xs text-gray-300">Our smart socket is designed to monitor daily electricity usage and give you full control directly from your smartphone</p>
                                        </div>
                                        <!-- Card 2 -->
                                        <div class="flex flex-col items-center text-center space-y-6 stagger-item">
                                            <div x-data="{ currentSlide: 0, slides: 3 }" x-init="setInterval(() => { currentSlide = (currentSlide + 1) % slides }, 5000)" class="bg-white/5 backdrop-blur-md w-full max-w-sm h-56 rounded-3xl flex items-center justify-center shadow-lg border border-white/10 overflow-hidden relative hover:bg-white/10 hover:border-white/20 hover:shadow-3xl hover:scale-105 transition-all duration-300 ease-out">
                                                                            <!-- Sliding Gallery Container -->
                                                                            <div class="w-full h-full relative">
                                                                                <div class="flex transition-transform duration-500 ease-in-out h-full" :style="`transform: translateX(-${currentSlide * 100}%)`">
                                                                                    <div class="w-full h-full flex-shrink-0">
                                                                                        <img data-src="{{ asset('images/dist/gallery1.jpeg') }}" src="{{ asset('images/dist/placeholders/gallery1.jpeg') }}" alt="Gallery Image 1" class="lazyload w-full h-full object-cover" width="384" height="224" decoding="async">
                                                                                    </div>
                                                                                    <div class="w-full h-full flex-shrink-0">
                                                                                        <img data-src="{{ asset('images/dist/gallery2.jpeg') }}" src="{{ asset('images/dist/placeholders/gallery2.jpeg') }}" alt="Gallery Image 2" class="lazyload w-full h-full object-cover" width="384" height="224" decoding="async">
                                                                                    </div>
                                                                                    <div class="w-full h-full flex-shrink-0">
                                                                                        <img data-src="{{ asset('images/dist/gallery3.jpeg') }}" src="{{ asset('images/dist/placeholders/gallery3.jpeg') }}" alt="Gallery Image 3" class="lazyload w-full h-full object-cover" width="384" height="224" decoding="async">
                                                                                    </div>
                                                                                </div>
                                                                                <!-- Navigation Dots -->
                                                                                <div class="absolute bottom-4 left-1/2 transform -translate-x-1/2 flex space-x-2">
                                                                                    <template x-for="i in slides" :key="i">
                                                                                        <button @click="currentSlide = i - 1" class="w-3 h-3 rounded-full transition-colors" :class="{'bg-white': currentSlide === i - 1, 'bg-white/50 hover:bg-white/80': currentSlide !== i - 1}"></button>
                                                                                    </template>
                                                                                </div>
                                                                            </div>
                                                                        </div>                                            
                                                <div x-data="{ isOn: true }" @click="isOn = !isOn" class="toggle-switch" :class="{ 'on': isOn, 'off': !isOn }">
                                                <div class="toggle-switch-handle">
                                                    <span x-text="isOn ? 'ON' : 'OFF'"></span>
                                                </div>
                                                <span class="toggle-switch-text on" x-show="!isOn">ON</span>
                                                <span class="toggle-switch-text off" x-show="isOn">OFF</span>
                                            </div>
                                            <p class="max-w-xs text-gray-300">With remote on/off functionality, Wattaway helps extend the life of your electronic devices—effortlessly and safely</p>
                                        </div>
                                    </div>
        </section>

        <!-- About Us Section -->
        <section id="about" class="has-radial-gradient relative w-full z-10 mt-16 py-40 overflow-hidden stagger-container">
            <div class="relative max-w-7xl mx-auto px-4">
                <div class="absolute top-8 left-8 text-black/40 opacity-50 transform -rotate-12 about-smiley-1">&#9789;</div>
                <div class="absolute bottom-8 right-8 text-black/40 opacity-50 transform rotate-12 about-smiley-2">&#9789;</div>
                
                <div class="relative z-10 max-w-3xl mx-auto text-center">
                    <div class="bg-white/5 backdrop-blur-md rounded-3xl p-8 md:p-12 shadow-lg border border-white/10 stagger-item">
                        <p class="text-xl md:text-2xl leading-relaxed text-gray-200">
                            Wattaway was born from the spirit of innovation and collaboration through the <span class="text-yellow-300 font-semibold">P2MW (Student Entrepreneur Development Program)</span>.
                            <br><br>
                            Our flagship product, the <span class="text-yellow-300 font-semibold">Wattaway Smart Socket</span>, is designed to <span class="text-purple-300 font-bold">help people become more mindful of their electronic usage</span>. With features like real-time voltage detection and customizable timers, Wattaway empowers users to manage their electricity safely and efficiently
                        </p>
                    </div>
                    
                    <!-- Floating text boxes -->
                    <div class="hidden md:block">
                        <div class="absolute top-1/4 -left-40 bg-[#d47f5a]/30 backdrop-blur-md border border-white/10 rounded-2xl p-4 max-w-[200px] shadow-2xl rotate-12 transform stagger-item hover:scale-110 hover:bg-[#d47f5a]/40 hover:shadow-3xl hover:border-white/20 transition-all duration-300 ease-out">
                            <p class="text-sm text-amber-100">especially during <span class="font-semibold">nighttime hours</span> when risks often go unnoticed.</p>
                        </div>
                         <div class="absolute top-0 -right-48 bg-[#d47f5a]/30 backdrop-blur-md border border-white/10 rounded-2xl p-4 max-w-[250px] shadow-2xl -rotate-12 transform stagger-item hover:scale-110 hover:bg-[#d47f5a]/40 hover:shadow-3xl hover:border-white/20 transition-all duration-300 ease-out">
                            <p class="text-sm text-amber-100">We're not just building a product. We're building a culture of <span class="font-semibold">care, awareness, and futuristic design-!</span></p>
                        </div>
                        <div class="absolute bottom-0 -right-48 bg-white/5 backdrop-blur-md border border-white/10 rounded-2xl p-4 max-w-[220px] shadow-2xl rotate-12 transform stagger-item hover:scale-110 hover:bg-white/10 hover:shadow-3xl hover:border-white/20 transition-all duration-300 ease-out">
                            <p class="text-sm text-gray-300">To bring our brand to life, we're developing a mascot: <span class="font-semibold text-purple-300">a friendly bat-robot.</span> It's not just a character; it embodies Wattaway's core values of vigilance, protection, and future-forward thinking.</p>
                        </div>
                    </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Contact Section -->
        <section id="contact" class="relative w-full z-10 mt-16 py-20 text-center stagger-container">
             <!-- Bat Wing Background PNG is now in the normal document flow -->
             <img data-src="{{ asset('images/dist/batwings.png') }}" src="{{ asset('images/dist/placeholders/batwings.png') }}" alt="Bat Wing Background" class="lazyload mx-auto w-full h-auto opacity-40 stagger-item" decoding="async">
             
             <!-- Text content is now positioned below the image -->
             <div class="relative mt-8">
                <h2 class="text-4xl md:text-5xl font-bold mb-10 stagger-item">Contact Us</h2>
                 <div class="px-6 text-xl max-w-4xl mx-auto grid grid-cols-1 md:grid-cols-2 gap-6 text-left stagger-item">
                    <!-- Phone -->
                    <a href="https://wa.me/6285119882237" class="group flex items-center p-4 bg-white/5 backdrop-blur-sm border border-white/10 rounded-2xl hover:bg-white/10 hover:border-white/20 hover:scale-105 transition-all duration-300">
                        <div class="w-12 h-12 bg-green-500/20 rounded-full flex items-center justify-center mr-4 group-hover:bg-green-500/30 transition-colors">
                            <svg class="w-6 h-6 text-green-400" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893A11.821 11.821 0 0020.051 3.488"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm text-gray-400 mb-1">Phone Number</p>
                            <p class="text-purple-300 font-medium">+62 851-1988-2237</p>
                        </div>
                    </a>

                    <!-- Shopee -->
                    <a href="http://shopee.co.id/wattaway" class="group flex items-center p-4 bg-white/5 backdrop-blur-sm border border-white/10 rounded-2xl hover:bg-white/10 hover:border-white/20 hover:scale-105 transition-all duration-300">
                        <div class="w-12 h-12 bg-orange-500/20 rounded-full flex items-center justify-center mr-4 group-hover:bg-orange-500/30 transition-colors">
                            <img data-src="{{ asset('images/dist/shopee.svg') }}" src="{{ asset('images/dist/placeholders/shopee.svg') }}" alt="Shopee" class="lazyload w-8 h-8" width="32" height="32" decoding="async">
                        </div>
                        <div>
                            <p class="text-sm text-gray-400 mb-1">Shopee Store</p>
                            <p class="text-purple-300 hover:text-purple-200 font-medium transition-colors">shopee.co.id/wattaway</p>
                        </div>
                    </a>

                    <!-- Email -->
                    <a href="mailto:wattaway.project@gmail.com" class="group flex items-center p-4 bg-white/5 backdrop-blur-sm border border-white/10 rounded-2xl hover:bg-white/10 hover:border-white/20 hover:scale-105 transition-all duration-300">
                        <div class="w-12 h-12 bg-blue-500/20 rounded-full flex items-center justify-center mr-4 group-hover:bg-blue-500/30 transition-colors">
                            <svg class="w-6 h-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm text-gray-400 mb-1">Email Address</p>
                            <p class="text-purple-300 hover:text-purple-200 font-medium transition-colors">wattaway.project@gmail.com</p>
                        </div>
                    </a>

                    <!-- Tokopedia -->
                    <div class="group flex items-center p-4 bg-white/5 backdrop-blur-sm border border-white/10 rounded-2xl hover:bg-white/10 hover:border-white/20 transition-all duration-300">
                        <div class="w-12 h-12 bg-red-500/20 rounded-full flex items-center justify-center mr-4 group-hover:bg-red-500/30 transition-colors">
                            <img data-src="{{ asset('images/dist/tokopedia.svg') }}" src="{{ asset('images/dist/placeholders/tokopedia.svg') }}" alt="Tokopedia" class="lazyload w-8 h-8" width="32" height="32" decoding="async">
                        </div>
                        <div>
                            <p class="text-sm text-gray-400 mb-1">Tokopedia Store</p>
                            <p class="text-gray-500 font-medium">Coming Soon</p>
                        </div>
                    </div>

                    <!-- Instagram -->
                    <a href="https://instagram.com/wattaway.project" class="group flex items-center p-4 bg-white/5 backdrop-blur-sm border border-white/10 rounded-2xl hover:bg-white/10 hover:border-white/20 hover:scale-105 transition-all duration-300 md:col-span-2 max-w-md mx-auto">
                        <div class="w-12 h-12 bg-pink-500/20 rounded-full flex items-center justify-center mr-4 group-hover:bg-pink-500/30 transition-colors">
                            <svg class="w-6 h-6 text-pink-400" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm text-gray-400 mb-1">Instagram</p>
                            <p class="text-purple-300 hover:text-purple-200 font-medium transition-colors">@wattaway.project</p>
                        </div>
                    </a>
                 </div>
                 <div class="mt-16 text-black/40 opacity-50 stagger-item contact-smiley">&#9789;</div>
             </div>
        </section>
    </div>
</div>
@endsection

@push('scripts')
    <!-- Navigation Highlighting Script -->
    <script>
        window.addEventListener('load', function() {
            document.body.classList.add('loaded');
        });
    </script>
@endpush