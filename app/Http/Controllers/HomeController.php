<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class HomeController extends Controller
{
    /**
     * Show the homepage with the curated, static client feedback.
     */
    public function __invoke(): View
    {
        return view('welcome', [
            'testimonials' => collect(config('testimonials.items', []))
                ->map(fn (array $testimonial): object => (object) $testimonial),
        ]);
    }
}
