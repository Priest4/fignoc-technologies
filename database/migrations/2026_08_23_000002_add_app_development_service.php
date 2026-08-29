<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Add the app development service.
 *
 * We sell apps — the website landing page quotes "from $1,500" — but there was
 * no service page for them, so "mobile app developers Zimbabwe" and "app
 * development cost Zimbabwe" had nowhere on the site to land. See
 * docs/SEO-KEYWORDS.md.
 *
 * Slug is 'app-development' rather than 'mobile-apps' because that is how the
 * query is phrased.
 *
 * The seeder carries the same row for fresh installs; this carries it to any
 * database the deploy touches. Idempotent — keyed on the slug, safe to re-run.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (DB::table('services')->where('slug', 'app-development')->exists()) {
            return;
        }

        // Sits with the other Build services, immediately after custom software.
        $after = DB::table('services')->where('slug', 'custom-software')->value('sort_order');
        $sort = $after !== null ? $after + 1 : 99;

        // Everything below it shifts down one, so the catalogue order holds.
        DB::table('services')->where('sort_order', '>=', $sort)->increment('sort_order');

        DB::table('services')->insert([
            'name' => 'App development',
            'slug' => 'app-development',
            'tag' => 'Mobile',
            'category' => 'Build',
            'group_no' => '01',
            'is_featured' => false,
            'is_active' => true,
            'sort_order' => $sort,
            'description' => 'Android and iOS apps for Zimbabwean businesses — built once, running on both, with local payments wired in.',
            'detail' => json_encode([
                'what_it_is' => 'Mobile apps built from one codebase so Android and iOS stay in step, with EcoCash and Paynow payments, offline handling for patchy data, and push notifications.',
                'who_for' => 'Businesses whose customers already live on their phones — delivery, bookings, marketplaces, loyalty, field teams collecting data away from a desk.',
                'delivers' => [
                    'One codebase, Android and Play Store listing',
                    'EcoCash and Paynow payments in-app',
                    'Works on a weak connection, syncs when it returns',
                    'Push notifications and an admin dashboard',
                    'You own the code and the store listing',
                ],
                'why' => 'NiceJob is ours — a live marketplace app with two-way reviews and real-time chat. We built it, we run it, and we know what a Zimbabwean phone budget will actually tolerate.',
                'faqs' => [
                    [
                        'q' => 'How much does an app cost in Zimbabwe?',
                        'a' => 'Ours start at $1,500 and are quoted properly after a free scoping call. Around the market you will see $800 to $1,200 for something simple and far more for anything with payments or accounts. The honest answer is that it depends on what the app has to do, which is what the scoping call is for.',
                    ],
                    [
                        'q' => 'Do I need both Android and iOS?',
                        'a' => 'Most Zimbabwean businesses need Android first — it is the overwhelming majority of phones here. We build from one codebase, so iOS is there when you want it rather than a second project.',
                    ],
                    [
                        'q' => 'Can the app take EcoCash payments?',
                        'a' => 'Yes. Paynow covers EcoCash, OneMoney, InnBucks, ZimSwitch and cards through one integration, and it works in an app the same way it works on a website.',
                    ],
                    [
                        'q' => 'Would a website do instead?',
                        'a' => 'Often, yes — and we will say so. A fast mobile website costs a fraction of an app and needs nothing installed. An app earns its cost when people come back often, or when you need push notifications, offline use or the phone hardware.',
                    ],
                ],
                'related' => ['custom-software', 'web-systems', 'ecommerce'],
            ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        DB::table('services')->where('slug', 'app-development')->delete();
    }
};
