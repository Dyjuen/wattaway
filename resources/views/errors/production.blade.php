
@extends('layouts.base')

@section('title', 'Error')

@section('content')
<body class="bg-gray-100 flex items-center justify-center h-screen">
    <div class="text-center">
        <h1 class="text-4xl font-bold text-gray-800">Sorry, we have encountered a problem.</h1>
        <p class="text-gray-600 mt-4">An unexpected error has occurred. Please try again later.</p>
        <a href="{{ route('home') }}" class="mt-8 inline-block bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
            Go back to Home
        </a>
    </div>
</body>
@endsection
