@extends('layouts.base')

@section('title', 'Wattaway - Register')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/animations.css') }}">
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
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
        .register-bg {
            background-image: url('/images/bg-sign-up.png');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            position: relative;
        }
        .register-bg::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(7, 12, 39, 0.85) 0%, rgba(132, 20, 92, 0.6) 50%, rgba(7, 12, 39, 0.9) 100%);
            z-index: 1;
        }
        .register-container {
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
            background: linear-gradient(135deg, #a855f7 0%, #ec4899 100%);
            transition: all 0.3s ease;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(168, 85, 247, 0.3);
        }
        .btn-primary:disabled {
            opacity: 0.5;
            cursor: not-allowed;
            transform: none;
        }
        [x-cloak] { display: none !important; }
        .animate-slide-up {
            opacity: 0;
            transform: translateY(30px);
            transition: all 0.6s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .animate-slide-up.active {
            opacity: 1;
            transform: translateY(0);
        }
        .animate-pulse-glow {
            animation: pulse-glow 2s ease-in-out infinite;
        }
    </style>
@endpush

@section('content')
    <div class="register-bg min-h-screen flex items-center justify-center p-4">
        <div class="register-container w-full max-w-md">
            <!-- Logo and Title -->
            <div class="text-center mb-8 animate-slide-up">
                <img src="{{ asset('images/mascot.png') }}" alt="Wattaway Mascot" class="w-20 h-20 mx-auto mb-4 rounded-full">
                <h1 class="font-brand text-4xl font-black text-white mb-2">Join Wattaway</h1>
                <p class="text-gray-300">Create your account to get started with smart energy management.</p>
            </div>

            <!-- Register Form -->
            <div class="glass-card rounded-3xl p-8 shadow-2xl animate-slide-up">
                <form x-data="{ username: '', email: '', password: '', password_confirmation: '' }" method="POST" action="{{ route('register') }}" class="space-y-6">
                    @csrf

                    <!-- Username Field -->
                    <div class="animate-slide-up">
                        <label for="username" class="block text-sm font-medium text-gray-300 mb-2">Username</label>
                        <input
                            x-model="username"
                            id="username"
                            name="username"
                            type="text"
                            required
                            autocomplete="username"
                            class="input-field w-full px-4 py-3 rounded-xl text-white placeholder-gray-400 focus:outline-none"
                            placeholder="Choose a username"
                        >
                        @error('username')
                            <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

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
                        <div class="relative">
                            <input
                                x-model="password"
                                id="password"
                                name="password"
                                type="password"
                                required
                                autocomplete="new-password"
                                class="input-field w-full px-4 py-3 pr-12 rounded-xl text-white placeholder-gray-400 focus:outline-none"
                                placeholder="Create a password"
                            >
                            <button
                                type="button"
                                @click="document.getElementById('password').type = document.getElementById('password').type === 'password' ? 'text' : 'password'"
                                class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-white transition-colors"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                            </button>
                        </div>
                        @error('password')
                            <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Confirm Password Field -->
                    <div class="animate-slide-up">
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-300 mb-2">Confirm Password</label>
                        <div class="relative">
                            <input
                                x-model="password_confirmation"
                                id="password_confirmation"
                                name="password_confirmation"
                                type="password"
                                required
                                autocomplete="new-password"
                                class="input-field w-full px-4 py-3 pr-12 rounded-xl text-white placeholder-gray-400 focus:outline-none"
                                placeholder="Confirm your password"
                            >
                            <button
                                type="button"
                                @click="document.getElementById('password_confirmation').type = document.getElementById('password_confirmation').type === 'password' ? 'text' : 'password'"
                                class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-white transition-colors"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                            </button>
                        </div>
                    </div>



                    <!-- Submit Button -->
                    <button
                        type="submit"
                        class="btn-primary w-full py-3 px-4 rounded-xl text-white font-semibold focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 focus:ring-offset-transparent"
                        :disabled="!username || !email || !password || !password_confirmation"
                    >
                        <span x-show="!username || !email || !password || !password_confirmation" class="opacity-50">Create Account</span>
                        <span x-show="username && email && password && password_confirmation" class="animate-pulse-glow">Create Account</span>
                    </button>
                </form>

                <!-- Login Link -->
                <div class="mt-6 text-center animate-slide-up">
                    <p class="text-gray-400">
                        Already have an account?
                        <a href="{{ route('login') }}" class="text-purple-400 hover:text-purple-300 font-semibold transition-colors">
                            Sign in here
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
@endsection

@push('scripts')
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

            // Add floating animation to mascot
            const mascot = document.querySelector('img[alt="Wattaway Mascot"]');
            if (mascot) {
                mascot.style.animation = 'float 3s ease-in-out infinite';
            }

            // Form validation feedback
            const form = document.querySelector('form');
            const inputs = form.querySelectorAll('input[type="text"], input[type="email"], input[type="password"]');

            inputs.forEach(input => {
                input.addEventListener('focus', function() {
                    this.parentElement.classList.add('ring-2', 'ring-purple-500');
                });

                input.addEventListener('blur', function() {
                    this.parentElement.classList.remove('ring-2', 'ring-purple-500');
                });
            });
        });
    </script>
@endpush
