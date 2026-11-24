<?php

use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\AdminJobOrderController;
use App\Http\Controllers\AdminMessageController;
use App\Http\Controllers\AdminSessionController;
use App\Http\Controllers\AdminSettingsController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\AlphaRegistrationController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InquiryController;
use App\Http\Controllers\SeoController;
use App\Http\Controllers\TestimonialController;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');

Route::prefix('admin')->name('admin.')->group(function (): void {
    Route::middleware('guest')->group(function (): void {
        Route::get('/login', [AdminSessionController::class, 'create'])->name('login');
        Route::post('/login', [AdminSessionController::class, 'store'])
            ->middleware('throttle:20,1')->name('login.store');
    });

    Route::middleware(['auth', 'auth.session', 'admin'])->group(function (): void {
        Route::get('/', AdminDashboardController::class)->name('dashboard');
        Route::post('/logout', [AdminSessionController::class, 'destroy'])->name('logout');
        Route::resource('users', AdminUserController::class)->only(['index', 'create', 'store', 'edit', 'update']);
        Route::resource('job-orders', AdminJobOrderController::class)->only(['index', 'create', 'store', 'edit', 'update']);
        Route::get('/messages/direct/create', [AdminMessageController::class, 'createDirect'])->name('messages.direct.create');
        Route::post('/messages/direct', [AdminMessageController::class, 'storeDirect'])->name('messages.direct.store');
        Route::resource('messages', AdminMessageController::class)->parameters(['messages' => 'group'])
            ->only(['index', 'create', 'store', 'show', 'edit', 'update']);
        Route::get('/messages/{group}/feed', [AdminMessageController::class, 'feed'])->name('messages.feed');
        Route::post('/messages/{group}/send', [AdminMessageController::class, 'send'])
            ->middleware('throttle:60,1')->name('messages.send');
        Route::get('/settings', [AdminSettingsController::class, 'edit'])->name('settings');
        Route::patch('/settings/password', [AdminSettingsController::class, 'updatePassword'])
            ->middleware('throttle:10,1')->name('settings.password');
        Route::patch('/settings/appearance', [AdminSettingsController::class, 'updateAppearance'])->name('settings.appearance');
        Route::patch('/settings/maintenance', [AdminSettingsController::class, 'updateMaintenance'])
            ->middleware('throttle:10,1')->name('settings.maintenance');
    });
});

Route::redirect('/favicon.ico', '/avx-logo.png', 301);

Route::post('/alpha-registration', AlphaRegistrationController::class)
    ->middleware('throttle:alpha-registrations')->name('alpha-registration.store');

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
