@extends('layouts.base')

@section('title', '{{ $device->name }} - WattAway')

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
        <div class="stagger-item">
            <x-glass-card>
                <h1 class="text-3xl font-bold mb-6">{{ $device->name }}</h1>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                    <div>
                        <h3 class="text-xl font-semibold mb-2">Device Details</h3>
                        <p class="text-gray-300"><strong>Serial Number:</strong> {{ $device->serial_number }}</p>
                        <p class="text-gray-300"><strong>Hardware ID:</strong> {{ $device->hardware_id }}</p>
                        <p class="text-gray-300"><strong>Status:</strong> {{ ucfirst($device->status) }}</p>
                        <p class="text-gray-300"><strong>Last Seen:</strong> {{ $device->last_seen_at?->diffForHumans() ?? 'Never' }}</p>
                        <p class="text-gray-300"><strong>Activated At:</strong> {{ $device->activated_at?->format('Y-m-d H:i') ?? 'Not Activated' }}</p>
                    </div>
                </div>

                <div class="mt-6 flex justify-end">
                    <x-button tag="a" href="{{ route('dashboard') }}" variant="secondary">Back to Dashboard</x-button>
                </div>
            </x-glass-card>
        </div>

        {{-- Chart Section --}}
        <div class="stagger-item mt-8">
            <x-glass-card>
                <h3 class="text-xl font-semibold mb-4">Real-time Power Consumption (24h)</h3>
                <div class="p-4 rounded-lg">
                    <div id="power-chart" style="height: 300px;"></div>
                </div>
            </x-glass-card>
        </div>
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

<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const deviceId = {{ $device->id }};

        const options = {
            series: [],
            chart: {
                type: 'area',
                height: 300,
                zoom: {
                    enabled: false
                },
                toolbar: {
                    show: false
                },
                animations: {
                    enabled: true,
                    easing: 'linear',
                    dynamicAnimation: {
                        speed: 1000
                    }
                },
                background: 'transparent' // Make chart background transparent
            },
            dataLabels: {
                enabled: false
            },
            stroke: {
                curve: 'smooth',
                width: 2
            },
            xaxis: {
                type: 'datetime',
                labels: {
                    style: {
                        colors: '#E5E7EB' // Lighter gray for dark theme
                    }
                }
            },
            yaxis: {
                title: {
                    text: 'Power (Watts)',
                    style: {
                        color: '#D1D5DB' // Lighter gray
                    }
                },
                labels: {
                    style: {
                        colors: '#E5E7EB' // Lighter gray
                    }
                }
            },
            tooltip: {
                x: {
                    format: 'dd MMM yyyy HH:mm'
                },
                theme: 'dark'
            },
            grid: {
                borderColor: 'rgba(255, 255, 255, 0.1)' // Lighter grid lines
            },
            noData: {
                text: 'Loading data...',
                align: 'center',
                verticalAlign: 'middle',
                style: {
                    color: '#9CA3AF',
                    fontSize: '14px',
                }
            }
        };

        const chart = new ApexCharts(document.querySelector("#power-chart"), options);
        chart.render();

        function fetchData() {
            axios.get(`/api/v1/devices/${deviceId}/readings?range=24h`)
                .then(function (response) {
                    const readings = response.data.data;

                    if (readings.length === 0) {
                        chart.updateOptions({
                            noData: {
                                text: 'No data available for the last 24 hours.',
                            }
                        });
                        return;
                    }

                    chart.updateSeries([
                        {
                            name: 'Channel 1 Power',
                            data: readings.map(r => ({ x: r.timestamp, y: r.channels.find(c => c.channel === 1)?.power || 0 }))
                        },
                        {
                            name: 'Channel 2 Power',
                            data: readings.map(r => ({ x: r.timestamp, y: r.channels.find(c => c.channel === 2)?.power || 0 }))
                        },
                        {
                            name: 'Channel 3 Power',
                            data: readings.map(r => ({ x: r.timestamp, y: r.channels.find(c => c.channel === 3)?.power || 0 }))
                        }
                    ]);
                })
                .catch(function (error) {
                    console.error('Error fetching chart data:', error);
                    chart.updateOptions({
                        noData: {
                            text: 'Could not load chart data.',
                        }
                    })
                });
        }

        fetchData();
        // Refresh data every 30 seconds
        setInterval(fetchData, 30000);
    });
</script>
@endpush
