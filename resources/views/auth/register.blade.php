@extends('layouts.auth-luxury')

@section('title', 'Register - LuxuryFly')

@push('styles')
<script src="https://www.google.com/recaptcha/api.js" async defer></script>
@endpush

@section('content')
<div class="w-full max-w-lg">
    <!-- Card -->
    <div class="bg-zinc-900/90 backdrop-blur-md border border-amber-500/20 rounded-2xl p-8 shadow-2xl shadow-amber-500/5">
        <!-- Logo & Title -->
        <div class="text-center mb-8">
            <a href="{{ route('homepage') }}" class="font-luxury text-3xl font-bold tracking-widest text-amber-500 inline-block mb-2">
                LUXURY<span class="text-white font-light">FLY</span>
            </a>
            <p class="text-zinc-400 text-sm mt-2">Create your premium account</p>
        </div>

        <form method="POST" action="{{ route('register') }}" class="space-y-5">
            @csrf

            <!-- Full Name -->
            <div>
                <label for="name" class="block text-xs uppercase tracking-widest text-amber-500 font-bold mb-2">Full Name</label>
                <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name"
                    class="w-full bg-zinc-950 border @error('name') border-red-500 @else border-zinc-800 @enderror text-white rounded-lg px-4 py-3 focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition placeholder-zinc-600"
                    placeholder="John Doe">
                @error('name')
                    <p class="text-red-400 text-xs mt-1.5">{{ $message }}</p>
                @enderror
            </div>

            <!-- Email -->
            <div>
                <label for="email" class="block text-xs uppercase tracking-widest text-amber-500 font-bold mb-2">Email Address</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="username"
                    class="w-full bg-zinc-950 border @error('email') border-red-500 @else border-zinc-800 @enderror text-white rounded-lg px-4 py-3 focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition placeholder-zinc-600"
                    placeholder="your@email.com">
                @error('email')
                    <p class="text-red-400 text-xs mt-1.5">{{ $message }}</p>
                @enderror
            </div>

            <!-- Password -->
            <div>
                <label for="password" class="block text-xs uppercase tracking-widest text-amber-500 font-bold mb-2">Password</label>
                <input id="password" type="password" name="password" required autocomplete="new-password"
                    class="w-full bg-zinc-950 border @error('password') border-red-500 @else border-zinc-800 @enderror text-white rounded-lg px-4 py-3 focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition placeholder-zinc-600"
                    placeholder="&#8226;&#8226;&#8226;&#8226;&#8226;&#8226;&#8226;&#8226;">
                @error('password')
                    <p class="text-red-400 text-xs mt-1.5">{{ $message }}</p>
                @enderror
            </div>

            <!-- Confirm Password -->
            <div>
                <label for="password_confirmation" class="block text-xs uppercase tracking-widest text-amber-500 font-bold mb-2">Confirm Password</label>
                <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password"
                    class="w-full bg-zinc-950 border @error('password_confirmation') border-red-500 @else border-zinc-800 @enderror text-white rounded-lg px-4 py-3 focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition placeholder-zinc-600"
                    placeholder="&#8226;&#8226;&#8226;&#8226;&#8226;&#8226;&#8226;&#8226;">
                @error('password_confirmation')
                    <p class="text-red-400 text-xs mt-1.5">{{ $message }}</p>
                @enderror
            </div>

            <!-- reCAPTCHA -->
            <div class="flex justify-center pt-2">
                <div class="g-recaptcha" data-sitekey="{{ env('NOCAPTCHA_SITEKEY') }}"></div>
            </div>
            @error('g-recaptcha-response')
                <p class="text-red-400 text-xs text-center">{{ $message }}</p>
            @enderror

            <!-- Terms -->
            <label class="flex items-start gap-3 cursor-pointer group">
                <input type="checkbox" name="terms" required class="mt-0.5 rounded bg-zinc-800 border-zinc-700 text-amber-500 focus:ring-amber-500 focus:ring-offset-0 cursor-pointer">
                <span class="text-sm text-zinc-400 group-hover:text-zinc-300 transition">
                    I agree to the
                    <a href="#" class="text-amber-500 hover:text-amber-400 underline underline-offset-2">Terms & Conditions</a>
                    and
                    <a href="#" class="text-amber-500 hover:text-amber-400 underline underline-offset-2">Privacy Policy</a>
                </span>
            </label>

            <!-- Submit Button -->
            <button type="submit" class="w-full bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-600 hover:to-amber-700 text-black font-bold uppercase tracking-widest text-sm py-3.5 px-4 rounded-lg transition duration-300 shadow-lg shadow-amber-500/20 transform hover:-translate-y-0.5">
                Create Account
            </button>

            <!-- Login Link -->
            <p class="text-center text-zinc-500 text-sm mt-6">
                Already have an account?
                <a href="{{ route('login') }}" class="text-amber-500 hover:text-amber-400 font-semibold transition">Sign In</a>
            </p>
        </form>
    </div>
</div>
@endsection