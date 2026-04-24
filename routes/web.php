<?php

use App\Http\Controllers\Auth\SocialiteController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\StripeController;
use App\Http\Middleware\IsAdmin;
use App\Models\ProfessionalReview;
use App\Models\User;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::get('/auth/{provider}/redirect', [SocialiteController::class, 'redirect'])->name('social.redirect');
Route::get('/auth/{provider}/callback', [SocialiteController::class, 'callback'])->name('social.callback');

Volt::route('/', 'pages.landing')->name('landing');
Volt::route('services/jha', 'pages.services.jha')->name('services.jha');
Volt::route('services/aha', 'pages.services.aha')->name('services.aha');
Volt::route('services/jsa', 'pages.services.jsa')->name('services.jsa');
Volt::route('terms', 'pages.terms')->name('terms');
Volt::route('privacy', 'pages.privacy')->name('privacy');
Volt::route('refund', 'pages.refund')->name('refund');

Volt::route('dashboard', 'pages.user-dashboard')
    ->middleware(['auth', 'verified'])
    ->name('user-dashboard');

Volt::route('dashboard/reviews', 'pages.user-professional-reviews')
    ->middleware(['auth', 'verified'])
    ->name('user.reviews');

Route::middleware(['auth', 'verified', IsAdmin::class])
    ->prefix('admin')
    ->group(function () {
        Volt::route('dashboard', 'pages.admin.dashboard')->name('admin.dashboard');
        Volt::route('pricing', 'pages.admin.pricing')->name('admin.pricing');
        Volt::route('reviews', 'pages.admin.reviews')->name('admin.reviews');
        Volt::route('users', 'pages.admin.users')->name('admin.users');
        Volt::route('template', 'pages.admin.template')->name('admin.template');
        Volt::route('prompts', 'pages.admin.prompts')->name('admin.prompts');
        Volt::route('seo', 'pages.admin.seo-manager')->name('admin.seo');
    });

// Safety System Routes
Volt::route('generate/jha', 'pages.generate-jha')
    ->middleware(['auth', 'verified'])
    ->name('generate.jha');

Volt::route('generate/aha', 'pages.generate-aha')
    ->middleware(['auth', 'verified'])
    ->name('generate.aha');

Volt::route('generate/jsa', 'pages.generate-jsa')
    ->middleware(['auth', 'verified'])
    ->name('generate.jsa');

Volt::route('preview/jha/{id}', 'pages.preview-jha')
    ->middleware(['auth', 'verified'])
    ->name('preview.jha');

Volt::route('preview/aha/{id}', 'pages.preview-aha')
    ->middleware(['auth', 'verified'])
    ->name('preview.aha');

Volt::route('preview/jsa/{id}', 'pages.preview-jsa')
    ->middleware(['auth', 'verified'])
    ->name('preview.jsa');

Volt::route('brief', 'pages.brief')->name('brief');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/stripe/checkout/{document}', [StripeController::class, 'checkout'])->name('stripe.checkout');
    Route::get('/stripe/review-checkout/{review}', [StripeController::class, 'professionalReviewCheckout'])->name('stripe.review-checkout');
    Route::get('/stripe/review-success/{review}', [StripeController::class, 'successReview'])->name('stripe.review-success');
    Route::get('/stripe/success/{document}', [StripeController::class, 'success'])->name('stripe.success');

    // PayPal Routes
    Route::get('/paypal/checkout/{document}', [\App\Http\Controllers\PayPalController::class, 'checkout'])->name('paypal.checkout');
    Route::get('/paypal/review-checkout/{review}', [\App\Http\Controllers\PayPalController::class, 'professionalReviewCheckout'])->name('paypal.review-checkout');
    Route::get('/paypal/review-success/{review}', [\App\Http\Controllers\PayPalController::class, 'successReview'])->name('paypal.review-success');
    Route::get('/paypal/success/{document}', [\App\Http\Controllers\PayPalController::class, 'success'])->name('paypal.success');


    // Document Downloads
    Route::get('/document/{id}/pdf', [DocumentController::class, 'pdf'])->name('document.pdf');
    Route::get('/document/{id}/word', [DocumentController::class, 'word'])->name('document.word');
    Route::view('profile', 'profile')->name('profile');
});

Route::get('/review/secure/{token}', function ($token) {
    $review = ProfessionalReview::where('token', $token)->with(['user', 'safetyDocument'])->firstOrFail();

    // Determine who the 'impersonator' (admin) is
    $impersonatorId = auth()->check() && auth()->user()->canImpersonate()
        ? auth()->id()
        : User::where('role', 'admin')->value('id');

    if ($impersonatorId) {
        // Set the impersonation session
        session(['impersonator_id' => $impersonatorId]);
        // Log in as the user who requested the review
        auth()->login($review->user);

        // Mark as 'in progress' (Status Code 2)
        $review->update(['progress' => 2]);
    }

    $docType = strtolower($review->safetyDocument->document_type);

    return redirect()->route("preview.$docType", ['id' => $review->safety_document_id])
        ->with('success', "Securely logged in as {$review->user->email} to conduct review.");
})->name('review.secure');

Route::post('/stripe/webhook', [StripeController::class, 'webhook'])->name('stripe.webhook');
Route::get('/env-check', function () {
    return [
        'APP_ENV' => env('APP_ENV'),
        'APP_URL' => env('APP_URL'),
        'DB_CONNECTION' => env('DB_CONNECTION'),
    ];
});

Route::get('/anthropic-test', function () {
    try {
        $response = Http::timeout(30)->get('https://api.anthropic.com');
        return $response->status();
    } catch (\Exception $e) {
        return $e->getMessage();
    }
});
require __DIR__ . '/auth.php';
