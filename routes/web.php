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

Route::get('/contact', [SiteController::class, 'contact'])->name('contact');
Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');

Route::get('/insights', [SiteController::class, 'insightsIndex'])->name('insights');
Route::get('/insights/{insight:slug}', [SiteController::class, 'insightShow'])->name('insights.show');

// Standalone landing page for the website service — no navigation, one offer,
// one decision. The destination for paid traffic.
Route::get('/website-design', [SiteController::class, 'websiteLanding'])->name('landing.website');
Route::post('/website-design/enquiry', [ContactController::class, 'websiteEnquiry'])
    ->name('landing.website.enquiry');

Route::get('/privacy', [SiteController::class, 'privacy'])->name('privacy');
Route::get('/terms', [SiteController::class, 'terms'])->name('terms');

Route::get('/sitemap.xml', [SiteController::class, 'sitemap'])->name('sitemap');
