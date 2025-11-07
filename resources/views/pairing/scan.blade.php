@extends('layouts.base')

@section('body-class', 'bg-gray-100 dark:bg-gray-900')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto">
        <h1 class="text-3xl font-bold text-center mb-8">Pair New Device</h1>

        <div id="qr-reader" class="w-full max-w-md mx-auto mb-8"></div>

        <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-6 mb-8">
            <h2 class="text-xl font-semibold mb-4">Manual Token Entry</h2>
            <div class="flex items-center">
                <input type="text" id="token-input" placeholder="WS-XXXXXXXXXXXX" class="w-full px-4 py-2 border rounded-l-md focus:outline-none focus:ring-2 focus:ring-blue-500 uppercase"
                    pattern="^WS-[A-Z0-9]{12}$" maxlength="15">
                <button id="pair-button" class="bg-blue-500 text-white px-4 py-2 rounded-r-md hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    Pair
                </button>
            </div>
        </div>

        <div id="device-info" class="hidden bg-white dark:bg-gray-800 shadow-md rounded-lg p-6 mb-8">
            <h2 class="text-xl font-semibold mb-4">Device Information</h2>
            <p class="mb-2"><strong>Serial Number:</strong> <span id="serial-number"></span></p>
            <div class="mb-4">
                <label for="device-name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Custom Device Name</label>
                <input type="text" id="device-name" class="w-full px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <button id="confirm-pairing-button" class="w-full bg-green-500 text-white px-4 py-2 rounded-md hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-green-500">
                Confirm Pairing
            </button>
        </div>

        <div class="bg-gray-100 dark:bg-gray-700 rounded-lg p-6">
            <h2 class="text-xl font-semibold mb-4">Instructions</h2>
            <ol class="list-decimal list-inside">
                <li>Position the QR code on your device within the scanner's view.</li>
                <li>Once scanned, device information will appear.</li>
                <li>Optionally, enter a custom name for your device.</li>
                <li>Click "Confirm Pairing" to complete the process.</li>
            </ol>
            <h3 class="text-lg font-semibold mt-4">Troubleshooting</h3>
            <ul class="list-disc list-inside">
                <li>Ensure you have granted camera permissions.</li>
                <li>Make sure the QR code is well-lit and not damaged.</li>
                <li>If scanning fails, you can manually enter the token.</li>
            </ul>
        </div>
    </div>
</div>

<div id="toast" class="hidden fixed top-4 right-4 px-6 py-3 rounded-lg shadow-lg text-white transition-opacity duration-300"></div>

@endsection

@push('scripts')
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const qrReader = new Html5Qrcode("qr-reader");
        const tokenInput = document.getElementById('token-input');
        const pairButton = document.getElementById('pair-button');
        const deviceInfoSection = document.getElementById('device-info');
        const serialNumberSpan = document.getElementById('serial-number');
        const deviceNameInput = document.getElementById('device-name');
        const confirmPairingButton = document.getElementById('confirm-pairing-button');
        const toast = document.getElementById('toast');

        let validatedToken = null;

        function showToast(message, type = 'info') {
            toast.textContent = message;
            toast.className = `fixed top-4 right-4 px-6 py-3 rounded-lg shadow-lg text-white ${type === 'success' ? 'bg-green-500' : type === 'error' ? 'bg-red-500' : 'bg-blue-500'}`;
            toast.classList.remove('hidden');
            setTimeout(() => {
                toast.classList.add('hidden');
            }, 3000);
        }

        function startQrScanner() {
            qrReader.start(
                { facingMode: "environment" },
                {
                    fps: 10,
                    qrbox: { width: 250, height: 250 }
                },
                (decodedText, decodedResult) => {
                    let token = decodedText;
                    if (decodedText.includes('token=')) {
                        token = new URL(decodedText).searchParams.get('token');
                    }
                    tokenInput.value = token;
                    validateToken(token);
                },
                (errorMessage) => {
                    // console.log(errorMessage);
                }
            ).catch((err) => {
                showToast('Could not start QR scanner. Please grant camera permissions.', 'error');
            });
        }

        async function validateToken(token) {
            try {
                const response = await fetch('/api/v1/pairing/validate', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': `Bearer ${window.apiToken}`,
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ token })
                });

                const data = await response.json();

                if (response.ok) {
                    validatedToken = token;
                    serialNumberSpan.textContent = data.device_info.serial_number;
                    deviceInfoSection.classList.remove('hidden');
                    qrReader.stop();
                } else {
                    showToast(data.error, 'error');
                }
            } catch (error) {
                showToast('An error occurred during validation.', 'error');
            }
        }

        async function pairDevice() {
            const deviceName = deviceNameInput.value;

            try {
                const response = await fetch('/api/v1/pairing/pair', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': `Bearer ${window.apiToken}`,
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ token: validatedToken, device_name: deviceName })
                });

                const data = await response.json();

                if (response.status === 201) {
                    showToast('Device paired successfully!', 'success');
                    setTimeout(() => {
                        window.location.href = '/devices';
                    }, 2000);
                } else {
                    showToast(data.message, 'error');
                }
            } catch (error) {
                showToast('An error occurred during pairing.', 'error');
            }
        }

        pairButton.addEventListener('click', () => {
            const token = tokenInput.value;
            if (token) {
                validateToken(token);
            }
        });

        confirmPairingButton.addEventListener('click', pairDevice);

        // Check for token in URL on page load
        const urlParams = new URLSearchParams(window.location.search);
        const tokenFromUrl = urlParams.get('token');

        if (tokenFromUrl) {
            tokenInput.value = tokenFromUrl.toUpperCase();
            validateToken(tokenFromUrl.toUpperCase());
            // Hide the QR reader if we are using a URL token
            document.getElementById('qr-reader').style.display = 'none';
        } else {
            startQrScanner();
        }
    });
</script>
@endpush
