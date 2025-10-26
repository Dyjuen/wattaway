@extends('layouts.admin')

@section('title', 'Message Details')

@section('content')
<div class="bg-white rounded-lg shadow p-6">
    <a href="{{ url()->previous() }}" class="text-indigo-600 hover:text-indigo-800 mb-4 inline-block"><i class="fas fa-arrow-left"></i> Back to Messages</a>
    <h2 class="text-2xl font-bold mb-4">Message Details</h2>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <p><strong>ID:</strong> {{ $message->id }}</p>
            <p><strong>Time:</strong> {{ $message->created_at }}</p>
            <p><strong>Device:</strong> {{ $message->device ? $message->device->name : 'N/A' }}</p>
            <p><strong>Direction:</strong> {{ $message->direction }}</p>
            <p><strong>Type:</strong> {{ $message->type }}</p>
            <p><strong>Status:</strong> {{ $message->status }}</p>
            <p><strong>Topic:</strong> {{ $message->topic ?? 'N/A' }}</p>
            <p><strong>Endpoint:</strong> {{ $message->endpoint ?? 'N/A' }}</p>
            <p><strong>IP Address:</strong> {{ $message->ip_address ?? 'N/A' }}</p>
            <p><strong>Response Code:</strong> {{ $message->response_code ?? 'N/A' }}</p>
        </div>
        <div>
            <h3 class="text-lg font-bold mb-2">Payload</h3>
            <pre class="json-viewer"><code>{{ json_encode($message->payload, JSON_PRETTY_PRINT) }}</code></pre>
            @if($message->error_message)
                <h3 class="text-lg font-bold mt-4 mb-2 text-red-600">Error Message</h3>
                <pre class="json-viewer bg-red-100 text-red-800"><code>{{ $message->error_message }}</code></pre>
            @endif
        </div>
    </div>
</div>
@endsection
