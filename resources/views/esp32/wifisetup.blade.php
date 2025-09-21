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

      // UI elements
      const el = {
        deviceName: document.getElementById('bleDeviceName'),
        statusBadge: document.getElementById('bleStatusBadge'),
        connectBtn: document.getElementById('bleConnectBtn'),
        saveBtn: document.getElementById('bleSaveBtn'),
        ssid: document.getElementById('bleSsidInput'),
        password: document.getElementById('blePasswordInput'),
        showPassword: document.getElementById('bleShowPassword'),
        statusText: document.getElementById('bleStatusText'),
        statusLog: document.getElementById('bleStatusLog'),
        clearLog: document.getElementById('bleClearLog'),
      };

      // BLE state
      let device = null;
      let server = null;
      let service = null;
      let wifiChar = null;
      let statusChar = null;

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

      // Initial badge
      setBadge('Idle', 'gray');
    })();
  </script>
</body>
</html>
