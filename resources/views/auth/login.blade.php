@extends('layouts.auth-luxury')

@section('title', 'Login - LuxuryFly')

@section('content')
<div class="w-full max-w-md">
    <!-- Session Status -->
    @if(session('status'))
        <div class="mb-6 p-4 bg-emerald-500/10 border border-emerald-500/20 rounded-lg text-emerald-400 text-sm text-center">
            {{ session('status') }}
        </div>
    @endif

    <!-- Card -->
    <div class="bg-zinc-900/90 backdrop-blur-md border border-amber-500/20 rounded-2xl p-8 shadow-2xl shadow-amber-500/5">
        <!-- Logo & Title -->
        <div class="text-center mb-8">
            <a href="{{ route('homepage') }}" class="font-luxury text-3xl font-bold tracking-widest text-amber-500 inline-block mb-2">
                LUXURY<span class="text-white font-light">FLY</span>
            </a>
            <p class="text-zinc-400 text-sm mt-2">Welcome back to premium travel</p>
        </div>

        <form method="POST" action="{{ route('login') }}" class="space-y-5">
            @csrf

            <!-- Email -->
            <div>
                <label for="email" class="block text-xs uppercase tracking-widest text-amber-500 font-bold mb-2">Email Address</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                    class="w-full bg-zinc-950 border @error('email') border-red-500 @else border-zinc-800 @enderror text-white rounded-lg px-4 py-3 focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition placeholder-zinc-600"
                    placeholder="your@email.com">
                @error('email')
                    <p class="text-red-400 text-xs mt-1.5">{{ $message }}</p>
                @enderror
            </div>

            <!-- Password -->
            <div>
                <label for="password" class="block text-xs uppercase tracking-widest text-amber-500 font-bold mb-2">Password</label>
                <input id="password" type="password" name="password" required autocomplete="current-password"
                    class="w-full bg-zinc-950 border @error('password') border-red-500 @else border-zinc-800 @enderror text-white rounded-lg px-4 py-3 focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition placeholder-zinc-600"
                    placeholder="&#8226;&#8226;&#8226;&#8226;&#8226;&#8226;&#8226;&#8226;">
                @error('password')
                    <p class="text-red-400 text-xs mt-1.5">{{ $message }}</p>
                @enderror
            </div>

            <!-- Remember Me & Forgot Password -->
            <div class="flex items-center justify-between">
                <label for="remember_me" class="flex items-center gap-2 cursor-pointer group">
                    <input id="remember_me" type="checkbox" name="remember" class="rounded bg-zinc-800 border-zinc-700 text-amber-500 focus:ring-amber-500 focus:ring-offset-0 cursor-pointer">
                    <span class="text-sm text-zinc-400 group-hover:text-zinc-300 transition">Remember me</span>
                </label>
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="text-sm text-amber-500 hover:text-amber-400 transition underline underline-offset-2">
                        Forgot Password?
                    </a>
                @endif
            </div>

            <!-- Submit Button -->
            <button type="submit" class="w-full bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-600 hover:to-amber-700 text-black font-bold uppercase tracking-widest text-sm py-3.5 px-4 rounded-lg transition duration-300 shadow-lg shadow-amber-500/20 transform hover:-translate-y-0.5">
                Sign In
            </button>

            <!-- Register Link -->
            <p class="text-center text-zinc-500 text-sm mt-6">
                Don't have an account?
                <a href="{{ route('register') }}" class="text-amber-500 hover:text-amber-400 font-semibold transition">Register</a>
            </p>
        </form>
    </div>
</div>
@endsection