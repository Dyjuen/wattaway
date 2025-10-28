<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR Code Label - {{ $token->serial_number }}</title>
    <style>
        body {
            font-family: sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            background-color: #f0f0f0;
        }
        .label-container {
            border: 1px solid #ccc;
            padding: 20px;
            text-align: center;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            page-break-after: always;
        }
        .qr-code {
            margin-bottom: 15px;
        }
        .serial-number {
            font-size: 1.2em;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .token-value {
            font-size: 0.9em;
            color: #555;
        }
        @media print {
            body {
                background-color: #fff;
            }
            .label-container {
                border: none;
                box-shadow: none;
            }
        }
    </style>
</head>
<body>
    <div class="label-container">
        <img src="{{ $qrCode }}" alt="QR Code" class="qr-code">
        <div class="serial-number">{{ $token->serial_number }}</div>
        <div class="token-value">{{ $token->token }}</div>
        @if (isset($token->metadata['batch']))
            <div class="batch-info">Batch: {{ $token->metadata['batch'] }}</div>
        @endif
    </div>
</body>
</html>
