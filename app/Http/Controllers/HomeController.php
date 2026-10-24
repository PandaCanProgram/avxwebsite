<?php

namespace App\Http\Controllers;

use App\Models\Testimonial;
use Illuminate\View\View;

class HomeController extends Controller
{
    /**
     * Show the homepage with the latest client feedback.
     */
    public function __invoke(): View
    {
        return view('welcome', [
            'testimonials' => Testimonial::latest()->take(6)->get(),
        ]);
    }
}
