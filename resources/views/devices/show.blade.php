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
        .relay-toggle-btn {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            font-size: 1rem;
            font-weight: 600;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 2px solid transparent;
        }
        .relay-toggle-btn.state-on {
            background-color: #10B981; /* Green-500 */
            color: white;
            box-shadow: 0 0 15px rgba(16, 185, 129, 0.5);
            border-color: #6EE7B7;
        }
        .relay-toggle-btn.state-off {
            background-color: #EF4444; /* Red-500 */
            color: white;
            box-shadow: 0 0 15px rgba(239, 68, 68, 0.5);
            border-color: #FCA5A5;
        }
        .power-btn {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: rgba(255, 255, 255, 0.1);
            border: 2px solid rgba(255, 255, 255, 0.2);
            transition: all 0.3s ease;
        }
        .power-btn:hover {
            background-color: rgba(255, 255, 255, 0.2);
            border-color: rgba(255, 255, 255, 0.4);
        }
        .power-btn svg {
            width: 40px;
            height: 40px;
            color: #FDBA74; /* Orange-300 */
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

        {{-- Relay Controls --}}
        <div class="stagger-item mt-8">
            @php
            function getRelayState($latestReading, $channel) {
                if (!$latestReading || !$latestReading->channelReadings) {
                    return 'off'; // Default to off if no data
                }
                $reading = $latestReading->channelReadings->firstWhere('channel', $channel);
                return $reading ? $reading->relay_state : 'off';
            }
            @endphp
            <x-glass-card>
                <div class="flex items-center justify-around p-4 flex-wrap">
                    {{-- Master Power Button --}}
                    <div class="text-center m-2">
                        <button class="power-btn" id="master-power-btn" aria-label="Toggle All Relays">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5.636 5.636a9 9 0 1 0 12.728 0M12 3v9" />
                            </svg>
                        </button>
                        <span class="block mt-2 text-sm font-medium">ALL</span>
                    </div>

                    {{-- Individual Relay Buttons --}}
                    @foreach ([1, 2, 3] as $channel)
                        @php
                            $state = getRelayState($latestReading, $channel);
                        @endphp
                        <div class="text-center m-2">
                            <button
                                class="relay-toggle-btn state-{{ $state }}"
                                data-channel="{{ $channel }}"
                                data-state="{{ $state }}"
                            >
                                {{ strtoupper($state) }}
                            </button>
                            <span class="block mt-2 text-sm font-medium">RELAY {{ $channel }}</span>
                        </div>
                    @endforeach
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

        {{-- Wi-Fi Setup Section --}}
        <div class="stagger-item mt-8">
            <x-glass-card>
                <h3 class="text-xl font-semibold mb-4">ESP32 Wi-Fi Setup (BLE)</h3>

                <!-- Browser/HTTPS Support Notice -->
                <div class="bg-blue-500/10 border border-blue-400/20 text-blue-200 rounded-lg p-4 mb-6 text-sm">
                    <p class="mb-1">Web Bluetooth works in Chromium-based browsers (Chrome, Edge, Android Chrome).</p>
                    <p>Page must be served over <span class="font-medium">HTTPS</span> or <span class="font-medium">localhost</span>.</p>
                </div>

                <!-- Connect Bar -->
                <div class="bg-white/5 rounded-lg p-6 mb-6">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <div>
                            <div class="text-sm text-gray-400">Device</div>
                            <div id="bleDeviceName" class="text-lg font-medium">Not Connected</div>
                        </div>
                        <div class="flex items-center gap-3">
                            <div id="bleStatusBadge" class="px-2 py-1 text-xs rounded bg-gray-200 text-gray-800">—</div>
                            <x-button id="bleConnectBtn">Connect to Device</x-button>
                        </div>
                    </div>
                </div>

                <!-- Credentials Form -->
                <div class="bg-white/5 rounded-lg p-6 mb-6">
                    <h2 class="text-xl font-semibold mb-4">Wi‑Fi Credentials</h2>
                    <div class="grid grid-cols-1 gap-4">
                        <div>
                            <label for="bleSsidInput" class="block text-sm font-medium text-gray-300 mb-1">SSID</label>
                            <x-input id="bleSsidInput" type="text" placeholder="Your Wi‑Fi SSID" />
                            <div class="mt-3 flex flex-col sm:flex-row gap-3">
                                <x-button id="bleScanBtn" type="button" variant="secondary">
                                    <svg class="w-4 h-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M3 4.5A1.5 1.5 0 014.5 3h11A1.5 1.5 0 0117 4.5V6a.5.5 0 01-1 0V4.5a.5.5 0 00-.5-.5h-11a.5.5 0 00-.5.5v11a.5.5 0 00.5.5H6a.5.5 0 010 1H4.5A1.5 1.5 0 013 15.5v-11z" clip-rule="evenodd"/><path d="M6 8a1 1 0 011-1h7a1 1 0 110 2H7a1 1 0 01-1-1zM6 12a1 1 0 011-1h5a1 1 0 110 2H7a1 1 0 01-1-1z"/></svg>
                                    Scan Networks
                                </x-button>
                                <div class="flex-1">
                                    <x-select id="bleSsidSelect" class="w-full">
                                        <option value="">Select scanned network…</option>
                                    </x-select>
                                </div>
                            </div>
                            <div class="flex items-center gap-2 mt-1 text-xs text-gray-400">
                                <div id="bleScanHint" class="hidden">Scanning… please wait</div>
                                <div id="bleScanCooldown" class="hidden">Rescan available in <span id="bleScanCooldownSec">0</span>s</div>
                                <button id="bleScanHelp" type="button" class="ml-auto underline hover:text-gray-200" title="Android requires Location to be ON for BLE scanning. Ensure Bluetooth and Location are enabled.">Help</button>
                            </div>
                        </div>
                        <div>
                            <label for="blePasswordInput" class="block text-sm font-medium text-gray-300 mb-1">Password</label>
                            <div class="flex gap-2">
                                <x-input id="blePasswordInput" type="password" placeholder="Your Wi‑Fi Password" class="flex-1" />
                                <x-button id="bleShowPassword" type="button" variant="secondary">Show</x-button>
                            </div>
                        </div>
                        <div class="pt-2">
                            <x-button id="bleSaveBtn" variant="success" disabled>Save Credentials</x-button>
                        </div>
                    </div>
                </div>

                <!-- Live Status Log -->
                <div class="bg-white/5 rounded-lg p-6 mb-6">
                    <div class="flex items-center justify-between mb-3">
                        <h2 class="text-xl font-semibold">Device Status</h2>
                        <x-button id="bleClearLog" variant="secondary">Clear</x-button>
                    </div>
                    <div id="bleStatusText" class="text-sm text-gray-300 mb-3">—</div>
                    <div id="bleStatusLog" class="bg-black/20 border border-white/10 rounded p-3 h-56 overflow-y-auto text-sm font-mono whitespace-pre-wrap">Waiting for device...</div>
                </div>

                <!-- Footer -->
                <div class="text-center text-xs text-gray-400">
                    <p>Service UUID: <code>4fafc201-1fb5-459e-8fcc-c5c9c331914b</code></p>
                    <p>Write (Wi‑Fi) Char: <code>beb5483e-36e1-4688-b7f5-ea07361b26a8</code> • Status Char (Read/Notify): <code>6e400003-b5a3-f393-e0a9-e50e24dcca9e</code></p>
                </div>
            </x-glass-card>
        </div>

        {{-- Device Configuration --}}
        <div class="stagger-item mt-8">
            <x-glass-card>
                <h3 class="text-xl font-semibold mb-4">Scheduler</h3>
                <x-device-settings.scheduler :device="$device" />
                <div class="flex justify-end space-x-3 mt-4 pt-4 border-t border-white/10">
                    <x-button variant="primary" onclick="saveConfiguration('{{ $device->id }}', 'scheduler')">Save Scheduler</x-button>
                </div>
            </x-glass-card>
        </div>

        <div class="stagger-item mt-8">
            <x-glass-card>
                <h3 class="text-xl font-semibold mb-4">Timer</h3>
                <x-device-settings.timer :device="$device" />
                <div class="flex justify-end space-x-3 mt-4 pt-4 border-t border-white/10">
                    <x-button variant="primary" onclick="saveConfiguration('{{ $device->id }}', 'timer')">Save Timer</x-button>
                </div>
            </x-glass-card>
        </div>

        <div class="stagger-item mt-8">
            <x-glass-card>
                <h3 class="text-xl font-semibold mb-4">Watt Limit</h3>
                <x-device-settings.watt-limit :device="$device" />
                <div class="flex justify-end space-x-3 mt-4 pt-4 border-t border-white/10">
                    <x-button variant="primary" onclick="saveConfiguration('{{ $device->id }}', 'watt_limit')">Save Watt Limit</x-button>
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

{{-- BLE Wi-Fi Setup Script --}}
<script>
    (function() {
      'use strict';

      // UUIDs (must match firmware in esp32-controller.ino)
      const SERVICE_UUID = '4fafc201-1fb5-459e-8fcc-c5c9c331914b';
      const WIFI_CHAR_UUID = 'beb5483e-36e1-4688-b7f5-ea07361b26a8';
      const STATUS_CHAR_UUID = '6e400003-b5a3-f393-e0a9-e50e24dcca9e';
      const SCAN_CMD_UUID = '6e400010-b5a3-f393-e0a9-e50e24dcca9e';
      const SCAN_RESULTS_UUID = '6e400011-b5a3-f393-e0a9-e50e24dcca9e';

      // UI elements
      const el = {
        deviceName: document.getElementById('bleDeviceName'),
        statusBadge: document.getElementById('bleStatusBadge'),
        connectBtn: document.getElementById('bleConnectBtn'),
        saveBtn: document.getElementById('bleSaveBtn'),
        ssid: document.getElementById('bleSsidInput'),
        ssidSelect: document.getElementById('bleSsidSelect'),
        scanBtn: document.getElementById('bleScanBtn'),
        password: document.getElementById('blePasswordInput'),
        showPassword: document.getElementById('bleShowPassword'),
        statusText: document.getElementById('bleStatusText'),
        statusLog: document.getElementById('bleStatusLog'),
        clearLog: document.getElementById('bleClearLog'),
        scanHint: document.getElementById('bleScanHint'),
        scanCooldown: document.getElementById('bleScanCooldown'),
        scanCooldownSec: document.getElementById('bleScanCooldownSec'),
        scanHelp: document.getElementById('bleScanHelp'),
      };

      // BLE state
      let device = null;
      let server = null;
      let service = null;
      let wifiChar = null;
      let statusChar = null;
      let scanCmdChar = null;
      let scanResultsChar = null;
      let scanning = false;
      const networks = new Map(); // SSID -> { rssi, sec }
      let cooldownTimer = null;

      function log(line) {
        const now = new Date().toLocaleTimeString();
        el.statusLog.textContent = `[${now}] ${line}\n` + el.statusLog.textContent;
      }

      function setBadge(text, color = 'gray') {
        const map = {
          gray: 'bg-gray-200 text-gray-800',
          green: 'bg-green-100 text-green-800',
          red: 'bg-red-100 text-red-800',
          yellow: 'bg-yellow-100 text-yellow-800',
          blue: 'bg-blue-100 text-blue-800'
        };
        el.statusBadge.className = `px-2 py-1 text-xs rounded ${map[color] || map.gray}`;
        el.statusBadge.textContent = text;
      }

      function supportsWebBluetooth() {
        if (!navigator.bluetooth) {
          setBadge('BLE Unsupported', 'red');
          el.connectBtn.disabled = true;
          log('Error: This browser does not support Web Bluetooth.');
          return false;
        }
        return true;
      }

      function decodeValue(event) {
        const value = event.target.value; // DataView
        const decoder = new TextDecoder('utf-8');
        return decoder.decode(value.buffer);
      }

      function onStatusChanged(event) {
        try {
          const msg = decodeValue(event).replace(/\0+$/, '');
          el.statusText.textContent = msg;
          log(`Status: ${msg}`);
          // Color hint
          const m = msg.toLowerCase();
          if (m.includes('connected')) setBadge('Connected', 'green');
          else if (m.includes('connecting')) setBadge('Connecting…', 'yellow');
          else if (m.includes('waiting')) setBadge('Waiting', 'blue');
          else if (m.includes('fail') || m.includes('invalid')) setBadge('Error', 'red');
          else setBadge(msg.substring(0, 18) || '—', 'gray');
        } catch (e) {
          log('Status decode error: ' + e.message);
        }
      }

      async function connect() {
        try {
          if (!supportsWebBluetooth()) return;

          setBadge('Selecting…', 'blue');
          device = await navigator.bluetooth.requestDevice({
            filters: [{ services: [SERVICE_UUID] }],
            optionalServices: [SERVICE_UUID]
          });
          el.deviceName.textContent = device.name || 'ESP32_WiFi_Config';
          log('Device selected: ' + el.deviceName.textContent);

          device.addEventListener('gattserverdisconnected', () => {
            setBadge('Disconnected', 'red');
            el.saveBtn.disabled = true;
            log('GATT disconnected. Device may be restarting after credentials write.');
          });

          setBadge('Connecting…', 'yellow');
          server = await device.gatt.connect();
          service = await server.getPrimaryService(SERVICE_UUID);

          // Characteristics
          wifiChar = await service.getCharacteristic(WIFI_CHAR_UUID);
          statusChar = await service.getCharacteristic(STATUS_CHAR_UUID);
          // Scan characteristics
          try { scanCmdChar = await service.getCharacteristic(SCAN_CMD_UUID); } catch (e) { scanCmdChar = null; }
          try { scanResultsChar = await service.getCharacteristic(SCAN_RESULTS_UUID); } catch (e) { scanResultsChar = null; }

          // Subscribe to status notifications
          await statusChar.startNotifications();
          statusChar.addEventListener('characteristicvaluechanged', onStatusChanged);

          // Initial read
          try {
            const initial = await statusChar.readValue();
            onStatusChanged({ target: { value: initial } });
          } catch (e) {
            log('Initial status read failed: ' + e.message);
          }

          setBadge('Connected', 'green');
          el.saveBtn.disabled = false;
          log('Connected and ready. You can now send Wi‑Fi credentials.');
        } catch (err) {
          setBadge('Error', 'red');
          log('Connect error: ' + err.message);
        }
      }

      function clearNetworks() {
        networks.clear();
        while (el.ssidSelect.options.length > 1) el.ssidSelect.remove(1);
      }

      function setScanning(on) {
        scanning = on;
        if (on) {
          el.scanHint.classList.remove('hidden');
          el.scanBtn.disabled = true;
          setBadge('Scanning…', 'blue');
        } else {
          el.scanHint.classList.add('hidden');
          el.scanBtn.disabled = false;
        }
      }

      function startScanCooldown(seconds = 10) {
        clearInterval(cooldownTimer);
        let remaining = seconds;
        if (remaining <= 0) {
          el.scanCooldown.classList.add('hidden');
          el.scanBtn.disabled = false;
          return;
        }
        el.scanCooldown.classList.remove('hidden');
        el.scanBtn.disabled = true;
        el.scanCooldownSec.textContent = remaining;
        cooldownTimer = setInterval(() => {
          remaining -= 1;
          if (remaining <= 0) {
            clearInterval(cooldownTimer);
            el.scanCooldown.classList.add('hidden');
            el.scanBtn.disabled = false;
          } else {
            el.scanCooldownSec.textContent = remaining;
          }
        }, 1000);
      }

      function rssiToBars(rssi) {
        // Approximate conversion to 0..4 bars based on RSSI (dBm)
        if (isNaN(rssi)) return 0;
        if (rssi >= -50) return 4;     // excellent
        if (rssi >= -60) return 3;     // good
        if (rssi >= -70) return 2;     // fair
        if (rssi >= -80) return 1;     // weak
        return 0;                       // very weak
      }

      function barsToGlyph(bars) {
        // Simple ASCII bars 0..4
        const glyphs = ['    ', '▂   ', '▂▃  ', '▂▃▅ ', '▂▃▅▇'];
        return glyphs[Math.max(0, Math.min(4, bars))];
      }

      function formatNetworkLabel(ssid, info) {
        const bars = rssiToBars(info.rssi);
        const glyph = barsToGlyph(bars);
        return `${ssid} [${glyph}] (${info.sec}, ${info.rssi} dBm)`;
      }

      function applyNetworksToDropdown() {
        // Convert to array and sort by RSSI descending (higher is better, closer to 0)
        const arr = Array.from(networks.entries()) // [ssid, {rssi, sec}]
          .sort((a, b) => b[1].rssi - a[1].rssi);
        clearNetworks(); // Leaves placeholder
        for (const [ssid, info] of arr) {
          const opt = document.createElement('option');
          opt.value = ssid;
          opt.textContent = formatNetworkLabel(ssid, info);
          el.ssidSelect.appendChild(opt);
        }
      }

      function onScanResultsChanged(event) {
        const line = decodeValue(event).replace(/\0+$/, '').trim();
        if (!line) return;
        if (line === 'SCAN_START') {
          clearNetworks();
          log('Scan started');
          return;
        }
        if (line === 'SCAN_DONE') {
          log('Scan completed');
          applyNetworksToDropdown();
          setScanning(false);
          setBadge('Connected', 'green');
          // Start a short cooldown before allowing another scan
          startScanCooldown(10);
          return;
        }
        if (line.startsWith('SCAN_ERROR')) {
          log(line);
          setScanning(false);
          setBadge('Connected', 'green');
          return;
        }
        if (line === 'SCAN_BUSY') {
          log('Device is busy scanning; try again shortly.');
          setScanning(false);
          setBadge('Connected', 'green');
          return;
        }
        // Parse SSID|RSSI|SEC
        const parts = line.split('|');
        if (parts.length >= 3) {
          const ssid = parts[0];
          const rssi = parseInt(parts[1], 10);
          const sec = parts[2];
          if (!ssid) return;
          const prev = networks.get(ssid);
          if (!prev || rssi > prev.rssi) {
            networks.set(ssid, { rssi, sec });
          }
        }
      }

      async function startScan() {
        try {
          if (!server || !service || !scanCmdChar) {
            alert('Please connect to the device first.');
            return;
          }
          if (scanResultsChar) {
            try {
              await scanResultsChar.startNotifications();
              // Ensure single listener
              scanResultsChar.removeEventListener('characteristicvaluechanged', onScanResultsChanged);
              scanResultsChar.addEventListener('characteristicvaluechanged', onScanResultsChanged);
            } catch (e) {
              log('Unable to start scan notifications: ' + e.message);
            }
          }
          clearNetworks();
          setScanning(true);
          const encoder = new TextEncoder();
          await scanCmdChar.writeValue(encoder.encode('SCAN'));
          log('Scan command sent');
        } catch (e) {
          setScanning(false);
          log('Scan error: ' + e.message);
        }
      }

      async function writeCredentials() {
        try {
          const ssid = (el.ssid.value || '').trim();
          const pass = (el.password.value || '').trim();
          if (!ssid || !pass) {
            alert('Please enter both SSID and Password.');
            return;
          }
          if (!wifiChar) {
            alert('Not connected to device.');
            return;
          }

          const payload = `${ssid},${pass}`;
          const encoder = new TextEncoder();
          const data = encoder.encode(payload);

          log('Writing credentials…');
          setBadge('Writing…', 'yellow');

          // Prefer writeValue when available
          if (wifiChar.writeValue) {
            await wifiChar.writeValue(data);
          } else if (wifiChar.writeValueWithoutResponse) {
            await wifiChar.writeValueWithoutResponse(data);
          } else {
            throw new Error('Characteristic does not support write.');
          }

          log('Credentials sent. Device should respond with "Received Credentials" and then "Restarting".');
          // After restart, the device may disconnect. Allow the user to reconnect to observe final status.
        } catch (err) {
          setBadge('Error', 'red');
          log('Write error: ' + err.message);
        }
      }

      function togglePassword() {
        const isHidden = el.password.type === 'password';
        el.password.type = isHidden ? 'text' : 'password';
        el.showPassword.textContent = isHidden ? 'Hide' : 'Show';
      }

      function clearLog() {
        el.statusLog.textContent = '';
      }

      // Wire up events
      el.connectBtn.addEventListener('click', connect);
      el.saveBtn.addEventListener('click', writeCredentials);
      el.showPassword.addEventListener('click', togglePassword);
      el.clearLog.addEventListener('click', clearLog);
      el.scanBtn.addEventListener('click', startScan);
      el.ssidSelect.addEventListener('change', () => {
        const chosen = el.ssidSelect.value;
        if (chosen) el.ssid.value = chosen;
      });
      el.scanHelp.addEventListener('click', () => {
        alert('Tips for scanning on Android:\n\n- Enable Bluetooth and Location services.\n- Keep Chrome/Edge up to date.\n- Ensure the ESP32 is advertising and in provisioning mode.');
      });

      // Initial badge
      setBadge('Idle', 'gray');
    })();
</script>

{{-- Relay Control and Device Configuration Scripts --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // --- Relay Control ---
        const apiTokenEl = document.querySelector('meta[name="api-token"]');
        if (!apiTokenEl) {
            console.error('API token meta tag not found.');
        }
        const apiToken = apiTokenEl ? apiTokenEl.getAttribute('content') : null;
        const deviceId = '{{ $device->id }}';

        function sendRelayCommand(channel, state, button) {
            if (!apiToken) {
                showNotification('Could not find API token. Cannot send command.', 'error');
                return;
            }

            const originalButtonContent = button.innerHTML;
            button.innerHTML = '...';
            button.disabled = true;

            fetch(`/api/v1/devices/${deviceId}/relay`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${apiToken}`,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ channel, state })
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(err => { throw new Error(err.message || 'Request failed') });
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    button.dataset.state = state;
                    button.textContent = state.toUpperCase();
                    button.classList.remove('state-on', 'state-off');
                    button.classList.add(`state-${state}`);
                    showNotification(`Relay ${channel} turned ${state}.`, 'success');
                } else {
                    showNotification('Failed to send relay command: ' + data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('An error occurred: ' + error.message, 'error');
            })
            .finally(() => {
                button.innerHTML = state.toUpperCase();
                setTimeout(() => {
                    button.disabled = false;
                }, 500);
            });
        }

        document.querySelectorAll('.relay-toggle-btn').forEach(button => {
            button.addEventListener('click', function () {
                const channel = this.dataset.channel;
                const currentState = this.dataset.state;
                const newState = currentState === 'on' ? 'off' : 'on';
                sendRelayCommand(channel, newState, this);
            });
        });

        const masterPowerBtn = document.getElementById('master-power-btn');
        if (masterPowerBtn) {
            masterPowerBtn.addEventListener('click', function() {
                const anyRelayOff = !!document.querySelector('.relay-toggle-btn[data-state="off"]');
                const masterState = anyRelayOff ? 'on' : 'off';

                document.querySelectorAll('.relay-toggle-btn').forEach(button => {
                    const channel = button.dataset.channel;
                    if (button.dataset.state !== masterState) {
                        sendRelayCommand(channel, masterState, button);
                    }
                });
            });
        }

        // --- Device Configuration ---
        // Timer functionality
        window.adjustTimer = function(deviceId, adjustment) {
            const slider = document.getElementById(`timer-duration-${deviceId}`);
            const currentValue = parseInt(slider.value);
            const newValue = Math.max(1, Math.min(120, currentValue + adjustment));
            slider.value = newValue;
            updateTimerDisplay(deviceId, newValue);
        };

        window.updateTimerDisplay = function(deviceId, minutes) {
            const display = document.getElementById(`timer-display-${deviceId}`);
            const arc = document.getElementById(`timer-arc-${deviceId}`);
            const slider = document.getElementById(`timer-duration-${deviceId}`);

            if (minutes >= 60) {
                const hours = Math.floor(minutes / 60);
                const remainingMinutes = minutes % 60;
                display.textContent = `${hours}h ${remainingMinutes}m`;
            } else {
                display.textContent = `${minutes}m`;
            }

            // The range is now 1 to 120.
            const percentage = Math.max(0, Math.min(((minutes - 1) / (120 - 1)) * 100, 100));
            const circumference = 2 * Math.PI * 19; // radius is 19
            const dashArray = `${(percentage / 100) * circumference} ${circumference}`;

            if (arc) {
                arc.setAttribute('stroke-dasharray', dashArray);
            }
            if (slider) {
                slider.style.background = `linear-gradient(to right, rgba(59, 130, 246, 0.8) 0%, rgba(59, 130, 246, 0.8) ${percentage}%, rgba(255,255,255,0.1) ${percentage}%, rgba(255,255,255,0.1) 100%)`;
            }
        };

        window.saveConfiguration = async function(deviceId, type) {
            const saveBtn = document.querySelector(`button[onclick="saveConfiguration('${deviceId}', '${type}')"]`);
            const originalText = saveBtn ? saveBtn.textContent : 'Save';

            try {
                if (saveBtn) {
                    saveBtn.textContent = 'Saving...';
                    saveBtn.disabled = true;
                }

                let configData = {};
                if (type === 'scheduler') {
                    configData = {
                        start_time: document.getElementById(`scheduler-start-${deviceId}`).value,
                        end_time: document.getElementById(`scheduler-end-${deviceId}`).value,
                        is_active: true
                    };
                } else if (type === 'timer') {
                    configData = {
                        duration: parseInt(document.getElementById(`timer-duration-${deviceId}`).value),
                        is_active: true
                    };
                } else if (type === 'watt_limit') {
                    configData = {
                        limit: parseInt(document.getElementById(`watt-limit-${deviceId}`).value),
                        is_active: true
                    };
                }

                const response = await axios.post(`/api/v1/devices/${deviceId}/configuration/${type}`, { configuration: configData });
                showNotification(`${type.replace('_', ' ')} configuration saved!`, 'success');
                await loadConfiguration(deviceId, type);

            } catch (error) {
                console.error(`Error saving ${type} configuration:`, error.response);
                let message = `Failed to save ${type} configuration.`;
                if (error.response && error.response.data) {
                    message = error.response.data.message || message;
                    if (error.response.data.errors) {
                        const errors = Object.values(error.response.data.errors).flat().join(' ');
                        message += ` ${errors}`;
                    }
                }
                showNotification(message, 'error');
            } finally {
                if (saveBtn) {
                    saveBtn.textContent = originalText;
                    saveBtn.disabled = false;
                }
            }
        };

        window.loadConfiguration = async function(deviceId, type) {
            try {
                const response = await axios.get(`/api/v1/devices/${deviceId}/configuration`);
                const config = response.data;

                if (type === 'scheduler' && config.scheduler) {
                    document.getElementById(`scheduler-start-${deviceId}`).value = config.scheduler.start_time || '08:00';
                    document.getElementById(`scheduler-end-${deviceId}`).value = config.scheduler.end_time || '18:00';
                }

                if (type === 'timer' && config.timer) {
                    const duration = config.timer.duration || 30;
                    document.getElementById(`timer-duration-${deviceId}`).value = duration;
                    updateTimerDisplay(deviceId, duration);
                }

                if (type === 'watt_limit' && config.watt_limit) {
                    document.getElementById(`watt-limit-${deviceId}`).value = config.watt_limit.limit || 1000;
                }

                showNotification(`${type.replace('_', ' ')} configuration loaded!`, 'success');

            } catch (error) {
                console.error(`Error loading ${type} configuration:`, error);
                showNotification(error.response?.data?.message || `Failed to load ${type} configuration.`, 'error');
            }
        };

        document.querySelectorAll('[id^="timer-duration-"]').forEach(slider => {
            const deviceId = slider.id.replace('timer-duration-', '');
            updateTimerDisplay(deviceId, slider.value);
            slider.addEventListener('input', (e) => updateTimerDisplay(deviceId, e.target.value));
        });

        function showNotification(message, type = 'info') {
            const notification = document.createElement('div');
            notification.className = `fixed top-24 right-4 px-6 py-3 rounded-lg z-50 shadow-lg text-white ${
                type === 'success' ? 'bg-green-500' :
                type === 'error' ? 'bg-red-500' : 'bg-blue-500'
            }`;
            notification.textContent = message;
            document.body.appendChild(notification);
            setTimeout(() => {
                notification.style.transition = 'opacity 0.5s ease';
                notification.style.opacity = '0';
                setTimeout(() => notification.remove(), 500);
            }, 3000);
        }
    });
</script>
@endpush
