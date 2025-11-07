@extends('layouts.app')

@section('content')
<div class="antialiased text-white dashboard-bg min-h-screen">
    <div class="relative min-h-screen">
        <!-- Navigation Bar -->
        <x-navbar />

        <!-- Main Content -->
        <main class="container mx-auto px-6 py-8 pt-24">
            <div class="flex justify-center">
                <div class="w-full max-w-2xl">
                    <div class="glass-card rounded-2xl p-8 stagger-item">
                        <h1 class="text-3xl md:text-4xl font-bold mb-4 text-center gradient-text">Confirm Device Pairing</h1>
                        <p class="text-gray-300 text-lg text-center mb-8">You are about to add a new WattAway device to your account.</p>

                        {{-- General Error Display --}}
                        @if ($errors->any() || session('error'))
                            <div class="bg-red-500/20 text-red-300 p-4 rounded-lg mb-6 border border-red-500/30">
                                <ul>
                                    @if(session('error'))
                                        <li>{{ session('error') }}</li>
                                    @else
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    @endif
                                </ul>
                            </div>
                        @endif

                        <form id="pair-form" method="POST" action="{{ route('pairing.pair') }}" class="space-y-6">
                            @csrf

                            <input type="hidden" name="token" value="{{ $token }}">

                            <div>
                                <label for="device_name" class="block text-sm font-medium text-gray-300 mb-2">Device Name (Optional)</label>
                                <input id="device_name" type="text" 
                                       class="w-full bg-white/10 border rounded-lg py-3 px-4 text-white placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:outline-none @error('device_name') border-red-500/50 @enderror" 
                                       name="device_name" value="{{ old('device_name') }}" 
                                       placeholder="e.g., Living Room Lamp" autocomplete="device_name" autofocus>

                                @error('device_name')
                                    <span class="text-red-400 text-sm mt-2" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div>
                                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded-lg transition-colors duration-300 text-lg">
                                    {{ __('Pair Device') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>
                            @endsection