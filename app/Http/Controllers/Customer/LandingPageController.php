<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Airport;
use App\Models\Cms\Banner;
use App\Models\Cms\Destination;
use App\Models\Cms\Testimonial;
use App\Models\Cms\Faq;
use Illuminate\View\View;

class LandingPageController extends Controller
{
    public function index(): View
    {
        // Ambil data komponen CMS yang aktif dari database
        $banners = Banner::where('is_active', true)->get();
        $featuredDestinations = Destination::where('is_featured', true)->take(6)->get();
        $testimonials = Testimonial::latest()->take(4)->get();
        $faqs = Faq::all();
        
        // Ambil data bandara untuk kebutuhan input Dropdown Search Form
        $airports = Airport::orderBy('city', 'asc')->get();

        return view('welcome', compact('banners', 'featuredDestinations', 'testimonials', 'faqs', 'airports'));
    }
}