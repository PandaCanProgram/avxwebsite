<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\InquiryController;
use App\Http\Controllers\SeoController;
use App\Http\Controllers\TestimonialController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');

Route::view('/projects', 'projects')->name('projects');

Route::view('/investment', 'investment')->name('investment');

Route::get('/sitemap.xml', [SeoController::class, 'sitemap'])->name('sitemap');

Route::get('/robots.txt', [SeoController::class, 'robots'])->name('robots');

Route::post('/contact', [InquiryController::class, 'store'])
    ->middleware('throttle:inquiries')->name('inquiries.contact');

Route::post('/investment/inquiries', [InquiryController::class, 'store'])
    ->middleware('throttle:inquiries')->name('inquiries.investment');

Route::post('/testimonials', [TestimonialController::class, 'store'])
    ->middleware('throttle:5,1')
    ->name('testimonials.store');
