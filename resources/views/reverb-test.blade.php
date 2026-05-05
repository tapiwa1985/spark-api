<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Reverb Test Page</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 24px;
            background: #f6f8fa;
            color: #111827;
        }
        .container {
            max-width: 900px;
            margin: 0 auto;
            background: #ffffff;
            padding: 20px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
        }
        h1 {
            margin-top: 0;
        }
        .grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
        }
        label {
            display: block;
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 6px;
        }
        input, textarea {
            width: 100%;
            box-sizing: border-box;
            padding: 8px;
            border: 1px solid #d1d5db;
            border-radius: 4px;
        }
        textarea {
            min-height: 80px;
            resize: vertical;
        }
        .actions {
            margin: 16px 0;
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }
        button {
            padding: 10px 12px;
            border: 1px solid #111827;
            background: #111827;
            color: white;
            border-radius: 4px;
            cursor: pointer;
        }
        button.secondary {
            background: #ffffff;
            color: #111827;
        }
        pre {
            background: #0f172a;
            color: #cbd5e1;
            padding: 12px;
            border-radius: 6px;
            max-height: 320px;
            overflow: auto;
            white-space: pre-wrap;
            word-break: break-word;
        }
    </style>
</head>
<body>
<div class="container">
    <h1>Reverb Chat Test</h1>
    <p>Use this page to connect to Reverb, authorize a private channel, subscribe, and send a chat message.</p>

    <div class="grid">
        @php
            $reverbConnection = config('broadcasting.connections.reverb', []);
            $reverbHost = data_get($reverbConnection, 'options.host', '127.0.0.1');
            $reverbPort = (string) data_get($reverbConnection, 'options.port', 8081);
            $reverbScheme = data_get($reverbConnection, 'options.scheme', 'http');
            $reverbAppKey = (string) data_get($reverbConnection, 'key', '');
            $wsScheme = $reverbScheme === 'https' ? 'wss' : 'ws';
            $wsUrl = sprintf(
                '%s://%s:%s/app/%s?protocol=7&client=js&version=8.4.0&flash=false',
                $wsScheme,
                $reverbHost,
                $reverbPort,
                rawurlencode($reverbAppKey)
            );
        @endphp

        <div>
            <label for="ws-url">WebSocket URL</label>
            <input id="ws-url" value="{{ $wsUrl }}">
        </div>
        <div>
            <label for="api-base-url">API Base URL</label>
            <input id="api-base-url" value="{{ url('') }}">
        </div>
        <div style="grid-column: 1 / -1;">
            <label for="jwt-token">JWT Token (Bearer)</label>
            <textarea id="jwt-token" placeholder="Paste JWT token"></textarea>
        </div>
        <div>
            <label for="match-id">Match ID</label>
            <input id="match-id" value="1">
        </div>
        <div>
            <label for="channel-name">Channel Name</label>
            <input id="channel-name" value="private-chat.1">
        </div>
        <div style="grid-column: 1 / -1;">
            <label for="message-text">Message</label>
            <input id="message-text" value="Hello from reverb test page">
        </div>
    </div>

    <div class="actions">
        <button id="connect-btn">1) Connect WS</button>
        <button id="authorize-btn" class="secondary">2) Authorize Channel</button>
        <button id="subscribe-btn" class="secondary">3) Subscribe</button>
        <button id="send-btn">4) Send Message</button>
        <button id="clear-log-btn" class="secondary">Clear Log</button>
    </div>

    <pre id="log"></pre>
</div>

