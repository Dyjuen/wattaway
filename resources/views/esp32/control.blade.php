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
        
        <!-- Device Status Panel -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h2 class="text-xl font-semibold mb-4">Device Status</h2>
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <div class="bg-gray-50 p-4 rounded">
                    <div class="text-sm text-gray-500">Connection Status</div>
                    <div id="connectionStatus" class="text-lg font-medium">
                        <span class="text-yellow-500"><i class="fas fa-circle-notch fa-spin mr-1"></i> Connecting...</span>
                    </div>
                </div>
                <div class="bg-gray-50 p-4 rounded">
                    <div class="text-sm text-gray-500">Last Message</div>
                    <div id="lastMessageTime" class="text-lg font-medium">Never</div>
                </div>
                <div class="bg-gray-50 p-4 rounded">
                    <div class="text-sm text-gray-500">Total Messages</div>
                    <div id="messageCount" class="text-lg font-medium">0</div>
                </div>
                <div class="bg-gray-50 p-4 rounded">
                    <div class="text-sm text-gray-500">Arduino Time</div>
                    <div id="arduinoTime" class="text-lg font-mono">--:--:--</div>
                </div>
                <div class="bg-gray-50 p-4 rounded">
                    <div class="text-sm text-gray-500">LED State</div>
                    <div id="ledState" class="text-lg font-medium">
                        <span class="px-2 py-1 rounded-full text-xs bg-gray-200 text-gray-800">UNKNOWN</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Message Log Panel -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-semibold">Message Log</h2>
                <div class="flex space-x-2">
                    <button onclick="clearLogs()" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded text-sm">
                        <i class="fas fa-trash-alt mr-1"></i> Clear Logs
                    </button>
                </div>
            </div>
            
            <div id="messageLogs" class="bg-gray-50 rounded-lg p-4 h-96 overflow-y-auto mb-4">
                <p class="text-center text-gray-500 py-8">Waiting for messages from ESP32...</p>
            </div>
            
            <div class="flex flex-col space-y-2">
                <div class="text-sm text-gray-500">Last Updated: <span id="lastUpdated">Never</span></div>
            </div>
        </div>
        
        <!-- Command Panel -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-semibold mb-4">Send Command</h2>
            <div class="flex flex-col space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <button onclick="sendCommand('LED_ON')" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-3 rounded-lg flex items-center justify-center">
                        <i class="fas fa-lightbulb mr-2"></i> Turn LED ON
                    </button>
                    <button onclick="sendCommand('LED_OFF')" class="bg-red-500 hover:bg-red-600 text-white px-4 py-3 rounded-lg flex items-center justify-center">
                        <i class="fas fa-lightbulb mr-2"></i> Turn LED OFF
                    </button>
                </div>
                <div class="flex space-x-2 mt-2">
                    <input type="text" id="customCommand" placeholder="Enter custom command..." class="flex-1 border rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <button onclick="sendCustomCommand()" class="bg-green-500 hover:bg-green-600 text-white px-6 py-2 rounded-lg flex items-center">
                        <i class="fas fa-paper-plane mr-2"></i> Send
                    </button>
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
        
        // Set up WebSocket connection with reconnection
        function setupWebSocket() {
            // Close existing connection if any
            if (socket && socket.readyState === WebSocket.OPEN) {
                socket.close();
            }

            const socketProtocol = window.location.protocol === 'https:' ? 'wss://' : 'ws://';
            const wsUrl = socketProtocol + window.location.host + '/ws/esp32';
            
            console.log('Connecting to WebSocket:', wsUrl);
            socket = new WebSocket(wsUrl);
            
            socket.onopen = function() {
                console.log('WebSocket connection established');
                updateConnectionStatus(true);
                addSystemMessage('Connected to WebSocket server');
                
                // Request initial state
                if (socket.readyState === WebSocket.OPEN) {
                    socket.send(JSON.stringify({
                        type: 'get_initial_state'
                    }));
                }
            };
            
            socket.onmessage = function(event) {
                try {
                    console.log('WebSocket message received:', event.data);
                    const data = JSON.parse(event.data);
                    
                    // Handle different message types
                    if (data.type === 'message') {
                        processNewMessage(data.message, data.direction || 'incoming');
                    } 
                    // Handle initial state
                    else if (data.type === 'initial_state') {
                        if (data.messages && Array.isArray(data.messages)) {
                            data.messages.forEach(msg => {
                                processNewMessage(msg.content, msg.direction || 'incoming');
                            });
                        }
                        if (data.status) {
                            updateConnectionStatus(true);
                        }
                    }
                    // Handle status updates
                    else if (data.type === 'status_update') {
                        if (data.connected !== undefined) {
                            updateConnectionStatus(data.connected);
                        }
                        if (data.last_message_time) {
                            updateLastMessageTime(data.last_message_time);
                        }
                    }
                    
                } catch (e) {
                    console.error('Error processing WebSocket message:', e, 'Data:', event.data);
                }
            };
            
            socket.onclose = function(event) {
                console.log('WebSocket connection closed:', event.code, event.reason);
                updateConnectionStatus(false);
                
                // Attempt to reconnect after a delay
                const reconnectTime = 5000; // 5 seconds
                addSystemMessage(`WebSocket disconnected. Reconnecting in ${reconnectTime/1000} seconds...`);
                
                setTimeout(() => {
                    if (socket.readyState !== WebSocket.OPEN && 
                        socket.readyState !== WebSocket.CONNECTING) {
                        setupWebSocket();
                    }
                }, reconnectTime);
            };
            
            socket.onerror = function(error) {
                console.error('WebSocket error:', error);
                updateConnectionStatus(false);
                
                // On error, close the socket which will trigger the onclose handler
                if (socket) {
                    socket.close();
                }
            };
        }
        
        // Fetch messages from the server
        async function fetchMessages() {
            try {
                const response = await fetch(`/api/esp32/messages?since=${lastMessageTime}`);
                const data = await response.json();
                
                if (data.data && data.data.length > 0) {
                    data.data.forEach(msg => {
                        try {
                            // Check if content is a string and parse it as JSON if needed
                            let messageContent = msg.content;
                            if (typeof messageContent === 'string') {
                                try {
                                    messageContent = JSON.parse(messageContent);
                                } catch (e) {
                                    // If parsing fails, use the string as is
                                    console.log('Content is not valid JSON, using as string');
                                }
                            }
                            
                            // Process the message with the correct content
                            processNewMessage(messageContent, msg.direction || 'incoming');
                            
                            // Update last message time if this is newer
                            const messageTime = new Date(msg.created_at).getTime();
                            if (messageTime > lastMessageTime) {
                                lastMessageTime = messageTime;
                            }
                        } catch (e) {
                            console.error('Error processing message:', e);
                        }
                    });
                    
                    // Update the UI with the latest message
                    if (data.data.length > 0) {
                        updateLastMessageTime();
                        document.getElementById('messageCount').textContent = data.data.length;
                    }
                }
                
                updateLastUpdated();
            } catch (error) {
                console.error('Error fetching messages:', error);
                addSystemMessage('Failed to fetch messages: ' + error.message);
            }
        }
        
        // Process a new message
        function processNewMessage(content, direction) {
            try {
                console.log('Processing message:', { content, direction });
                
                // The content might already be an object or a string
                const messageData = typeof content === 'string' ? 
                    (() => { try { return JSON.parse(content); } catch(e) { return content; } })() : 
                    content;
                
                // Update last message time
                lastMessageTime = Date.now();
                
                // Update message count for incoming messages
                if (direction === 'incoming') {
                    messageCount++;
                    document.getElementById('messageCount').textContent = messageCount;
                    updateLastReceived(content);
                    
                    // Check for nested state.reported structure
                    const reportedData = messageData.state?.reported || messageData;
                    
                    // Update Arduino time if available
                    if (reportedData.time) {
                        updateArduinoTime(reportedData.time);
                    } else if (messageData.time) {
                        updateArduinoTime(messageData.time);
                    }
                    
                    // Update LED state if available
                    if (reportedData.led_state) {
                        updateLedState(reportedData.led_state);
                    } else if (messageData.led_state) {
                        updateLedState(messageData.led_state);
                    }
                }
                
                // Format the content for display
                let displayContent;
                if (typeof messageData === 'object' && messageData !== null) {
                    // If it's an object, try to stringify it with nice formatting
                    try {
                        displayContent = JSON.stringify(messageData, null, 2);
                    } catch (e) {
                        displayContent = String(messageData);
                    }
                } else {
                    displayContent = String(content);
                }
                
                // Add to log with formatted content
                addLogMessage(displayContent, direction);
                
                // Update last message time display
                updateLastMessageTime();
                
            } catch (e) {
                console.error('Error processing message:', e);
                // Fallback to simple processing if anything fails
                lastMessageTime = Date.now();
                addLogMessage(`Error: ${e.message}\n${JSON.stringify(content, null, 2)}`, 'error');
                updateLastMessageTime();
            }
        }
        
        // Add a message to the log
        function addLogMessage(content, type = 'incoming') {
            const logsContainer = document.getElementById('messageLogs');
            
            // Clear the "waiting" message if it's the first message
            if (logsContainer.children.length === 1 && logsContainer.children[0].classList.contains('text-center')) {
                logsContainer.innerHTML = '';
            }
            
            const messageElement = document.createElement('div');
            messageElement.className = `mb-2 p-3 rounded-lg ${
                type === 'outgoing' ? 'bg-blue-100' : 
                type === 'system' ? 'bg-gray-200 text-gray-700' : 
                type === 'error' ? 'bg-red-100 text-red-800' : 
                'bg-gray-50 border border-gray-200'
            }`;
            
            // Format the content for display
            let displayContent = content;
            if (typeof content === 'object' && content !== null) {
                try {
                    displayContent = `<pre class="whitespace-pre-wrap">${JSON.stringify(content, null, 2)}</pre>`;
                } catch (e) {
                    displayContent = String(content);
                }
            } else if (typeof content === 'string' && (content.startsWith('{') || content.startsWith('['))) {
                try {
                    const parsed = JSON.parse(content);
                    displayContent = `<pre class="whitespace-pre-wrap">${JSON.stringify(parsed, null, 2)}</pre>`;
                } catch (e) {
                    // Not valid JSON, use as is
                }
            }
            
            messageElement.innerHTML = `
                <div class="flex justify-between items-start">
                    <div class="flex-1 overflow-x-auto">
                        <div class="font-medium text-sm mb-1 ${
                            type === 'outgoing' ? 'text-blue-700' : 
                            type === 'system' ? 'text-gray-600' : 
                            type === 'error' ? 'text-red-700' : 'text-gray-700'
                        }">
                            ${type === 'outgoing' ? 'You' : type === 'system' ? 'System' : type === 'error' ? 'Error' : 'ESP32'}
                            <span class="text-xs text-gray-500 ml-2">${new Date().toLocaleTimeString()}</span>
                        </div>
                        <div class="text-sm">${displayContent}</div>
                    </div>
                </div>
            `;
            
            // Add to the top of the logs
            logsContainer.insertBefore(messageElement, logsContainer.firstChild);
            
            // Limit the number of messages to prevent performance issues
            while (logsContainer.children.length > 100) {
                logsContainer.removeChild(logsContainer.lastChild);
            }
            
            // Update last updated time
            updateLastUpdated();
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
            document.getElementById('lastUpdated').textContent = `Last updated: ${now.toLocaleString()}`;
        }
        
        // Update Arduino time display
        function updateArduinoTime(timeString) {
            try {
                const timeElement = document.getElementById('arduinoTime');
                if (!timeString) return;
                
                // Try to parse the time string
                let time;
                if (typeof timeString === 'string') {
                    // Try ISO format first
                    time = new Date(timeString);
                    
                    // If invalid, try to parse as a timestamp
                    if (isNaN(time.getTime())) {
                        const timestamp = Date.parse(timeString);
                        if (!isNaN(timestamp)) {
                            time = new Date(timestamp);
                        } else {
                            console.warn('Invalid time format:', timeString);
                            timeElement.textContent = 'Invalid time';
                            timeElement.title = timeString;
                            return;
                        }
                    }
                } else if (typeof timeString === 'number') {
                    // Handle Unix timestamp (in seconds or milliseconds)
                    time = timeString > 1e10 ? new Date(timeString) : new Date(timeString * 1000);
                } else if (timeString instanceof Date) {
                    time = timeString;
                } else {
                    console.warn('Unsupported time format:', typeof timeString, timeString);
                    timeElement.textContent = 'Invalid format';
                    timeElement.title = String(timeString);
                    return;
                }
                
                // Format the time for display
                const options = {
                    year: 'numeric',
                    month: 'short',
                    day: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit',
                    second: '2-digit',
                    hour12: false
                };
                
                timeElement.textContent = time.toLocaleString(undefined, options);
                timeElement.title = `Arduino Time: ${time.toISOString()}`;
                
            } catch (e) {
                console.error('Error updating Arduino time:', e);
                const timeElement = document.getElementById('arduinoTime');
                timeElement.textContent = 'Error';
                timeElement.title = `Error: ${e.message}\nTime: ${String(timeString)}`;
            }
        }
        
        // Update LED state display
        function updateLedState(state) {
            try {
                const ledElement = document.getElementById('ledState');
                
                // Handle different state formats
                if (state === undefined || state === null) {
                    setUnknownState(ledElement);
                    return;
                }
                
                // Convert various state formats to a boolean
                const stateStr = String(state).toLowerCase().trim();
                let isOn;
                
                if (stateStr === 'on' || state === true || state === 1 || state === '1' || stateStr === 'true') {
                    isOn = true;
                } else if (stateStr === 'off' || state === false || state === 0 || state === '0' || stateStr === 'false') {
                    isOn = false;
                } else if (typeof state === 'object' && state.led_state !== undefined) {
                    // Handle nested state object
                    return updateLedState(state.led_state);
                } else {
                    console.warn('Unknown LED state format:', state);
                    setUnknownState(ledElement);
                    return;
                }
                
                // Update the UI
                ledElement.innerHTML = `
                    <div class="flex items-center">
                        <div class="relative w-8 h-4 mr-2 rounded-full transition-colors duration-300 ${isOn ? 'bg-green-400' : 'bg-gray-300'}">
                            <div class="absolute top-0.5 left-0.5 w-3 h-3 bg-white rounded-full shadow-md transform transition-transform duration-300 ${isOn ? 'translate-x-4' : 'translate-x-0'}"></div>
                        </div>
                        <span class="text-sm font-medium ${isOn ? 'text-green-700' : 'text-gray-700'}">
                            ${isOn ? 'ON' : 'OFF'}
                        </span>
                    </div>
                `;
                
                ledElement.title = `LED is ${isOn ? 'ON' : 'OFF'}`;
                
            } catch (e) {
                console.error('Error updating LED state:', e);
                setUnknownState(ledElement);
            }
            
            function setUnknownState(element) {
                element.innerHTML = `
                    <div class="flex items-center">
                        <div class="relative w-8 h-4 mr-2 rounded-full bg-yellow-100">
                            <div class="absolute top-0.5 left-0.5 w-3 h-3 bg-yellow-400 rounded-full shadow-md"></div>
                        </div>
                        <span class="text-sm font-medium text-yellow-700">UNKNOWN</span>
                    </div>
                `;
                element.title = 'LED state unknown or not available';
            }
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
        
        // Update sensor data if available
        function updateSensorData(data) {
            if (!data) return;
            
            try {
                const sensorDataDiv = document.getElementById('sensorData');
                if (!sensorDataDiv) return;
                
                let sensorHtml = '';
                for (const [key, value] of Object.entries(data)) {
                    sensorHtml += `<p><span class="font-medium">${key}:</span> ${JSON.stringify(value)}</p>`;
                }
                sensorDataDiv.innerHTML = sensorHtml || '<p>No sensor data available</p>';
            } catch (error) {
                console.error('Error updating sensor data:', error);
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

        // Update connection status in the UI
        function updateConnectionStatus(isConnected) {
            const statusElement = document.getElementById('connectionStatus');
            if (!statusElement) return;
            
            if (isConnected) {
                statusElement.innerHTML = `
                    <span class="text-green-600">
                        <i class="fas fa-check-circle mr-1"></i> Connected
                    </span>
                `;
            } else {
                statusElement.innerHTML = `
                    <span class="text-red-600">
                        <i class="fas fa-times-circle mr-1"></i> Disconnected
                    </span>
                `;
            }
        }
        
        // Update last message time display
        function updateLastMessageTime(timestamp) {
            const timeElement = document.getElementById('lastMessageTime');
            if (!timeElement) return;
            
            try {
                const date = timestamp ? new Date(timestamp) : new Date();
                timeElement.textContent = date.toLocaleTimeString();
                timeElement.title = `Last message: ${date.toLocaleString()}`;
            } catch (e) {
                console.error('Error updating last message time:', e);
                timeElement.textContent = 'Error';
            }
        }
        
        // Update last updated timestamp
        function updateLastUpdated() {
            const element = document.getElementById('lastUpdated');
            if (element) {
                element.textContent = new Date().toLocaleTimeString();
            }
        }
        
        // Update last received message
        function updateLastReceived(message) {
            try {
                const messageData = typeof message === 'string' ? JSON.parse(message) : message;
                
                // Update Arduino time if available
                if (messageData.time) {
                    updateArduinoTime(messageData.time);
                } else if (messageData.timestamp) {
                    updateArduinoTime(messageData.timestamp);
                }
                
                // Update LED state if available
                if (messageData.led_state !== undefined) {
                    updateLedState(messageData.led_state);
                } else if (messageData.led !== undefined) {
                    updateLedState(messageData.led);
                }
                
                // Update last message time
                updateLastMessageTime(messageData.timestamp || new Date().toISOString());
                
            } catch (e) {
                console.error('Error processing received message:', e);
            }
        }
        
        // Update device status
        async function updateStatus() {
            try {
                const response = await fetch('/api/esp32/status');
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
                const data = await response.json();
                console.log('Status response:', data);
                
                if (data.status !== 'success') {
                    throw new Error(data.message || 'Failed to fetch status');
                }
                
                // Update connection status
                updateConnectionStatus(data.connected || false);
                
                // Update last message time if available
                if (data.last_message_time) {
                    updateLastMessageTime(data.last_message_time);
                }
                
                // Update message count
                if (data.message_count !== undefined) {
                    const countElement = document.getElementById('messageCount');
                    if (countElement) {
                        countElement.textContent = data.message_count;
                    }
                }
                
                // Process any recent messages
                if (data.recent_messages && Array.isArray(data.recent_messages)) {
                    data.recent_messages.forEach(msg => {
                        // Only process new messages
                        if (!lastMessageTime || new Date(msg.created_at) > new Date(lastMessageTime)) {
                            processNewMessage(msg.content, msg.direction || 'incoming');
                            lastMessageTime = msg.created_at;
                        }
                    });
                }
                
                // Update last updated time
                updateLastUpdated();
                
            } catch (error) {
                console.error('Error updating status:', error);
                updateConnectionStatus(false);
                
                // Show error in the UI
                const logsContainer = document.getElementById('messageLogs');
                if (logsContainer) {
                    const errorDiv = document.createElement('div');
                    errorDiv.className = 'text-red-500 text-sm mb-2';
                    errorDiv.textContent = `Status error: ${error.message}`;
                    logsContainer.prepend(errorDiv);
                }
            }
        }
        
        // Update status immediately and then every 5 seconds
        updateStatus();
        setInterval(updateStatus, 5000);
    </script>
</body>
</html>
