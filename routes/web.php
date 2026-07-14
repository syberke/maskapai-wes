<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\AirportController;
use App\Http\Controllers\Admin\AirlineController;
use App\Http\Controllers\Admin\AirplaneController;
use App\Http\Controllers\Admin\SeatController;
use App\Http\Controllers\Admin\FlightController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\Cms\BannerController;
use App\Http\Controllers\Admin\Cms\DestinationController;
use App\Http\Controllers\Admin\Cms\TestimonialController;
use App\Http\Controllers\Admin\Cms\FaqController;

use App\Http\Controllers\Customer\LandingPageController;
use App\Http\Controllers\Customer\FlightSearchController;
use App\Http\Controllers\Customer\DashboardController as CustomerDashboardController;
use App\Http\Controllers\Customer\BookingController;
use App\Http\Controllers\Customer\PaymentController;
use App\Http\Controllers\Customer\MidtransController;

use App\Http\Controllers\Staff\DashboardController as StaffDashboardController;
use App\Http\Controllers\Staff\ManifestController;
use App\Http\Controllers\Staff\FlightMonitoringController;

use App\Http\Controllers\Manager\AnalyticsController;
use App\Http\Controllers\Manager\ReportController;
use App\Http\Controllers\Manager\ReportExportController;

Route::get('/', [LandingPageController::class, 'index'])->name('homepage');
Route::get('/flights/search', [FlightSearchController::class, 'search'])->name('flights.search');

Route::middleware(['auth', 'verified'])->prefix('customer')->name('customer.')->group(function () {
    Route::get('/dashboard', [CustomerDashboardController::class, 'index'])->name('dashboard');
    Route::get('/flights/{flight}/seats', [BookingController::class, 'selectSeats'])->name('flights.seats');
    Route::post('/flights/{flight}/passengers', [BookingController::class, 'passengerForm'])->name('flights.passengers');
    Route::post('/bookings', [BookingController::class, 'store'])->name('bookings.store');
    Route::get('/bookings', [BookingController::class, 'history'])->name('bookings.history');
    Route::get('/bookings/{booking}', [BookingController::class, 'show'])->name('booking.show');
    Route::get('/payments', [PaymentController::class, 'index'])->name('payments.index');
    Route::get('/payments/{booking}', [PaymentController::class, 'show'])->name('payment.show');
    Route::post('/payments/{booking}/process', [PaymentController::class, 'process'])->name('payment.process');
    Route::get('/payments/{booking}/success', [PaymentController::class, 'success'])->name('payment.success');
    Route::get('/eticket/{booking}', [PaymentController::class, 'eticket'])->name('eticket');
    Route::post('/midtrans/pay/{booking}', [MidtransController::class, 'pay'])->name('midtrans.pay');
    Route::post('/midtrans/callback', [MidtransController::class, 'callback'])->name('midtrans.callback');
});

Route::middleware(['auth', 'verified', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::resource('airports', AirportController::class);
    Route::resource('airlines', AirlineController::class);
    Route::resource('airplanes', AirplaneController::class);
    Route::resource('seats', SeatController::class);
    Route::post('/seats/generate', [SeatController::class, 'generate'])->name('seats.generate');
    Route::resource('flights', FlightController::class);
    Route::resource('users', UserController::class);
    Route::prefix('cms')->name('cms.')->group(function () {
        Route::resource('banners', BannerController::class);
        Route::resource('destinations', DestinationController::class);
        Route::resource('testimonials', TestimonialController::class);
        Route::resource('faqs', FaqController::class);
    });
});

Route::middleware(['auth', 'verified', 'role:staff'])->prefix('staff')->name('staff.')->group(function () {
    Route::get('/dashboard', [StaffDashboardController::class, 'index'])->name('dashboard');
    Route::get('/manifest', [ManifestController::class, 'index'])->name('manifest');
    Route::get('/manifest/{flight}', [ManifestController::class, 'show'])->name('manifest.show');
    Route::get('/flights', [FlightMonitoringController::class, 'index'])->name('flights');
});

Route::middleware(['auth', 'verified', 'role:manager'])->prefix('manager')->name('manager.')->group(function () {
    Route::get('/analytics', [AnalyticsController::class, 'index'])->name('analytics');
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/revenue', [ReportController::class, 'revenue'])->name('revenue');
        Route::get('/bookings', [ReportController::class, 'bookings'])->name('bookings');
        Route::get('/passengers', [ReportController::class, 'passengers'])->name('passengers');
        Route::get('/occupancy', [ReportController::class, 'occupancy'])->name('occupancy');
        Route::get('/airline-performance', [ReportController::class, 'airlinePerformance'])->name('airline-performance');
        Route::get('/route-performance', [ReportController::class, 'routePerformance'])->name('route-performance');
        Route::get('/{report}/export/pdf', [ReportExportController::class, 'pdf'])->name('export.pdf');
        Route::get('/{report}/export/excel', [ReportExportController::class, 'excel'])->name('export.excel');
    });
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', function () {
        $user = auth()->user();
        if ($user->role === 'admin') return redirect()->route('admin.dashboard');
        if ($user->role === 'manager') return redirect()->route('manager.analytics');
        if ($user->role === 'staff') return redirect()->route('staff.dashboard');
        if ($user->role === 'customer') return redirect()->route('customer.dashboard');
        return view('dashboard');
    })->name('dashboard');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
