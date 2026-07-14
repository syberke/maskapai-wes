@extends('layouts.customer')
@section('title', 'My Profile')
@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    <!-- Page Header -->
    <div class="space-y-4">
        <nav class="flex items-center space-x-2 text-sm">
            <a href="{{ route('customer.dashboard') }}" class="text-zinc-400 hover:text-amber-500 transition">Home</a>
            <span class="text-zinc-600">›</span>
            <a href="{{ route('customer.dashboard') }}" class="text-zinc-400 hover:text-amber-500 transition">Dashboard</a>
            <span class="text-zinc-600">›</span>
            <span class="text-amber-500 font-medium">Profile</span>
        </nav>
        
        <div>
            <h1 class="text-3xl font-bold text-white tracking-tight">Customer Profile</h1>
            <p class="text-zinc-400 mt-1">Manage your personal information and travel preferences</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left Column - Profile Card & Stats -->
        <div class="lg:col-span-1 space-y-6">
            <!-- Profile Card -->
            <div class="bg-gradient-to-br from-zinc-900 to-zinc-800 border border-zinc-700 rounded-xl p-6 text-center">
                <div class="relative inline-block mb-4">
                    <div class="w-24 h-24 bg-gradient-to-br from-amber-500 to-amber-600 rounded-full flex items-center justify-center text-3xl font-bold text-black">
                        {{ substr($user->name, 0, 2) }}
                    </div>
                    @if($user->email_verified_at)
                    <div class="absolute -bottom-1 -right-1 w-8 h-8 bg-emerald-500 rounded-full flex items-center justify-center border-4 border-zinc-800">
                        <i class="fas fa-check text-white text-xs"></i>
                    </div>
                    @endif
                </div>
                
                <h2 class="text-xl font-bold text-white mb-1">{{ $user->name }}</h2>
                <p class="text-zinc-400 text-sm mb-3">{{ $user->email }}</p>
                
                <div class="inline-flex items-center gap-2 px-4 py-2 bg-amber-500/10 border border-amber-500/20 rounded-full">
                    <i class="fas fa-crown text-amber-500 text-sm"></i>
                    <span class="text-amber-500 text-sm font-semibold capitalize">{{ $user->membership_level ?? 'Silver' }} Member</span>
                </div>
                
                <div class="mt-4 pt-4 border-t border-zinc-700 space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-zinc-400">Phone</span>
                        <span class="text-white">{{ $user->phone ?? 'Not set' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-zinc-400">Country</span>
                        <span class="text-white">{{ $user->nationality ?? 'Not set' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-zinc-400">Member Since</span>
                        <span class="text-white">{{ $user->member_since ? \Carbon\Carbon::parse($user->member_since)->format('M Y') : 'N/A' }}</span>
                    </div>
                </div>
            </div>

            <!-- Statistics -->
            <div class="bg-zinc-900/80 border border-zinc-800 rounded-xl p-6">
                <h3 class="text-base font-semibold text-white mb-4">Travel Statistics</h3>
                <div class="space-y-3">
                    <div class="flex items-center justify-between p-3 bg-zinc-800/50 rounded-lg">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-amber-500/10 rounded-lg flex items-center justify-center">
                                <i class="fas fa-plane text-amber-500"></i>
                            </div>
                            <div>
                                <p class="text-zinc-400 text-xs">Total Flights</p>
                                <p class="text-white font-bold">{{ $stats['total_flights'] }}</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="flex items-center justify-between p-3 bg-zinc-800/50 rounded-lg">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-emerald-500/10 rounded-lg flex items-center justify-center">
                                <i class="fas fa-check-circle text-emerald-500"></i>
                            </div>
                            <div>
                                <p class="text-zinc-400 text-xs">Completed</p>
                                <p class="text-white font-bold">{{ $stats['completed_flights'] }}</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="flex items-center justify-between p-3 bg-zinc-800/50 rounded-lg">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-blue-500/10 rounded-lg flex items-center justify-center">
                                <i class="fas fa-clock text-blue-500"></i>
                            </div>
                            <div>
                                <p class="text-zinc-400 text-xs">Upcoming</p>
                                <p class="text-white font-bold">{{ $stats['upcoming_flights'] }}</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="flex items-center justify-between p-3 bg-zinc-800/50 rounded-lg">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-purple-500/10 rounded-lg flex items-center justify-center">
                                <i class="fas fa-wallet text-purple-500"></i>
                            </div>
                            <div>
                                <p class="text-zinc-400 text-xs">Total Spending</p>
                                <p class="text-white font-bold">Rp {{ number_format($stats['total_spending'], 0, ',', '.') }}</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="flex items-center justify-between p-3 bg-gradient-to-r from-amber-500/10 to-amber-600/5 border border-amber-500/20 rounded-lg">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-amber-500/20 rounded-lg flex items-center justify-center">
                                <i class="fas fa-star text-amber-500"></i>
                            </div>
                            <div>
                                <p class="text-amber-400 text-xs">Reward Points</p>
                                <p class="text-amber-500 font-bold">{{ number_format($stats['reward_points']) }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column - Forms & Info -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Personal Information -->
            <div class="bg-zinc-900/80 border border-zinc-800 rounded-xl p-6">
                <h3 class="text-lg font-semibold text-white mb-4">Personal Information</h3>
                <form method="POST" action="{{ route('profile.update') }}" class="space-y-4">
                    @csrf
                    @method('PATCH')
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-zinc-300 mb-2">First Name</label>
                            <input type="text" name="first_name" value="{{ old('first_name', $user->first_name) }}" class="w-full bg-zinc-800 border border-zinc-700 rounded-lg px-4 py-2.5 text-white focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition">
                            @error('first_name') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-zinc-300 mb-2">Last Name</label>
                            <input type="text" name="last_name" value="{{ old('last_name', $user->last_name) }}" class="w-full bg-zinc-800 border border-zinc-700 rounded-lg px-4 py-2.5 text-white focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition">
                            @error('last_name') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-zinc-300 mb-2">Email</label>
                            <input type="email" name="email" value="{{ old('email', $user->email) }}" class="w-full bg-zinc-800 border border-zinc-700 rounded-lg px-4 py-2.5 text-white focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition">
                            @error('email') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-zinc-300 mb-2">Phone</label>
                            <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" class="w-full bg-zinc-800 border border-zinc-700 rounded-lg px-4 py-2.5 text-white focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition">
                            @error('phone') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-zinc-300 mb-2">Gender</label>
                            <select name="gender" class="w-full bg-zinc-800 border border-zinc-700 rounded-lg px-4 py-2.5 text-white focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition">
                                <option value="">Select</option>
                                <option value="male" {{ old('gender', $user->gender) === 'male' ? 'selected' : '' }}>Male</option>
                                <option value="female" {{ old('gender', $user->gender) === 'female' ? 'selected' : '' }}>Female</option>
                            </select>
                            @error('gender') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-zinc-300 mb-2">Date of Birth</label>
                            <input type="date" name="date_of_birth" value="{{ old('date_of_birth', $user->date_of_birth) }}" class="w-full bg-zinc-800 border border-zinc-700 rounded-lg px-4 py-2.5 text-white focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition">
                            @error('date_of_birth') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-zinc-300 mb-2">Nationality</label>
                            <input type="text" name="nationality" value="{{ old('nationality', $user->nationality) }}" class="w-full bg-zinc-800 border border-zinc-700 rounded-lg px-4 py-2.5 text-white focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition">
                            @error('nationality') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-zinc-300 mb-2">Passport Number</label>
                        <input type="text" name="passport_number" value="{{ old('passport_number', $user->passport_number) }}" class="w-full bg-zinc-800 border border-zinc-700 rounded-lg px-4 py-2.5 text-white focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition">
                        @error('passport_number') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-zinc-300 mb-2">Emergency Contact</label>
                            <input type="text" name="emergency_contact" value="{{ old('emergency_contact', $user->emergency_contact) }}" class="w-full bg-zinc-800 border border-zinc-700 rounded-lg px-4 py-2.5 text-white focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition">
                            @error('emergency_contact') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-zinc-300 mb-2">Emergency Phone</label>
                            <input type="text" name="emergency_phone" value="{{ old('emergency_phone', $user->emergency_phone) }}" class="w-full bg-zinc-800 border border-zinc-700 rounded-lg px-4 py-2.5 text-white focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition">
                            @error('emergency_phone') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="flex items-center gap-3 pt-4">
                        <button type="submit" class="px-6 py-2.5 bg-gradient-to-r from-amber-500 to-amber-600 text-black rounded-xl font-semibold hover:from-amber-600 hover:to-amber-700 transition shadow-lg shadow-amber-500/20">
                            Save Changes
                        </button>
                        @if(session('status') === 'profile-updated')
                        <span class="text-emerald-400 text-sm"><i class="fas fa-check-circle mr-1"></i>Profile updated successfully</span>
                        @endif
                    </div>
                </form>
            </div>

            <!-- Travel Preferences -->
            <div class="bg-zinc-900/80 border border-zinc-800 rounded-xl p-6">
                <h3 class="text-lg font-semibold text-white mb-4">Travel Preferences</h3>
                <form method="POST" action="{{ route('profile.update') }}" class="space-y-4">
                    @csrf
                    @method('PATCH')
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-zinc-300 mb-2">Preferred Cabin</label>
                            <select name="preferred_cabin" class="w-full bg-zinc-800 border border-zinc-700 rounded-lg px-4 py-2.5 text-white focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition">
                                <option value="">Select</option>
                                <option value="economy" {{ old('preferred_cabin', $user->preferred_cabin) === 'economy' ? 'selected' : '' }}>Economy</option>
                                <option value="business" {{ old('preferred_cabin', $user->preferred_cabin) === 'business' ? 'selected' : '' }}>Business</option>
                                <option value="first" {{ old('preferred_cabin', $user->preferred_cabin) === 'first' ? 'selected' : '' }}>First Class</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-zinc-300 mb-2">Preferred Seat</label>
                            <select name="preferred_seat" class="w-full bg-zinc-800 border border-zinc-700 rounded-lg px-4 py-2.5 text-white focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition">
                                <option value="">Select</option>
                                <option value="window" {{ old('preferred_seat', $user->preferred_seat) === 'window' ? 'selected' : '' }}>Window</option>
                                <option value="aisle" {{ old('preferred_seat', $user->preferred_seat) === 'aisle' ? 'selected' : '' }}>Aisle</option>
                                <option value="middle" {{ old('preferred_seat', $user->preferred_seat) === 'middle' ? 'selected' : '' }}>Middle</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-zinc-300 mb-2">Meal Preference</label>
                            <select name="meal_preference" class="w-full bg-zinc-800 border border-zinc-700 rounded-lg px-4 py-2.5 text-white focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition">
                                <option value="">Select</option>
                                <option value="regular" {{ old('meal_preference', $user->meal_preference) === 'regular' ? 'selected' : '' }}>Regular</option>
                                <option value="vegetarian" {{ old('meal_preference', $user->meal_preference) === 'vegetarian' ? 'selected' : '' }}>Vegetarian</option>
                                <option value="halal" {{ old('meal_preference', $user->meal_preference) === 'halal' ? 'selected' : '' }}>Halal</option>
                                <option value="kosher" {{ old('meal_preference', $user->meal_preference) === 'kosher' ? 'selected' : '' }}>Kosher</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-zinc-300 mb-2">Special Assistance</label>
                        <textarea name="special_assistance" rows="3" class="w-full bg-zinc-800 border border-zinc-700 rounded-lg px-4 py-2.5 text-white focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition">{{ old('special_assistance', $user->special_assistance) }}</textarea>
                        @error('special_assistance') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-zinc-300 mb-2">Preferred Language</label>
                            <select name="preferred_language" class="w-full bg-zinc-800 border border-zinc-700 rounded-lg px-4 py-2.5 text-white focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition">
                                <option value="en" {{ old('preferred_language', $user->preferred_language) === 'en' ? 'selected' : '' }}>English</option>
                                <option value="id" {{ old('preferred_language', $user->preferred_language) === 'id' ? 'selected' : '' }}>Indonesian</option>
                                <option value="ar" {{ old('preferred_language', $user->preferred_language) === 'ar' ? 'selected' : '' }}>Arabic</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-zinc-300 mb-2">Timezone</label>
                            <select name="timezone" class="w-full bg-zinc-800 border border-zinc-700 rounded-lg px-4 py-2.5 text-white focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition">
                                <option value="Asia/Jakarta" {{ old('timezone', $user->timezone) === 'Asia/Jakarta' ? 'selected' : '' }}>Jakarta (WIB)</option>
                                <option value="Asia/Makassar" {{ old('timezone', $user->timezone) === 'Asia/Makassar' ? 'selected' : '' }}>Makassar (WITA)</option>
                                <option value="Asia/Jayapura" {{ old('timezone', $user->timezone) === 'Asia/Jayapura' ? 'selected' : '' }}>Jayapura (WIT)</option>
                            </select>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Notification Preferences -->
            <div class="bg-zinc-900/80 border border-zinc-800 rounded-xl p-6">
                <h3 class="text-lg font-semibold text-white mb-4">Notification Preferences</h3>
                <form method="POST" action="{{ route('profile.update') }}" class="space-y-3">
                    @csrf
                    @method('PATCH')
                    
                    <div class="flex items-center justify-between p-4 bg-zinc-800/50 rounded-lg">
                        <div>
                            <p class="text-white font-medium">Email Notifications</p>
                            <p class="text-zinc-400 text-xs">Receive booking confirmations and updates</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="email_notification" class="sr-only peer" {{ old('email_notification', $user->email_notification) ? 'checked' : '' }}>
                            <div class="w-11 h-6 bg-zinc-700 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-amber-500/20 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-amber-500"></div>
                        </label>
                    </div>

                    <div class="flex items-center justify-between p-4 bg-zinc-800/50 rounded-lg">
                        <div>
                            <p class="text-white font-medium">SMS Notifications</p>
                            <p class="text-zinc-400 text-xs">Receive text messages for important updates</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="sms_notification" class="sr-only peer" {{ old('sms_notification', $user->sms_notification) ? 'checked' : '' }}>
                            <div class="w-11 h-6 bg-zinc-700 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-amber-500/20 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-amber-500"></div>
                        </label>
                    </div>

                    <div class="flex items-center justify-between p-4 bg-zinc-800/50 rounded-lg">
                        <div>
                            <p class="text-white font-medium">Flight Reminders</p>
                            <p class="text-zinc-400 text-xs">Get reminded before your flight departs</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="flight_reminder" class="sr-only peer" {{ old('flight_reminder', $user->flight_reminder) ? 'checked' : '' }}>
                            <div class="w-11 h-6 bg-zinc-700 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-amber-500/20 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-amber-500"></div>
                        </label>
                    </div>

                    <div class="flex items-center justify-between p-4 bg-zinc-800/50 rounded-lg">
                        <div>
                            <p class="text-white font-medium">Promotions</p>
                            <p class="text-zinc-400 text-xs">Receive special offers and deals</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="promotion" class="sr-only peer" {{ old('promotion', $user->promotion) ? 'checked' : '' }}>
                            <div class="w-11 h-6 bg-zinc-700 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-amber-500/20 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-amber-500"></div>
                        </label>
                    </div>

                    <div class="flex items-center justify-between p-4 bg-zinc-800/50 rounded-lg">
                        <div>
                            <p class="text-white font-medium">Newsletter</p>
                            <p class="text-zinc-400 text-xs">Subscribe to our monthly newsletter</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="newsletter" class="sr-only peer" {{ old('newsletter', $user->newsletter) ? 'checked' : '' }}>
                            <div class="w-11 h-6 bg-zinc-700 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-amber-500/20 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-amber-500"></div>
                        </label>
                    </div>
                </form>
            </div>

            <!-- Recent Bookings -->
            <div class="bg-zinc-900/80 border border-zinc-800 rounded-xl overflow-hidden">
                <div class="p-6 border-b border-zinc-800">
                    <h3 class="text-lg font-semibold text-white">Recent Bookings</h3>
                    <p class="text-xs text-zinc-500 mt-1">Your latest 5 bookings</p>
                </div>
                <div class="divide-y divide-zinc-800">
                    @forelse($recentBookings as $booking)
                    <div class="p-4 flex items-center justify-between hover:bg-zinc-800/20 transition">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 bg-amber-500/10 rounded-lg flex items-center justify-center">
                                <span class="text-amber-500 font-bold text-xs">{{ substr($booking->flight->airline->name, 0, 2) }}</span>
                            </div>
                            <div>
                                <p class="text-white font-semibold text-sm">{{ $booking->flight->airline->name }}</p>
                                <p class="text-zinc-400 text-xs">{{ $booking->flight->departureAirport->iata_code }} → {{ $booking->flight->arrivalAirport->iata_code }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-white text-sm font-semibold">{{ \Carbon\Carbon::parse($booking->flight->departure_time)->format('d M Y') }}</p>
                            <span class="px-2 py-0.5 rounded text-xs font-semibold @if($booking->status=='issued' || $booking->status=='paid') bg-emerald-500/10 text-emerald-400 @elseif($booking->status=='pending') bg-yellow-500/10 text-yellow-400 @else bg-red-500/10 text-red-400 @endif">{{ ucfirst($booking->status) }}</span>
                        </div>
                    </div>
                    @empty
                    <div class="p-8 text-center text-zinc-500">
                        <i class="fas fa-calendar-times text-2xl mb-2 block text-zinc-700"></i>
                        No bookings yet
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection