<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Panel') - {{ config('app.name', 'LuxuryFly') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;700&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
    <style>
        .font-luxury { font-family: 'Cinzel', serif; }
        body { font-family: 'Inter', sans-serif; }
        .sidebar-scroll::-webkit-scrollbar { width: 4px; }
        .sidebar-scroll::-webkit-scrollbar-track { background: transparent; }
        .sidebar-scroll::-webkit-scrollbar-thumb { background: rgba(245, 158, 11, 0.2); border-radius: 20px; }
        .glass-card { background: rgba(24, 24, 27, 0.8); backdrop-filter: blur(12px); border: 1px solid rgba(245, 158, 11, 0.1); }
        .stat-card { transition: all 0.3s ease; }
        .stat-card:hover { transform: translateY(-4px); box-shadow: 0 20px 40px rgba(0,0,0,0.3); }
        .nav-link { transition: all 0.2s ease; position: relative; }
        .nav-link::before { content: ''; position: absolute; left: 0; top: 50%; transform: translateY(-50%); width: 3px; height: 0; background: #f59e0b; border-radius: 0 3px 3px 0; transition: height 0.2s ease; }
        .nav-link:hover::before, .nav-link.active::before { height: 60%; }
        .nav-link.active { background: rgba(245, 158, 11, 0.08); color: #f59e0b; border: 1px solid rgba(245, 158, 11, 0.15); }
    </style>
</head>
<body class="font-sans antialiased bg-zinc-950">
    <div class="min-h-screen flex">
        <!-- Sidebar -->
        <aside class="w-64 bg-black border-r border-amber-500/10 fixed h-full z-40 flex flex-col">
            <!-- Logo -->
            <div class="p-6 border-b border-amber-500/10">
                <a href="{{ route('admin.dashboard') }}" class="font-luxury text-xl font-bold tracking-widest text-amber-500">
                    LUXURY<span class="text-white font-light">FLY</span>
                    <span class="block text-xs text-zinc-500 font-sans font-normal tracking-normal mt-1">Admin Panel</span>
                </a>
            </div>

            <!-- Navigation -->
            <nav class="flex-1 p-4 space-y-0.5 overflow-y-auto sidebar-scroll">

                <!-- Dashboard -->
                <a href="{{ route('admin.dashboard') }}" class="nav-link flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm {{ request()->routeIs('admin.dashboard') ? 'active' : 'text-zinc-400 hover:text-white hover:bg-zinc-900/50' }} transition">
                    <i class="fas fa-chart-pie w-5 text-center text-sm"></i>
                    <span>Dashboard</span>
                </a>

                <!-- Master Data Section -->
                <div class="pt-6 pb-1.5">
                    <p class="text-[10px] uppercase tracking-[0.2em] text-zinc-600 px-4 font-semibold">Master Data</p>
                </div>

                <a href="{{ route('admin.airports.index') }}" class="nav-link flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm {{ request()->routeIs('admin.airports.*') ? 'active' : 'text-zinc-400 hover:text-white hover:bg-zinc-900/50' }} transition">
                    <i class="fas fa-plane-departure w-5 text-center text-sm"></i>
                    <span>Airports</span>
                </a>
                <a href="{{ route('admin.airlines.index') }}" class="nav-link flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm {{ request()->routeIs('admin.airlines.*') ? 'active' : 'text-zinc-400 hover:text-white hover:bg-zinc-900/50' }} transition">
                    <i class="fas fa-building w-5 text-center text-sm"></i>
                    <span>Airlines</span>
                </a>
                <a href="{{ route('admin.airplanes.index') }}" class="nav-link flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm {{ request()->routeIs('admin.airplanes.*') ? 'active' : 'text-zinc-400 hover:text-white hover:bg-zinc-900/50' }} transition">
                    <i class="fas fa-plane w-5 text-center text-sm"></i>
                    <span>Airplanes</span>
                </a>
                <a href="{{ route('admin.seats.index') }}" class="nav-link flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm {{ request()->routeIs('admin.seats.*') ? 'active' : 'text-zinc-400 hover:text-white hover:bg-zinc-900/50' }} transition">
                    <i class="fas fa-chair w-5 text-center text-sm"></i>
                    <span>Seats</span>
                </a>
                <a href="{{ route('admin.flights.index') }}" class="nav-link flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm {{ request()->routeIs('admin.flights.*') ? 'active' : 'text-zinc-400 hover:text-white hover:bg-zinc-900/50' }} transition">
                    <i class="fas fa-ticket-alt w-5 text-center text-sm"></i>
                    <span>Flights</span>
                </a>

                <!-- Users Section -->
                <div class="pt-6 pb-1.5">
                    <p class="text-[10px] uppercase tracking-[0.2em] text-zinc-600 px-4 font-semibold">Users</p>
                </div>

                <a href="{{ route('admin.users.index') }}" class="nav-link flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm {{ request()->routeIs('admin.users.*') ? 'active' : 'text-zinc-400 hover:text-white hover:bg-zinc-900/50' }} transition">
                    <i class="fas fa-users w-5 text-center text-sm"></i>
                    <span>User Management</span>
                </a>

                <!-- CMS Section -->
                <div class="pt-6 pb-1.5">
                    <p class="text-[10px] uppercase tracking-[0.2em] text-zinc-600 px-4 font-semibold">CMS</p>
                </div>

                <a href="{{ route('admin.cms.banners.index') }}" class="nav-link flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm {{ request()->routeIs('admin.cms.banners.*') ? 'active' : 'text-zinc-400 hover:text-white hover:bg-zinc-900/50' }} transition">
                    <i class="fas fa-images w-5 text-center text-sm"></i>
                    <span>Banners</span>
                </a>
                <a href="{{ route('admin.cms.destinations.index') }}" class="nav-link flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm {{ request()->routeIs('admin.cms.destinations.*') ? 'active' : 'text-zinc-400 hover:text-white hover:bg-zinc-900/50' }} transition">
                    <i class="fas fa-map-marker-alt w-5 text-center text-sm"></i>
                    <span>Destinations</span>
                </a>
                <a href="{{ route('admin.cms.testimonials.index') }}" class="nav-link flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm {{ request()->routeIs('admin.cms.testimonials.*') ? 'active' : 'text-zinc-400 hover:text-white hover:bg-zinc-900/50' }} transition">
                    <i class="fas fa-star w-5 text-center text-sm"></i>
                    <span>Testimonials</span>
                </a>
                <a href="{{ route('admin.cms.faqs.index') }}" class="nav-link flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm {{ request()->routeIs('admin.cms.faqs.*') ? 'active' : 'text-zinc-400 hover:text-white hover:bg-zinc-900/50' }} transition">
                    <i class="fas fa-question-circle w-5 text-center text-sm"></i>
                    <span>FAQs</span>
                </a>

                <!-- Footer Links -->
                <div class="pt-6 mt-6 border-t border-zinc-800/50">
                    <a href="{{ route('homepage') }}" target="_blank" class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm text-zinc-500 hover:text-white hover:bg-zinc-900/50 transition">
                        <i class="fas fa-external-link-alt w-5 text-center text-sm"></i>
                        <span>View Website</span>
                    </a>
                    <form method="POST" action="{{ route('logout') }}" class="mt-1">
                        @csrf
                        <button type="submit" class="w-full flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm text-zinc-500 hover:text-red-400 hover:bg-red-500/5 transition">
                            <i class="fas fa-sign-out-alt w-5 text-center text-sm"></i>
                            <span>Logout</span>
                        </button>
                    </form>
                </div>
            </nav>
        </aside>

        <!-- Main Content Area -->
        <div class="flex-1 ml-64 flex flex-col min-h-screen">
            <!-- Top Bar -->
            <header class="bg-black/80 backdrop-blur-md border-b border-amber-500/10 sticky top-0 z-30">
                <div class="flex items-center justify-between px-8 py-3.5">
                    <div class="flex items-center gap-4">
                        <h1 class="text-lg font-semibold text-white tracking-tight">@yield('title', 'Dashboard')</h1>
                    </div>
                    <div class="flex items-center gap-4">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 bg-amber-500/20 rounded-full flex items-center justify-center">
                                <i class="fas fa-user text-amber-500 text-xs"></i>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-medium text-white leading-tight">{{ auth()->user()->name }}</p>
                                <p class="text-[10px] text-zinc-500 uppercase tracking-wider">{{ auth()->user()->role }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1 p-8">
                <!-- Alerts -->
                @if(session('success'))
                    <div class="mb-6 p-4 bg-emerald-500/10 border border-emerald-500/20 rounded-xl text-emerald-400 text-sm flex items-center gap-3">
                        <i class="fas fa-check-circle text-emerald-400"></i>
                        <span>{{ session('success') }}</span>
                    </div>
                @endif
                @if(session('error'))
                    <div class="mb-6 p-4 bg-red-500/10 border border-red-500/20 rounded-xl text-red-400 text-sm flex items-center gap-3">
                        <i class="fas fa-exclamation-circle text-red-400"></i>
                        <span>{{ session('error') }}</span>
                    </div>
                @endif
                @yield('content')
            </main>

            <!-- Footer -->
            <footer class="px-8 py-4 border-t border-zinc-800/50">
                <p class="text-xs text-zinc-600 text-center">
                    &copy; {{ date('Y') }} <span class="text-amber-500/70 font-luxury">LUXURYFLY</span> Admin Panel. All rights reserved.
                </p>
            </footer>
        </div>
    </div>
    @stack('scripts')
</body>
</html>