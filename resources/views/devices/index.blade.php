@extends('layouts.base')

@section('title', 'My Devices - WattAway')

@push('styles')
    <link rel="preload" as="image" href="{{ asset('images/bg-main.png') }}">
    <link rel="stylesheet" href="{{ asset('css/animations.css') }}">
    <style>
        .settings-bg {
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            transition: opacity 0.3s ease-in-out;
        }
        .settings-bg > img {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            z-index: -1;
        }
        .settings-bg.bg-loaded {
            opacity: 1;
        }
        body:not(.bg-loaded) .settings-bg {
            opacity: 0.8;
        }
        main {
            position: relative !important;
            z-index: 1 !important;
        }
        .section-hidden {
            opacity: 0 !important;
            transform: translateY(50px) !important;
            transition: all 0.8s cubic-bezier(0.4, 0, 0.2, 1) !important;
        }
        .section-visible {
            opacity: 1 !important;
            transform: translateY(0) !important;
            transition: all 0.8s cubic-bezier(0.4, 0, 0.2, 1) !important;
        }
        .stagger-item {
            opacity: 0 !important;
            transform: translateY(30px) !important;
            transition: all 0.6s cubic-bezier(0.4, 0, 0.2, 1) !important;
        }
        .stagger-item.stagger-visible {
            opacity: 1 !important;
            transform: translateY(0) !important;
            transition: all 0.6s cubic-bezier(0.4, 0, 0.2, 1) !important;
        }
    </style>
@endpush

@section('body-class', 'antialiased text-white settings-bg min-h-screen')

@section('content')
    <img data-src="{{ asset('images/dist/bg-main.png') }}" src="{{ asset('images/dist/placeholders/bg-main.png') }}" alt="Background" class="lazyload">

    <!--Navbar -->
    <x-navbar />

    <!-- Main Content -->
    <main class="container mx-auto mt-12 px-6 py-8 stagger-container section-hidden" id="main-content">
        <div class="stagger-item mb-8">
            <div class="flex flex-col md:flex-row justify-between md:items-center">
                <h1 class="text-3xl font-bold mb-4 md:mb-0">My Devices</h1>
                <div class="flex gap-4">
                    <a href="{{ route('pairing.scan') }}" class="px-4 py-2 rounded-lg transition-colors bg-blue-500 hover:bg-blue-600 text-white">Add New Device</a>
                    <a href="{{ route('dashboard') }}" class="px-4 py-2 rounded-lg transition-colors bg-white/10 hover:bg-white/20 text-white">Back to Dashboard</a>
                </div>
            </div>
        </div>

        @if($devices->isEmpty())
            <div class="stagger-item">
                <x-glass-card>
                    <p class="text-gray-300 text-center py-12">You haven't added any devices yet.</p>
                </x-glass-card>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach ($devices as $device)
                    <div class="stagger-item">
                        <x-glass-card class="flex flex-col h-full">
                            <div class="relative">
                                <img data-src="{{ asset('images/product.png') }}" src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" alt="WattAway Product Image" class="lazyload rounded-lg mb-4 aspect-video object-cover">
                                <div class="absolute top-2 right-2">
                                    <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $device->status === 'online' ? 'bg-green-200 text-green-800' : 'bg-gray-200 text-gray-800' }}">
                                        {{ ucfirst($device->status) }}
                                    </span>
                                </div>
                            </div>

                            <div class="flex-grow">
                                <h3 class="text-xl font-bold mb-2">{{ $device->name }}</h3>
                                <p class="text-sm text-gray-400 mb-1"><strong>Serial:</strong> {{ $device->serial_number }}</p>
                                <p class="text-sm text-gray-400"><strong>Last Seen:</strong> {{ $device->last_seen_at?->diffForHumans() ?? 'Never' }}</p>
                            </div>

                            <div class="mt-6">
                                <x-button href="{{ route('devices.show', $device) }}" class="w-full">View Details</x-button>
                            </div>
                        </x-glass-card>
                    </div>
                @endforeach
            </div>
        @endif
    </main>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const bgImage = new Image();
        bgImage.onload = function() {
            document.body.classList.add('bg-loaded');
        };
        bgImage.src = "{{ asset('images/bg-main.png') }}";

        setTimeout(() => {
            const main = document.getElementById('main-content');
            if (main) {
                main.classList.add('section-visible');
                main.classList.remove('section-hidden');
            }
            const staggerItems = document.querySelectorAll('.stagger-item');
                staggerItems.forEach((item, index) => {
                setTimeout(() => {
                    item.classList.add('stagger-visible');
                }, index * 100); // Stagger delay
            });
        }, 100);
    });
</script>
@endpush
