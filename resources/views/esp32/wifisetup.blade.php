<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>ESP32 Wi‑Fi Setup (BLE)</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet" />
</head>
<body class="bg-gray-100 min-h-screen">
  <div class="max-w-3xl mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-6 text-center">ESP32 Wi‑Fi Setup (BLE)</h1>

    <!-- Browser/HTTPS Support Notice -->
    <div class="bg-blue-50 border border-blue-200 text-blue-800 rounded-lg p-4 mb-6 text-sm">
      <p class="mb-1">Web Bluetooth works in Chromium-based browsers (Chrome, Edge, Android Chrome).</p>
      <p>Page must be served over <span class="font-medium">HTTPS</span> or <span class="font-medium">localhost</span>.</p>
    </div>

    <!-- Connect Bar -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
          <div class="text-sm text-gray-500">Device</div>
          <div id="bleDeviceName" class="text-lg font-medium">Not Connected</div>
        </div>
        <div class="flex items-center gap-3">
          <div id="bleStatusBadge" class="px-2 py-1 text-xs rounded bg-gray-200 text-gray-800">—</div>
          <button id="bleConnectBtn" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded">Connect to Device</button>
        </div>
      </div>
    </div>

    <!-- Credentials Form -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
      <h2 class="text-xl font-semibold mb-4">Wi‑Fi Credentials</h2>
      <div class="grid grid-cols-1 gap-4">
        <div>
          <label for="bleSsidInput" class="block text-sm font-medium text-gray-700 mb-1">SSID</label>
          <input id="bleSsidInput" type="text" placeholder="Your Wi‑Fi SSID" class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500" />
          <div class="mt-3 flex flex-col sm:flex-row gap-3">
            <button id="bleScanBtn" type="button" class="inline-flex items-center gap-2 px-3 py-2 border rounded hover:bg-gray-50">
              <svg class="w-4 h-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M3 4.5A1.5 1.5 0 014.5 3h11A1.5 1.5 0 0117 4.5V6a.5.5 0 01-1 0V4.5a.5.5 0 00-.5-.5h-11a.5.5 0 00-.5.5v11a.5.5 0 00.5.5H6a.5.5 0 010 1H4.5A1.5 1.5 0 013 15.5v-11z" clip-rule="evenodd"/><path d="M6 8a1 1 0 011-1h7a1 1 0 110 2H7a1 1 0 01-1-1zM6 12a1 1 0 011-1h5a1 1 0 110 2H7a1 1 0 01-1-1z"/></svg>
              Scan Networks
            </button>
            <div class="flex-1">
              <select id="bleSsidSelect" class="w-full border rounded px-3 py-2 bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <option value="">Select scanned network…</option>
              </select>
            </div>
          </div>
          <div class="flex items-center gap-2 mt-1 text-xs text-gray-500">
            <div id="bleScanHint" class="hidden">Scanning… please wait</div>
            <div id="bleScanCooldown" class="hidden">Rescan available in <span id="bleScanCooldownSec">0</span>s</div>
            <button id="bleScanHelp" type="button" class="ml-auto underline hover:text-gray-700" title="Android requires Location to be ON for BLE scanning. Ensure Bluetooth and Location are enabled.">Help</button>
          </div>
        </div>
        <div>
          <label for="blePasswordInput" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
          <div class="flex gap-2">
            <input id="blePasswordInput" type="password" placeholder="Your Wi‑Fi Password" class="flex-1 border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500" />
            <button id="bleShowPassword" type="button" class="text-sm px-3 py-2 border rounded hover:bg-gray-50">Show</button>
          </div>
        </div>
        <div class="pt-2">
          <button id="bleSaveBtn" class="bg-green-600 hover:bg-green-700 text-white px-5 py-2 rounded disabled:opacity-50" disabled>Save Credentials</button>
        </div>
      </div>
    </div>

    <!-- Live Status Log -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
      <div class="flex items-center justify-between mb-3">
        <h2 class="text-xl font-semibold">Device Status</h2>
        <button id="bleClearLog" class="text-sm px-3 py-1 border rounded hover:bg-gray-50">Clear</button>
      </div>
      <div id="bleStatusText" class="text-sm text-gray-600 mb-3">—</div>
      <div id="bleStatusLog" class="bg-gray-50 border rounded p-3 h-56 overflow-y-auto text-sm font-mono whitespace-pre-wrap">Waiting for device...</div>
    </div>

    <!-- Footer -->
    <div class="text-center text-xs text-gray-500">
      <p>Service UUID: <code>4fafc201-1fb5-459e-8fcc-c5c9c331914b</code></p>
      <p>Write (Wi‑Fi) Char: <code>beb5483e-36e1-4688-b7f5-ea07361b26a8</code> • Status Char (Read/Notify): <code>6e400003-b5a3-f393-e0a9-e50e24dcca9e</code></p>
    </div>
  </div>

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
        const glyphs = ['▁▁▁▁', '▂▁▁▁', '▂▃▁▁', '▂▃▅▁', '▂▃▅▇'];
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
</body>
</html>
