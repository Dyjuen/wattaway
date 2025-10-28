<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Wattaway - Login</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Playfair+Display:wght@900&display=swap" rel="stylesheet">

    <!-- Animations -->
    <link rel="stylesheet" href="{{ asset('css/animations.css') }}">
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
    <link rel="icon" href="{{ asset('images/logo.png') }}" type="image/x-icon">

    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        body {
            font-family: 'Inter', sans-serif;
            scroll-behavior: smooth;
            background-color: #070C27;
            overflow-x: hidden;
        }
        .font-brand {
            font-family: 'Playfair Display', serif;
        }
        .login-bg {
            background-image: url('/images/bg-login.png');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            position: relative;
        }
        .login-bg::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            z-index: 1;
        }
        .login-container {
            position: relative;
            z-index: 2;
        }
        .glass-card {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 1.5rem;
        }
        .input-field {
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.2);
            transition: all 0.3s ease;
        }
        .input-field:focus {
            background: rgba(255, 255, 255, 0.12);
            border-color: rgba(168, 85, 247, 0.5);
            box-shadow: 0 0 0 3px rgba(168, 85, 247, 0.1);
        }
        .btn-primary {
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
        }

        .btn-primary:disabled {
            opacity: 0.5;
            cursor: not-allowed;
            transform: none;
        }
        [x-cloak] { display: none !important; }
    </style>
</head>

<body>
    <div class="login-bg min-h-screen flex items-center justify-center p-4">
        <div class="login-container w-full max-w-md">
            <!-- Logo and Title -->
            <div class="text-center mb-8 animate-slide-up">
                <img src="{{ asset('images/logo.png') }}" alt="Wattaway Logo" class="w-16 h-16 mx-auto mb-4 rounded-full">
                <h1 class="font-brand text-4xl font-black text-white mb-2">Wattaway</h1>
                <p class="text-gray-300">Welcome back! Please sign in to continue.</p>
            </div>

            <!-- Login Form -->
            <div class="glass-card rounded-3xl p-8 shadow-2xl animate-slide-up">
                <form x-data="{ email: '', password: '', remember: false }" method="POST" action="{{ route('login') }}" class="space-y-6">
                    @csrf

                    <!-- Email Field -->
                    <div class="animate-slide-up">
                        <label for="email" class="block text-sm font-medium text-gray-300 mb-2">Email Address</label>
                        <input
                            x-model="email"
                            id="email"
                            name="email"
                            type="email"
                            required
                            autocomplete="email"
                            class="input-field w-full px-4 py-3 rounded-xl text-white placeholder-gray-400 focus:outline-none"
                            placeholder="Enter your email"
                        >
                        @error('email')
                            <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Password Field -->
                    <div class="animate-slide-up">
                        <label for="password" class="block text-sm font-medium text-gray-300 mb-2">Password</label>
                        <div class="relative flex items-center">
                            <input
                                id="password"
                                name="password"
                                type="password"
                                required
                                autocomplete="current-password"
                                class="input-field w-full px-4 py-3 pr-12 rounded-xl text-white placeholder-gray-400 focus:outline-none"
                                placeholder="Enter your password"
                            >
                            <button
                                type="button"
                                id="toggle-password-button"
                                class="absolute right-3 text-gray-400 hover:text-white transition-colors"
                            >
                                <svg id="eye-icon" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                                <svg id="eye-slash-icon" class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7 1.274-4.057 5.064-7 9.542-7 .965 0 1.903.163 2.798.457m2.932 9.076a5.98 5.98 0 00.435-2.533m-2.932 9.076l-2.932-2.932m0 0a3 3 0 10-4.243-4.243m4.243 4.243L18.75 15M4.243 4.243L19.5 19.5" />
                                </svg>
                            </button>
                        </div>
                        @error('password')
                            <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Remember Me -->
                    <div class="flex items-center justify-between animate-slide-up">
                        <label class="flex items-center">
                            <input
                                x-model="remember"
                                name="remember"
                                type="checkbox"
                                class="rounded border-gray-600 text-purple-600 focus:ring-purple-500 focus:ring-2"
                            >
                            <span class="ml-2 text-sm text-gray-300">Remember me</span>
                        </label>
                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="text-sm text-purple-400 hover:text-purple-300 transition-colors">
                                Forgot password?
                            </a>
                        @endif
                    </div>

                    <!-- Submit Button -->
                    <button
                        type="submit"
                        class="btn-primary w-full py-3 px-4 rounded-xl text-white font-semibold focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 focus:ring-offset-transparent"
                        :disabled="!email || !password"
                    >
                        <span x-show="!email || !password" class="opacity-50">Sign In</span>
                        <span x-show="email && password" class="animate-pulse-glow">Sign In</span>
                    </button>
                </form>

                <!-- Register Link -->
                <div class="mt-6 text-center animate-slide-up">
                    <p class="text-gray-400">
                        Don't have an account?
                        <a href="{{ route('register') }}" class="text-purple-400 hover:text-purple-300 font-semibold transition-colors">
                            Sign up here
                        </a>
                    </p>
                </div>
            </div>

            <!-- Footer -->
            <div class="text-center mt-8 animate-slide-up">
                <p class="text-gray-400 text-sm">
                    © 2025 Wattaway. Smart energy management for modern living.
                </p>
            </div>
        </div>
    </div>

    <!-- Animation Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Add staggered animation delays
            const animatedElements = document.querySelectorAll('.animate-slide-up');
            animatedElements.forEach((element, index) => {
                element.style.animationDelay = `${index * 0.1}s`;
                element.style.opacity = '1';
                element.style.transform = 'translateY(0)';
            });

            // Add floating animation to logo
            const logo = document.querySelector('img[alt="Wattaway Logo"]');
            if (logo) {
                logo.style.animation = 'float 3s ease-in-out infinite';
            }

            // Form validation feedback
            const form = document.querySelector('form');
            const inputs = form.querySelectorAll('input[type="email"], input[type="password"]');

            inputs.forEach(input => {
                input.addEventListener('focus', function() {
                    this.parentElement.classList.add('ring-2', 'ring-purple-500');
                });

                input.addEventListener('blur', function() {
                    this.parentElement.classList.remove('ring-2', 'ring-purple-500');
                });
            });

            const togglePasswordButton = document.getElementById('toggle-password-button');
            const passwordInput = document.getElementById('password');
            const eyeIcon = document.getElementById('eye-icon');
            const eyeSlashIcon = document.getElementById('eye-slash-icon');

            togglePasswordButton.addEventListener('click', function () {
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);

                eyeIcon.classList.toggle('hidden');
                eyeSlashIcon.classList.toggle('hidden');
            });
        });
    </script>
</body>
</html>
