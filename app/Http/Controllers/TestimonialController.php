<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTestimonialRequest;
use App\Models\Testimonial;
use Illuminate\Http\RedirectResponse;

class TestimonialController extends Controller
{
    /**
     * Store a newly submitted testimonial.
     */
    public function store(StoreTestimonialRequest $request): RedirectResponse
    {
        Testimonial::create($request->safe()->except('website'));

        return redirect()->to(route('home').'#testimonials')
            ->with('testimonialSubmitted', 'Thank you! Your feedback has been added.');
    }
}
