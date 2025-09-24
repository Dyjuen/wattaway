<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>WattAway</title>

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
        .has-radial-gradient {
            background-image: radial-gradient(circle, #84145C, #070c27);
        }
        /* Apply this style for screens 768px and wider (md breakpoint) */
        @media (min-width: 768px) {
            .has-radial-gradient {
                background-image: radial-gradient(ellipse, #84145C, #070c27 60%);
                background-size: 100% 900px; /* Full width, 900px height */
                background-position: center;
                background-repeat: no-repeat;
            }
        }
    </style>
</head>

<body class="antialiased bg-[#0B0F2A] text-white">
    <div class="relative min-h-screen flex flex-col items-center">
        <!-- Background Color -->
        <div class="absolute top-0 left-0 w-full h-full bg-[#070C27] z-0"></div>

        <!-- Navigation Bar -->
        <x-navbar />

        <!-- Main content -->
        <main id="top" class="hero-section flex-grow w-full max-w-7xl mx-auto flex flex-col items-center justify-center text-center mt-16 z-10 px-6 stagger-container">
            <!-- Hero Section -->
            <h2 class="text-2xl md:text-3xl text-gray-300 hero-subtitle">Welcome!</h2>
            <h1 class="font-brand text-7xl md:text-9xl font-black my-4 bg-gradient-to-b from-white to-gray-400 bg-clip-text text-transparent hero-title" style="text-shadow: 0 5px 15px rgba(0,0,0,0.3);">
                WattAway
            </h1>

            <div class="relative mt-8 md:mt-4 w-full flex flex-col md:flex-row justify-center items-center">
                <!-- Mascot Image -->
                <img src="{{ asset('images/mascot.png') }}" alt="WattAway Mascot" class="w-96 h-96 md:w-[40rem] md:h-[40rem] object-contain z-0 hero-mascot stagger-item">

                <!-- Left Text Box -->
                <div class="hidden md:block absolute md:left-8 lg:left-[5%] top-1/2 -translate-y-1/2 bg-black/20 backdrop-blur-md border border-white/10 rounded-3xl p-6 max-w-xs shadow-2xl transform z-10 stagger-item">
                    <p class="text-xl leading-relaxed text-gray-200">
                        Which lets you control your <span class="text-pink-400 font-semibold">smart socket</span> directly from here-!
                    </p>
                </div>

                <!-- Right Text Box -->
                <div class="hidden md:block absolute md:right-8 lg:right-[5%] top-1/2 -translate-y-1/2 bg-black/20 backdrop-blur-md border border-white/10 rounded-3xl p-6 max-w-xs shadow-2xl transform z-10 stagger-item">
                     <p class="text-xl leading-relaxed text-gray-200">
                        <span class="text-pink-400 font-semibold">Effortless energy management</span>, right at your fingertips
                    </p>
                </div>

                 <!-- Mobile Text Boxes -->
                 <div class="md:hidden flex flex-col space-y-4 mt-8 w-full items-center">
                    <div class="bg-black/20 backdrop-blur-md border border-white/10 rounded-3xl p-6 max-w-xs shadow-2xl">
                        <p class="text-lg leading-relaxed text-gray-200">
                           which lets you control your <span class="text-pink-400 font-semibold">smart socket</span> directly from here-!
                        </p>
                    </div>
                    <div class="bg-black/20 backdrop-blur-md border border-white/10 rounded-3xl p-6 max-w-xs shadow-2xl">
                        <p class="text-lg leading-relaxed text-gray-200">
                            <span class="text-pink-400 font-semibold">Effortless energy management</span>, right at your fingertips
                        </p>
                    </div>
                </div>
            </div>
        </main>

        <!-- Product Section -->
        <section id="product" class="has-radial-gradient relative w-full z-10 mt-16 py-20 overflow-hidden stagger-container">
            <div class="relative max-w-7xl mx-auto px-4">
                <div class="absolute top-8 right-20 text-black/40 opacity-50 transform -rotate-12" style="font-size: 8rem; filter: blur(2px);"> &#9789; </div>
                <div class="absolute top-4 right-8 text-black/40 opacity-50 transform rotate-12" style="font-size: 5rem; filter: blur(2px);">&#9789;</div>
                <div class="absolute bottom-24 left-16 text-black/40 opacity-50 transform -rotate-12" style="font-size: 6rem; filter: blur(2px);">&#9789;</div>
                <div class="absolute bottom-8 right-32 text-black/40 opacity-50 transform rotate-12" style="font-size: 7rem; filter: blur(2px);">&#9789;</div>

                <h2 class="text-3xl md:text-4xl font-bold text-white mb-12 relative z-10 stagger-item">Wattaway offers smart <br> solutions for modern living</h2>

                <div class="relative z-10 grid grid-cols-1 md:grid-cols-2 gap-10 items-start">
                    
                    <div class="flex flex-col items-center text-center space-y-6 stagger-item">
                        <div class="bg-white/5 backdrop-blur-md w-full max-w-sm h-56 rounded-3xl flex items-center justify-center shadow-lg border border-white/10">
                            <span class="text-gray-400 text-lg">pict of product</span>
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

                    <div class="flex flex-col items-center text-center space-y-6 stagger-item">
                         <div class="bg-white/5 backdrop-blur-md w-full h-56 rounded-3xl flex items-center justify-center shadow-lg border border-white/10">
                            <span class="text-gray-400 text-lg">teaser</span>
                        </div>
                        <div class="bg-[#4a2c2a] p-2 rounded-full w-48 flex items-center justify-between shadow-lg border border-white/10">
                            <button class="bg-[#d47f5a] text-white px-8 py-3 rounded-full font-semibold shadow-md">ON</button>
                            <span class="text-white/50 px-6 font-semibold">OFF</span>
                        </div>
                         <p class="max-w-xs text-gray-300">With remote on/off functionality, Wattaway helps extend the life of your electronic devices—effortlessly and safely</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- About Us Section -->
        <section id="about" class="has-radial-gradient relative w-full z-10 mt-16 py-40 overflow-hidden stagger-container">
            <div class="relative max-w-7xl mx-auto px-4">
                <div class="absolute top-8 left-8 text-black/40 opacity-50 transform -rotate-12" style="font-size: 6rem; filter: blur(2px);">&#9789;</div>
                <div class="absolute bottom-8 right-8 text-black/40 opacity-50 transform rotate-12" style="font-size: 7rem; filter: blur(2px);">&#9789;</div>
                
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
                        <div class="absolute top-1/4 -left-40 bg-[#d47f5a]/30 backdrop-blur-md border border-white/10 rounded-2xl p-4 max-w-[200px] shadow-2xl rotate-12 transform stagger-item">
                            <p class="text-sm text-amber-100">especially during <span class="font-semibold">nighttime hours</span> when risks often go unnoticed.</p>
                        </div>
                         <div class="absolute top-0 -right-48 bg-[#d47f5a]/30 backdrop-blur-md border border-white/10 rounded-2xl p-4 max-w-[250px] shadow-2xl -rotate-12 transform stagger-item">
                            <p class="text-sm text-amber-100">We're not just building a product. We're building a culture of <span class="font-semibold">care, awareness, and futuristic design-!</span></p>
                        </div>
                        <div class="absolute bottom-0 -right-48 bg-white/5 backdrop-blur-md border border-white/10 rounded-2xl p-4 max-w-[220px] shadow-2xl rotate-12 transform stagger-item">
                            <p class="text-sm text-gray-300">To bring our brand to life, we're developing a mascot: <span class="font-semibold text-purple-300">a friendly bat-robot.</span> It's not just a character; it embodies Wattaway's core values of vigilance, protection, and future-forward thinking.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Contact Section -->
        <section id="contact" class="w-full mx-auto z-10 text-center stagger-container">
             <!-- Bat Wing Background PNG is now in the normal document flow -->
             <img src="{{ asset('images/batwings.png') }}" alt="Bat Wing Background" class="mx-auto w-full h-auto opacity-40 stagger-item">
             
             <!-- Text content is now positioned below the image -->
             <div class="relative mt-8">
                <h2 class="text-4xl md:text-5xl font-bold mb-10 stagger-item">Contact Us</h2>
                 <div class="text-xl max-w-2xl mx-auto grid grid-cols-1 md:grid-cols-2 gap-x-12 gap-y-6 text-left stagger-item">
                    <p><span class="font-semibold text-gray-400 w-28 inline-block">no :</span> <span class="text-purple-300">placeholder</span></p>
                    <p><span class="font-semibold text-gray-400 w-28 inline-block">shope acc :</span> <span class="text-purple-300">placeholder</span></p>
                    <p><span class="font-semibold text-gray-400 w-28 inline-block">email :</span> <span class="text-purple-300">placeholder</span></p>
                    <p><span class="font-semibold text-gray-400 w-28 inline-block">tokopedia acc :</span> <span class="text-purple-300">placeholder</span></p>
                    <p><span class="font-semibold text-gray-400 w-28 inline-block">instagram :</span> <span class="text-purple-300">placeholder</span></p>
                 </div>
                 <div class="mt-16 text-black/40 opacity-50 stagger-item" style="font-size: 8rem; filter: blur(2px);">&#9789;</div>
             </div>
        </section>
    </div>

    <!-- Navigation Highlighting Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const navLinks = document.querySelectorAll('nav a[data-section]');
            const sections = ['top', 'product', 'about', 'contact'];

            // Create intersection observer for each section
            const observers = sections.map(sectionId => {
                const section = document.getElementById(sectionId);
                if (!section) return null;

                const observer = new IntersectionObserver((entries) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            // Remove active class from all nav links
                            navLinks.forEach(link => link.classList.remove('text-white', 'font-semibold'));
                            navLinks.forEach(link => link.classList.add('text-gray-300'));

                            // Add active class to current nav link
                            const activeLink = document.querySelector(`nav a[data-section="${sectionId}"]`);
                            if (activeLink) {
                                activeLink.classList.remove('text-gray-300');
                                activeLink.classList.add('text-white', 'font-semibold');
                            }
                        }
                    });
                }, {
                    threshold: 0.3, // Trigger when 30% of the section is visible
                    rootMargin: '-50px 0px -50px 0px' // Account for sticky navbar
                });

                // Store the section reference and observe it
                observer.section = section;
                observer.observe(section);
                
                return observer;
            }).filter(Boolean);

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
            const animatedSections = document.querySelectorAll('section, .hero-section, .intro-section');
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
                            let delay = 0;
                            // Mascot appears first (index 0)
                            if (child.classList.contains('hero-mascot')) {
                                delay = 0;
                            }
                            // Text boxes appear after mascot with staggered delay
                            else {
                                delay = 300 + (index * 200); // 300ms after mascot, then 200ms between text boxes
                            }

                            setTimeout(() => {
                                child.classList.add('stagger-visible');
                            }, delay);
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
        });
    </script>
</body>
</html>



