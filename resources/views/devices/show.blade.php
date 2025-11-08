@extends('layouts.base')

@section('body-class', 'bg-gray-100 dark:bg-gray-900')

@section('content')
<div class="container mx-auto px-4 py-8">
    <x-glass-card>
        <h1 class="text-3xl font-bold mb-6">{{ $device->name }}</h1>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
            <div>
                <h3 class="text-xl font-semibold mb-2">Device Details</h3>
                <p class="text-gray-600 dark:text-gray-400"><strong>Serial Number:</strong> {{ $device->serial_number }}</p>
                <p class="text-gray-600 dark:text-gray-400"><strong>Hardware ID:</strong> {{ $device->hardware_id }}</p>
                <p class="text-gray-600 dark:text-gray-400"><strong>Status:</strong> {{ ucfirst($device->status) }}</p>
                <p class="text-gray-600 dark:text-gray-400"><strong>Last Seen:</strong> {{ $device->last_seen_at?->diffForHumans() ?? 'Never' }}</p>
                <p class="text-gray-600 dark:text-gray-400"><strong>Activated At:</strong> {{ $device->activated_at?->format('Y-m-d H:i') ?? 'Not Activated' }}</p>
            </div>
            @if ($device->provisioningToken)
            <div class="flex flex-col items-center justify-center">
                <h3 class="text-xl font-semibold mb-2">Pairing QR Code</h3>
                <img src="{{ route('admin.provisioning-tokens.qr', $device->provisioningToken) }}" alt="QR Code for {{ $device->provisioningToken->token }}" class="w-48 h-48 border border-gray-300 p-2 rounded-lg">
                <x-button tag="a" href="{{ route('admin.provisioning-tokens.qr', $device->provisioningToken) }}" download="{{ $device->serial_number }}_qr.png" class="mt-4">Download QR Code</x-button>
            </div>
            @endif
        </div>

        <div class="mt-6 flex justify-end">
            <x-button tag="a" href="{{ route('dashboard') }}" variant="secondary">Back to Dashboard</x-button>
        </div>
    </x-glass-card>

    {{-- Chart Section --}}
    <x-glass-card class="mt-8">
        <h3 class="text-xl font-semibold mb-4">Real-time Power Consumption (24h)</h3>
        <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow">
            <div id="power-chart" style="height: 300px;"></div>
        </div>
    </x-glass-card>
</div>
@endsection

@push('scripts')
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
                        colors: '#9CA3AF'
                    }
                }
            },
            yaxis: {
                title: {
                    text: 'Power (Watts)',
                    style: {
                        color: '#9CA3AF'
                    }
                },
                labels: {
                    style: {
                        colors: '#9CA3AF'
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
                borderColor: '#4B5563'
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
