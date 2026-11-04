<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\InquiryController;
use App\Http\Controllers\SeoController;
use App\Http\Controllers\TestimonialController;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');

Route::view('/projects', 'projects')->name('projects');

Route::view('/investment', 'investment')->name('investment');

Route::view('/drone-platforms', 'drone-systems')->name('drone-systems');

Route::get('/drone-systems', function (Request $request): RedirectResponse {
    return redirect()->route('drone-systems', $request->query(), 301);
});

Route::get('/sitemap.xml', [SeoController::class, 'sitemap'])->name('sitemap');

Route::get('/robots.txt', [SeoController::class, 'robots'])->name('robots');

Route::post('/contact', [InquiryController::class, 'store'])
    ->middleware('throttle:inquiries')->name('inquiries.contact');

Route::post('/contact/check-email', [InquiryController::class, 'checkEmail'])
    ->middleware('throttle:inquiry-email-checks')->name('inquiries.check-email');

Route::post('/investment/inquiries', [InquiryController::class, 'store'])
    ->middleware('throttle:inquiries')->name('inquiries.investment');

Route::post('/testimonials', [TestimonialController::class, 'store'])
    ->middleware('throttle:5,1')
    ->name('testimonials.store');
