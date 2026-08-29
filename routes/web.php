<?php

use App\Http\Controllers\ContactController;
use App\Http\Controllers\SiteController;
use Illuminate\Support\Facades\Route;

Route::get('/', [SiteController::class, 'home'])->name('home');

Route::get('/services', [SiteController::class, 'servicesIndex'])->name('services');
Route::get('/services/{service:slug}', [SiteController::class, 'serviceShow'])->name('services.show');

Route::get('/work', [SiteController::class, 'workIndex'])->name('work');
Route::get('/work/{work:slug}', [SiteController::class, 'workShow'])->name('work.show');

Route::get('/products', [SiteController::class, 'productsIndex'])->name('products');
Route::get('/products/{product:slug}', [SiteController::class, 'productShow'])->name('products.show');

Route::get('/about', [SiteController::class, 'about'])->name('about');

// One page covering all three lines of business. /website-design publishes
// website prices but is a navigation-free paid-traffic page, so it cannot do
// this job organically. See docs/SEO-KEYWORDS.md.
Route::get('/pricing', [SiteController::class, 'pricing'])->name('pricing');

Route::get('/contact', [SiteController::class, 'contact'])->name('contact');
Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');

Route::get('/insights', [SiteController::class, 'insightsIndex'])->name('insights');
Route::get('/insights/{insight:slug}', [SiteController::class, 'insightShow'])->name('insights.show');

// Standalone landing page for the website service — no navigation, one offer,
// one decision. The destination for paid traffic.
Route::get('/website-design', [SiteController::class, 'websiteLanding'])->name('landing.website');
Route::post('/website-design/enquiry', [ContactController::class, 'websiteEnquiry'])
    ->name('landing.website.enquiry');

// Legacy URLs Google still has indexed. /shop was a WordPress storefront that
// no longer exists; a 301 hands its link equity to the products index rather
// than serving 404s until it drops out of the index.
Route::redirect('/shop', '/products', 301);
Route::redirect('/shop/{any}', '/products', 301)->where('any', '.*');

Route::get('/privacy', [SiteController::class, 'privacy'])->name('privacy');
Route::get('/terms', [SiteController::class, 'terms'])->name('terms');

Route::get('/sitemap.xml', [SiteController::class, 'sitemap'])->name('sitemap');
