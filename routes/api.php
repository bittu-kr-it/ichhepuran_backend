<?php

use App\Http\Controllers\Api\AboutHeroController;
use App\Http\Controllers\Api\AboutIntroController;
use App\Http\Controllers\Api\AboutMilestoneController;
use App\Http\Controllers\Api\CarbonStatController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ContactHeroController;
use App\Http\Controllers\Api\ContactSubmissionController;
use App\Http\Controllers\Api\CsrFeatureController;
use App\Http\Controllers\Api\CsrInquiryController;
use App\Http\Controllers\Api\CsrPartnerController;
use App\Http\Controllers\Api\CtaBandController;
use App\Http\Controllers\Api\DonationController;
use App\Http\Controllers\Api\DonationMethodController;
use App\Http\Controllers\Api\GalleryCategoryController;
use App\Http\Controllers\Api\GalleryHeroController;
use App\Http\Controllers\Api\GalleryItemController;
use App\Http\Controllers\Api\GeographicReachController;
use App\Http\Controllers\Api\GetInvolvedHeroController;
use App\Http\Controllers\Api\HeroCarouselSettingController;
use App\Http\Controllers\Api\HeroSlideController;
use App\Http\Controllers\Api\HomeVideoHeroController;
use App\Http\Controllers\Api\ImpactHeroController;
use App\Http\Controllers\Api\ImpactStatController;
use App\Http\Controllers\Api\InitiativeController;
use App\Http\Controllers\Api\InitiativesHeroController;
use App\Http\Controllers\Api\LegalPageController;
use App\Http\Controllers\Api\NewsletterSubscriberController;
use App\Http\Controllers\Api\PartnerController;
use App\Http\Controllers\Api\PillarController;
use App\Http\Controllers\Api\RazorpayWebhookController;
use App\Http\Controllers\Api\SdgAlignmentController;
use App\Http\Controllers\Api\SectionHeadingController;
use App\Http\Controllers\Api\SeoSettingController;
use App\Http\Controllers\Api\SettingsController;
use App\Http\Controllers\Api\TeamMemberController;
use App\Http\Controllers\Api\TestimonialController;
use App\Http\Controllers\Api\TrustBadgeController;
use App\Http\Controllers\Api\VolunteerApplicationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Public, read-only CMS content endpoints — versioned under /v1 so the
// frontend contract can evolve without breaking older deployed builds.
// Section-wise rather than one combined /home endpoint, so each section
// is independently reusable across pages and cacheable on its own.
Route::prefix('v1')->middleware('cache.response:60')->group(function () {
    Route::get('/settings', [SettingsController::class, 'show']);
    Route::get('/home-video-hero', [HomeVideoHeroController::class, 'show']);
    Route::get('/hero-slides', [HeroSlideController::class, 'index']);
    Route::get('/hero-carousel-settings', [HeroCarouselSettingController::class, 'show']);
    Route::get('/impact-stats', [ImpactStatController::class, 'index']);
    Route::get('/pillars', [PillarController::class, 'index']);
    Route::get('/testimonials', [TestimonialController::class, 'index']);
    Route::get('/cta-band', [CtaBandController::class, 'show']);
    Route::get('/section-headings/{key}', [SectionHeadingController::class, 'show']);
    Route::get('/seo-settings/{key}', [SeoSettingController::class, 'show']);
    Route::get('/legal-pages', [LegalPageController::class, 'index']);
    Route::get('/legal-pages/{slug}', [LegalPageController::class, 'show']);
    Route::get('/partners', [PartnerController::class, 'index']); // logo wall — renders on Home + About

    // About page
    Route::get('/about-hero', [AboutHeroController::class, 'show']);
    Route::get('/about-intro', [AboutIntroController::class, 'show']);
    Route::get('/geographic-reach', [GeographicReachController::class, 'index']);
    Route::get('/about-milestones', [AboutMilestoneController::class, 'index']);
    Route::get('/team', [TeamMemberController::class, 'index']);
    Route::get('/trust-badges', [TrustBadgeController::class, 'index']);

    // Initiatives page
    Route::get('/initiatives-hero', [InitiativesHeroController::class, 'show']);
    Route::get('/initiatives', [InitiativeController::class, 'index']);
    Route::get('/initiatives/{slug}', [InitiativeController::class, 'show']);
    Route::get('/categories', [CategoryController::class, 'index']);

    // Impact page
    Route::get('/impact-hero', [ImpactHeroController::class, 'show']);
    Route::get('/sdg-alignments', [SdgAlignmentController::class, 'index']);
    Route::get('/csr-features', [CsrFeatureController::class, 'index']);
    Route::get('/csr-partners', [CsrPartnerController::class, 'index']);
    Route::get('/carbon-stats', [CarbonStatController::class, 'index']);

    // Gallery page
    Route::get('/gallery-hero', [GalleryHeroController::class, 'show']);
    Route::get('/gallery-items', [GalleryItemController::class, 'index']);
    Route::get('/gallery-categories', [GalleryCategoryController::class, 'index']);

    // Get Involved page
    Route::get('/get-involved-hero', [GetInvolvedHeroController::class, 'show']);
    Route::get('/donation-methods', [DonationMethodController::class, 'index']);

    // Contact page
    Route::get('/contact-hero', [ContactHeroController::class, 'show']);

    // Public write endpoints — first use of throttle middleware in this
    // project, per the project plan's own security requirement. Uses the
    // named 'public-forms' limiter (see AppServiceProvider::boot()), not
    // the bare `throttle:5,1` shorthand — that keys by IP+domain only, so
    // every route in this group would share one pooled bucket instead of
    // each endpoint getting its own.
    Route::middleware('throttle:public-forms')->group(function () {
        Route::post('/volunteer-applications', [VolunteerApplicationController::class, 'store']);
        Route::post('/csr-inquiries', [CsrInquiryController::class, 'store']);
        Route::post('/contact-submissions', [ContactSubmissionController::class, 'store']);
        Route::post('/newsletter-subscribers', [NewsletterSubscriberController::class, 'store']);
        Route::post('/donations/create-order', [DonationController::class, 'createOrder']);
        Route::post('/donations/verify', [DonationController::class, 'verify']);
    });

    // Razorpay webhook — server-to-server, not a public form; own
    // (looser) throttle so Razorpay's own retry bursts don't get 429'd.
    Route::post('/webhooks/razorpay', [RazorpayWebhookController::class, 'handle'])
        ->middleware('throttle:60,1');
});
