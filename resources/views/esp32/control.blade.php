<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>ESP32 Control Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold mb-8 text-center">ESP32 Control Panel</h1>
        
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h2 class="text-2xl font-semibold text-gray-800">ESP32 Communication Log</h2>
                    <p class="text-sm text-gray-500">Real-time monitoring of ESP32 communications</p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h3 class="font-semibold mb-3">Last Received Data</h3>
                    <div id="lastReceived" class="p-3 bg-white rounded border text-sm font-mono h-40 overflow-y-auto">
                        No data received yet
                    </div>
                </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-6">
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h3 class="font-semibold mb-2">Send Command</h3>
                    <div class="flex flex-col space-y-4">
                        <button onclick="sendCommand('LED_ON')" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">
                            Turn LED ON
                        </button>
                        <button onclick="sendCommand('LED_OFF')" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded">
                            Turn LED OFF
                        </button>
                        <div class="flex space-x-2">
                            <input type="text" id="customCommand" placeholder="Custom command" class="flex-1 border rounded px-3 py-2">
                            <button onclick="sendCustomCommand()" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded">
                                Send
                            </button>
                        </div>
                    </div>
                </div>
                
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h3 class="font-semibold mb-2">Sensor Data</h3>
                    <div id="sensorData" class="space-y-2">
                        <p class="text-gray-600">No data received yet</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Global state
        let lastMessageTime = 0;
        let messageCount = 0;
        let socket;
        
        // Initialize when the page loads
        document.addEventListener('DOMContentLoaded', function() {
            // Load initial messages
            fetchMessages();
            
            // Set up WebSocket connection
            setupWebSocket();
            
            // Set up auto-refresh
            setInterval(fetchMessages, 5000);
        });
        
        // Set up WebSocket connection
        function setupWebSocket() {
            const socketProtocol = window.location.protocol === 'https:' ? 'wss://' : 'ws://';
            socket = new WebSocket(socketProtocol + window.location.host + '/ws/esp32');
            
            socket.onopen = function() {
                updateConnectionStatus(true);
                addSystemMessage('Connected to WebSocket server');
            };
            
            socket.onmessage = function(event) {
                try {
                    const data = JSON.parse(event.data);
                    if (data.type === 'message') {
                        processNewMessage(data.message, data.direction || 'incoming');
                    }
                } catch (e) {
                    console.error('Error processing WebSocket message:', e);
                }
            };
            
            socket.onclose = function() {
                updateConnectionStatus(false);
                addSystemMessage('WebSocket disconnected - attempting to reconnect...');
                setTimeout(setupWebSocket, 5000);
            };
            
            socket.onerror = function(error) {
                console.error('WebSocket error:', error);
                updateConnectionStatus(false);
            };
        }
        
        // Fetch messages from the server
        async function fetchMessages() {
            try {
                const response = await fetch(`/api/esp32/messages?since=${lastMessageTime}`);
                const data = await response.json();
                
                if (data.messages && data.messages.length > 0) {
                    data.messages.forEach(msg => {
                        processNewMessage(msg.content, msg.direction || 'incoming');
                    });
                }
                
                updateLastUpdated();
            } catch (error) {
                console.error('Error fetching messages:', error);
                addSystemMessage('Failed to fetch messages');
            }
        }
        
        // Process a new message
        function processNewMessage(content, direction) {
            // Update last message time
            lastMessageTime = Date.now();
            
            // Update message count
            if (direction === 'incoming') {
                messageCount++;
                document.getElementById('messageCount').textContent = messageCount;
                updateLastReceived(content);
            }
            
            // Add to log
            addLogMessage(content, direction);
            
            // Update last message time display
            updateLastMessageTime();
        }
        
        // Add a message to the log
        function addLogMessage(content, type = 'incoming') {
            const logsContainer = document.getElementById('messageLogs');
            
            // Clear the "waiting" message if it's the first message
            if (logsContainer.children.length === 1 && logsContainer.children[0].classList.contains('text-center')) {
                logsContainer.innerHTML = '';
            }
            
            const messageDiv = document.createElement('div');
            const timestamp = new Date().toLocaleTimeString();
            const direction = type === 'incoming' ? 'from ESP32' : 'to ESP32';
            
            messageDiv.className = `p-3 rounded mb-3 ${
                type === 'incoming' ? 'bg-blue-50' : 
                type === 'outgoing' ? 'bg-green-50' : 'bg-gray-50'
            }`;
            
            messageDiv.innerHTML = `
                <div class="flex justify-between text-xs text-gray-500 mb-1">
                    <span class="font-medium">${direction.toUpperCase()}</span>
                    <span>${timestamp}</span>
                </div>
                <div class="font-mono text-sm break-words">${formatMessageContent(content)}</div>
            `;
            
            logsContainer.prepend(messageDiv);
            logsContainer.scrollTop = 0;
        }
        
        // Format message content for display
        function formatMessageContent(content) {
            try {
                const data = JSON.parse(content);
                return `<pre class="whitespace-pre-wrap">${JSON.stringify(data, null, 2)}</pre>`;
            } catch (e) {
                return content;
            }
        }
        
        // Update last received data display
        function updateLastReceived(content) {
            const lastReceivedDiv = document.getElementById('lastReceived');
            try {
                const jsonData = JSON.parse(content);
                lastReceivedDiv.innerHTML = `<pre class="text-xs">${JSON.stringify(jsonData, null, 2)}</pre>`;
            } catch (e) {
                lastReceivedDiv.textContent = content;
            }
        }
        
        // Update connection status
        function updateConnectionStatus(connected) {
            const statusEl = document.getElementById('connectionStatus');
            if (connected) {
                statusEl.innerHTML = '<span class="text-green-600"><i class="fas fa-circle mr-1"></i> Connected</span>';
            } else {
                statusEl.innerHTML = '<span class="text-red-600"><i class="fas fa-circle mr-1"></i> Disconnected</span>';
            }
        }
        
        // Update last message time
        function updateLastMessageTime() {
            const now = new Date();
            document.getElementById('lastMessageTime').textContent = now.toLocaleTimeString();
        }
        
        // Update last updated time
        function updateLastUpdated() {
            const now = new Date();
            document.getElementById('lastUpdated').textContent = `Last updated: ${now.toLocaleTimeString()}`;
        }
        
        // Add a system message
        function addSystemMessage(message) {
            addLogMessage(`<span class="text-gray-600">${message}</span>`, 'system');
        }
        
        // Clear all logs
        function clearLogs() {
            if (confirm('Are you sure you want to clear all logs? This cannot be undone.')) {
                fetch('/esp32/messages/clear', {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Content-Type': 'application/json',
                    },
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        const logsContainer = document.getElementById('messageLogs');
                        logsContainer.innerHTML = '<p class="text-center text-gray-500 py-8">Logs cleared</p>';
                        document.getElementById('lastReceived').innerHTML = '<p class="text-gray-500">No data received yet</p>';
                        messageCount = 0;
                        document.getElementById('messageCount').textContent = '0';
                        addSystemMessage('Logs cleared');
                    }
                })
                .catch(error => {
                    console.error('Error clearing logs:', error);
                    addSystemMessage('Failed to clear logs');
                });
            }
        }
        
        // WebSocket connection for real-time updates
        const socketProtocol = window.location.protocol === 'https:' ? 'wss://' : 'ws://';
        const socket = new WebSocket(socketProtocol + window.location.host + '/ws/esp32');
        
        socket.onmessage = function(event) {
            const data = JSON.parse(event.data);
            if (data.type === 'message') {
                addLogMessage(data.message, 'incoming');
                
                if (data.message) {
                    updateLastReceived(data.message);
                }
            }
        };
        
        // Set up a polling mechanism as a fallback
        let lastUpdate = 0;
        async function pollForUpdates() {
            try {
                const response = await fetch('/api/esp32/messages?since=' + lastUpdate);
                const data = await response.json();
                
                if (data.messages && data.messages.length > 0) {
                    data.messages.forEach(msg => {
                        addLogMessage(msg.content, 'incoming');
                        if (msg.content) {
                            updateLastReceived(msg.content);
                        }
                    });
                    lastUpdate = data.lastUpdate;
                }
            } catch (error) {
                console.error('Error polling for updates:', error);
            }
            
            setTimeout(pollForUpdates, 2000);
        }
        
        // Start polling
        pollForUpdates();
                    
                    // Update sensor data if available
                    if (data.data && Object.keys(data.data).length > 0) {
                        const sensorDataDiv = document.getElementById('sensorData');
                        let sensorHtml = '';
                        for (const [key, value] of Object.entries(data.data)) {
                            sensorHtml += `<p><span class="font-medium">${key}:</span> ${JSON.stringify(value)}</p>`;
                        }
                        sensorDataDiv.innerHTML = sensorHtml;
                    }
                } else {
                    statusDiv.innerHTML = '<p class="text-red-600 font-medium">Device is offline</p>';
                }
            } catch (error) {
                console.error('Error fetching status:', error);
            }
        }

        // Send command to ESP32
        async function sendCommand(command, value = null) {
            try {
                const response = await fetch('/api/device/command', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ command, value })
                });
                
                const result = await response.json();
                if (result.status === 'success') {
                    alert(`Command sent: ${command}`);
                }
            } catch (error) {
                console.error('Error sending command:', error);
                alert('Failed to send command');
            }
        }

        // Send custom command from input
        function sendCustomCommand() {
            const commandInput = document.getElementById('customCommand');
            if (commandInput.value.trim()) {
                sendCommand(commandInput.value.trim());
                commandInput.value = '';
            }
        }

        // Update status every 5 seconds
        updateStatus();
        setInterval(updateStatus, 5000);
    </script>
</body>
</html>
