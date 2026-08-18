<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Masuk' }} — E-Learning SDN 102040</title>
    <link rel="icon" href="{{ url('logo.png') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="antialiased min-h-screen bg-canvas">
    <div class="flex flex-col items-center justify-center px-6 py-12 mx-auto min-h-screen">
        {{ $slot }}
    </div>
    @livewireScripts
</body>
</html>