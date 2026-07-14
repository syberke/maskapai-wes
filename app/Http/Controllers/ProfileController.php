<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\Booking;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        $user = $request->user();
        
        // Calculate statistics
        $stats = [
            'total_flights' => Booking::where('user_id', $user->id)->count(),
            'completed_flights' => Booking::where('user_id', $user->id)->whereIn('status', ['issued', 'paid'])->count(),
            'upcoming_flights' => Booking::where('user_id', $user->id)->where('status', '!=', 'cancelled')->whereHas('flight', function($q) {
                $q->where('departure_time', '>', now());
            })->count(),
            'cancelled_flights' => Booking::where('user_id', $user->id)->where('status', 'cancelled')->count(),
            'total_spending' => Booking::where('user_id', $user->id)->whereHas('payment', function($q) {
                $q->where('payment_status', 'paid');
            })->sum('total_price'),
            'reward_points' => $user->reward_points ?? 0,
        ];
        
        // Recent bookings
        $recentBookings = Booking::with(['flight.departureAirport', 'flight.arrivalAirport', 'flight.airline'])
            ->where('user_id', $user->id)
            ->latest()
            ->limit(5)
            ->get();
        
        return view('profile.edit', [
            'user' => $user,
            'stats' => $stats,
            'recentBookings' => $recentBookings,
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
