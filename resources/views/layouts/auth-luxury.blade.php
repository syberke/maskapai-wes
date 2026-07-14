<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'LuxuryFly') - {{ config('app.name', 'LuxuryFly') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;700&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        .font-luxury { font-family: 'Cinzel', serif; }
        body { font-family: 'Inter', sans-serif; }
    </style>
    @stack('styles')
</head>
<body class="bg-zinc-950 text-slate-100 antialiased min-h-screen flex flex-col">
    <!-- Navbar -->
    <nav class="absolute top-0 left-0 w-full z-50 bg-gradient-to-b from-black/80 to-transparent backdrop-blur-sm border-b border-amber-500/10">
        <div class="max-w-7xl mx-auto px-6 py-4 flex justify-between items-center">
            <a href="{{ route('homepage') }}" class="font-luxury text-2xl font-bold tracking-widest text-amber-500">
                LUXURY<span class="text-white font-light">FLY</span>
            </a>
            <div class="flex items-center space-x-6 text-sm tracking-wider uppercase">
                <a href="{{ route('homepage') }}#destinations" class="hover:text-amber-500 transition text-zinc-400 hover:text-amber-500">Destinations</a>
                <a href="{{ route('homepage') }}#testimonials" class="hover:text-amber-500 transition text-zinc-400 hover:text-amber-500">Experience</a>
                <a href="{{ route('homepage') }}#faq" class="hover:text-amber-500 transition text-zinc-400 hover:text-amber-500">FAQ</a>
                @if(!request()->routeIs('login'))
                    <a href="{{ route('login') }}" class="hover:text-amber-500 transition text-zinc-400 hover:text-amber-500">Log In</a>
                @endif
                @if(!request()->routeIs('register'))
                    <a href="{{ route('register') }}" class="bg-amber-500 text-black font-semibold px-4 py-2 rounded hover:bg-amber-600 transition duration-300">Register</a>
                @endif
            </div>
        </div>
    </nav>

    <!-- Hero Background -->
    <div class="fixed inset-0 bg-cover bg-center -z-10" style="background-image: linear-gradient(to bottom, rgba(0,0,0,0.6), rgba(9,9,11,0.95)), url('https://images.unsplash.com/photo-1436491865332-7a61a109cc05?q=80&w=1920');"></div>

    <!-- Main Content -->
    <main class="flex-1 flex items-center justify-center px-4 py-24">
        @yield('content', $slot ?? '')
    </main>