<script>
    const logElement = document.getElementById('log');
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    let ws = null;
    let socketId = null;
    let channelAuth = null;

    const wsUrlInput = document.getElementById('ws-url');
    const apiBaseUrlInput = document.getElementById('api-base-url');
    const jwtTokenInput = document.getElementById('jwt-token');
    const matchIdInput = document.getElementById('match-id');
    const channelNameInput = document.getElementById('channel-name');
    const messageTextInput = document.getElementById('message-text');

    function logLine(message, data = null) {
        const timestamp = new Date().toISOString();
        const line = data ? `${timestamp} ${message} ${JSON.stringify(data)}` : `${timestamp} ${message}`;
        logElement.textContent += `${line}\n`;
        logElement.scrollTop = logElement.scrollHeight;
    }

    function buildAuthHeaders() {
        const token = jwtTokenInput.value.trim();
        const headers = {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': csrfToken,
        };

        if (token) {
            headers['Authorization'] = `Bearer ${token}`;
        }

        return headers;
    }

    function getChannelName() {
        const matchId = matchIdInput.value.trim();
        const channelName = `private-chat.${matchId}`;
        channelNameInput.value = channelName;
        return channelName;
    }

    document.getElementById('connect-btn').addEventListener('click', () => {
        const url = wsUrlInput.value.trim();
        if (!url) {
            logLine('Missing WebSocket URL');
            return;
        }

        if (ws && ws.readyState === WebSocket.OPEN) {
            ws.close();
        }

        socketId = null;
        channelAuth = null;
        ws = new WebSocket(url);

        ws.onopen = () => logLine('WebSocket connected');
        ws.onerror = () => logLine('WebSocket error (browser hides details; check close code)');
        ws.onclose = (evt) => logLine('WebSocket closed', {
            code: evt.code,
            reason: evt.reason || '(empty)',
            wasClean: evt.wasClean,
        });

        ws.onmessage = (event) => {
            let payload = null;
            try {
                payload = JSON.parse(event.data);
            } catch (e) {
                logLine('WS raw', { data: event.data });
                return;
            }

            if (payload.event === 'pusher:connection_established') {
                const data = JSON.parse(payload.data);
                socketId = data.socket_id;
                logLine('Socket established', { socket_id: socketId });
                return;
            }

            if (payload.event === 'pusher:error') {
                logLine('Pusher error', payload);
                return;
            }

            logLine('WS event', payload);
        };
    });

    document.getElementById('authorize-btn').addEventListener('click', async () => {
        const apiBase = apiBaseUrlInput.value.trim().replace(/\/+$/, '');
        const channelName = getChannelName();

        if (!socketId) {
            logLine('Cannot authorize: socket_id is missing. Connect WS first.');
            return;
        }

        const body = new URLSearchParams();
        body.append('channel_name', channelName);
        body.append('socket_id', socketId);

        try {
            const response = await fetch(`${apiBase}/broadcasting/auth`, {
                method: 'POST',
                headers: {
                    ...buildAuthHeaders(),
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body,
            });

            const json = await response.json().catch(() => ({}));
            if (!response.ok) {
                logLine('Auth failed', { status: response.status, response: json });
                return;
            }

            channelAuth = json.auth;
            logLine('Auth success', json);
        } catch (error) {
            logLine('Auth request error', { message: error.message });
        }
    });

    document.getElementById('subscribe-btn').addEventListener('click', () => {
        const channelName = getChannelName();

        if (!ws || ws.readyState !== WebSocket.OPEN) {
            logLine('Cannot subscribe: WebSocket is not connected.');
            return;
        }

        if (!channelAuth) {
            logLine('Cannot subscribe: authorize channel first.');
            return;
        }

        const subscribePayload = {
            event: 'pusher:subscribe',
            data: {
                channel: channelName,
                auth: channelAuth,
            },
        };

        ws.send(JSON.stringify(subscribePayload));
        logLine('Subscribe sent', subscribePayload);
    });

    document.getElementById('send-btn').addEventListener('click', async () => {
        const apiBase = apiBaseUrlInput.value.trim().replace(/\/+$/, '');
        const matchId = Number(matchIdInput.value.trim());
        const message = messageTextInput.value.trim();

        if (!matchId || !message) {
            logLine('Message send requires match id and message.');
            return;
        }

        try {
            const response = await fetch(`${apiBase}/api/v1/chat-messages`, {
                method: 'POST',
                headers: {
                    ...buildAuthHeaders(),
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    user_match_id: matchId,
                    message: message,
                }),
            });

            const json = await response.json().catch(() => ({}));
            logLine('Send message response', { status: response.status, response: json });
        } catch (error) {
            logLine('Send message error', { message: error.message });
        }
    });

    document.getElementById('clear-log-btn').addEventListener('click', () => {
        logElement.textContent = '';
    });
</script>
</body>
</html>
