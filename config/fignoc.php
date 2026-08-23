<?php

/*
|--------------------------------------------------------------------------
| Fignoc brand + shared copy
|--------------------------------------------------------------------------
| Marketing CONTENT (services / products / work / team) lives in the database
| (see FignocSeeder) — the single source of truth. This file holds brand facts
| and the site-wide FAQ pool (brief §7.9).
*/

return [
    'brand' => [
        'name'     => 'Fignoc Technologies',
        'domain'   => 'www.fignoc.co.zw',
        'address'  => '811 Waterfall, Harare, Zimbabwe',
        'email'    => 'sales@fignoc.co.zw',
        'phone'    => '0786 280 414',
        // WhatsApp number for the primary wa.me CTA (international format, no +).
        'whatsapp' => '263786280414',
        // Organization schema logo (path under /public). Swap for a dedicated mark when ready.
        'logo_path' => 'images/og-default.jpg',
        // Social / profile URLs for Organization sameAs — add when profiles exist.
        'same_as' => [
            // 'https://www.linkedin.com/company/fignoc',
            // 'https://x.com/fignoc',
        ],
    ],

    // Site-wide FAQ pool (brief §7.9). Question-shaped for FAQPage schema.
    'faqs' => [
        [
            'q' => 'What does Fignoc Technologies do?',
            'a' => 'Fignoc is a Harare digital product studio and growth agency. We build our own software platforms — like Recruitment263 and NestZim — and we help other businesses get built and found online, including on AI answer engines.',
        ],
        [
            'q' => 'What is answer-engine optimisation (AEO), and why does it matter in Zimbabwe?',
            'a' => 'AEO makes your business the answer AI tools give when someone asks a question in your category. As more Zimbabweans ask ChatGPT, Gemini and Google AI Overviews directly, being the cited answer matters more than being link number four on page one.',
        ],
        [
            'q' => 'How is AEO different from SEO — and what is GEO?',
            'a' => 'SEO ranks your pages in the classic list of links. AEO gets you named in the direct AI answer. GEO (Generative Engine Optimisation) shapes how generative models describe and cite you. They stack — Fignoc does all three.',
        ],
        [
            'q' => 'Does Fignoc build ecommerce stores?',
            'a' => 'Yes. We build online stores tuned for the Zimbabwean market — Paynow, EcoCash and USD checkout, mobile-first, and engineered to be found in search.',
        ],
        [
            'q' => 'Does Fignoc run Google Ads and social media ads in Zimbabwe?',
            'a' => 'Yes — we manage both Google Ads and paid social, targeted to Zimbabwean audiences and budgets, with conversion tracking so spend is accountable.',
        ],
        [
            'q' => 'What products has Fignoc built?',
            'a' => 'Recruitment263 and NestZim are live. CV263, Shop263 and NiceJob are in active development. WLSA Zimbabwe is a live client platform.',
        ],
        [
            'q' => 'Does Fignoc work with NGOs?',
            'a' => 'Yes. We built and maintain the website and content platform for WLSA Zimbabwe (Women and Law in Southern Africa), covering programmes, legal-aid information, research and donations.',
        ],
        [
            'q' => 'How do I start a project with Fignoc?',
            'a' => 'Start a project from the contact page — message us on WhatsApp or email and we reply within one business day.',
        ],
    ],

    // ── Analytics ───────────────────────────────────────────────────────────
    // Leave null until launch credentials exist. Fake IDs must never ship.
    // When ready: ga4 => 'G-XXXXXXXX', search_console_verification => meta token.
    'analytics' => [
        'ga4' => null,
        'search_console_verification' => null,
        // Microsoft Clarity project ID. The landing page sells session replay
        // and heatmaps; not running them on our own page is indefensible.
        'clarity' => null,
    ],

    // ── Testimonials (PLACEHOLDER) ──────────────────────────────────────────
    // Fabricated stand-ins so the section can be built — DO NOT publish as real
    // client quotes. Swap each for a genuine, attributable testimonial.
    'testimonials' => [
        [
            'quote'   => 'Fignoc rebuilt our site and had it live in under a month. It loads fast on any phone, and we get enquiries through it every week now.',
            'name'    => 'Tendai Moyo',
            'role'    => 'Managing Director',
            'company' => 'Placeholder Co.',
        ],
        [
            'quote'   => 'They did not just build the system — they made sure people could find us on Google. Our booking requests have roughly doubled.',
            'name'    => 'Rachel Ncube',
            'role'    => 'Operations Lead',
            'company' => 'Placeholder Ltd.',
        ],
        [
            'quote'   => 'Clear scope, honest timelines, and we own all the code. The best development experience we have had with a local team.',
            'name'    => 'Farai Chikwava',
            'role'    => 'Founder',
            'company' => 'Placeholder Studio',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Website landing page (/website-design) — copy v6
    |--------------------------------------------------------------------------
    | Standalone, navigation-free landing page for the website service, built to
    | the Starlink structure: short headings, one idea per block, price in the
    | hero. Everything a human must VERIFY before the page goes live lives here
    | rather than buried in the template — the proof figures, the client results
    | strip and the hosting cost.
    */
    'landing_website' => [

        // ── Hero ────────────────────────────────────────────────────────────
        'headline' => 'Fast, custom-built websites for your business.',
        'subhead'  => 'No full payment upfront. Ever.',
        'price_label' => 'Websites from',
        'price_unit'  => 'once-off',
        // "Harare" deliberately not in this line — the address carries the
        // local proof further down, without narrowing who the page speaks to.
        'trust' => [
            'You own the code',
            'Coded from scratch, not templates',
        ],

        // ── Proof (block 8) ─────────────────────────────────────────────────
        // Recruitment263 Search Console figures — our own platform, not a
        // client's. Re-pull and update these; never let them drift stale.
        'proof' => [
            'window'  => 'the last 28 days',
            'source'  => 'Google Search Console',
            'metrics' => [
                ['value' => '12,500', 'label' => 'Google impressions a month'],
                ['value' => '#6',     'label' => 'Average position'],
                ['value' => '7.6%',   'label' => 'Click-through rate'],
                ['value' => 'Cited',  'label' => 'By name in Google AI Overviews'],
            ],
        ],

        // ── Client reviews ──────────────────────────────────────────────────
        // Real, client-supplied quotes. Adding 'is_sample' => true to an entry
        // stamps "Sample" on its card, for when a slot is being laid out with
        // stand-in copy — a fabricated review must never ship unmarked.
        //
        // Businesses are described rather than named at the clients' request;
        // if any of them later agrees to be named, put it in 'business'.
        //
        // Ask the client three questions and quote the answers verbatim:
        //   1. What was the problem before we built it?
        //   2. What changed after launch — a number if they have one?
        //   3. Would you say that on the record, with your name?
        //
        // Get written permission to publish the name and business.
        //
        // Reviews are NOT emitted as schema: Google treats self-serving reviews
        // hosted on your own site as ineligible for rich results, so marking
        // them up gains nothing and risks a manual action.
        'reviews' => [
            [
                'sector'   => 'NGO',
                'quote'    => 'The old site was WordPress and every year something else wanted paying for — the gallery plugin, the forms plugin, the one that was supposed to make it faster. And it still took forever to open. Now we have a proper blog section, we put up photos from every workshop ourselves, and it loads. No plugin invoices at all this year.',
                'name'     => 'Tendai Kandeya',
                'role'     => 'Communications Officer',
                'business' => 'local NGO',
            ],
            [
                'sector'   => 'Online store',
                'quote'    => 'Everything was in one WhatsApp thread. I’d scroll back trying to work out who had actually paid. Paynow and EcoCash went live in week two — now orders come in overnight and I read them in the morning.',
                'name'     => 'Rachel Ncube',
                'role'     => 'Shop owner',
                'business' => '',
            ],
            [
                'sector'   => 'Services',
                'quote'    => 'Search Console showed people searching for something we do but never advertised. We added one page. Two enquiries a month became eleven.',
                'name'     => 'Farai Chikwava',
                'role'     => 'Marketing Officer',
                'business' => 'local service business',
            ],
        ],

        // ── Built and running (block 9) ─────────────────────────────────────
        // 'image' is a path under /public. When the file is missing the card
        // renders a labelled pending slot instead of a broken image, so dropping
        // the screenshot in is the only step needed to finish the rail.
        'showcase' => [
            [
                'name'  => 'Recruitment263',
                'url'   => 'https://recruitment263.co.zw/',
                'image' => 'images/live/recruitment263.jpg',
                'desc'  => 'Zimbabwe job board.',
            ],
            [
                'name'  => 'CV263',
                'url'   => 'https://www.cv263.co.zw/',
                'image' => 'images/live/cv263.jpg',
                'desc'  => 'CV builder for the Zimbabwean job market.',
            ],
            [
                'name'  => 'NestZim',
                'url'   => 'https://www.nestzim.co.zw/',
                'image' => 'images/live/nestzim.jpg',
                'desc'  => 'Property listings and enquiry routing.',
            ],
            [
                'name'  => 'Shop263',
                'url'   => 'https://www.shop263.co.zw/',
                'image' => 'images/live/shop263.jpg',
                'desc'  => 'Online store with Paynow, EcoCash and USD.',
            ],
            [
                'name'  => 'NiceJob',
                'url'   => 'https://www.nicejob.co.zw/',
                'image' => 'images/live/nicejob.jpg',
                'desc'  => 'Trades and services marketplace.',
            ],
            [
                'name'  => 'Fignoc Online',
                'url'   => 'https://fignoconline.co.zw/',
                'image' => 'images/live/fignoconline.jpg',
                'desc'  => 'Our own storefront.',
            ],
        ],

        // ── Launch offer ────────────────────────────────────────────────────
        // 'list_price' on each package below is the price we charge once this
        // offer ends. It MUST be the real standard price — a struck-through
        // figure we never actually charge is a misleading price claim under
        // consumer law and Google Ads policy alike.
        //
        // 'ends_at' is a FIXED date, on purpose. A deadline that quietly resets
        // to "60 days from today" on every page load is the dishonest kind of
        // urgency, and the kind that stops working the moment a buyer notices.
        // When this date passes the offer banner disappears and the list prices
        // become the prices — so either extend it deliberately or let it lapse.
        'offer' => [
            'label'   => 'Launch offer',
            'ends_at' => '2026-10-22',
            'note'    => 'Our first sixty days. After that these go back to standard pricing.',
        ],

        // ── Pricing (block 2) ───────────────────────────────────────────────
        // Bullet count climbs with price on purpose: the visual weight does the
        // selling before the words do. Google Business Profile and the three
        // measurement tools are in EVERY tier.
        // Feeds both the pricing cards and the Service/OfferCatalog JSON-LD, so
        // this is the single source of truth for what a package costs.
        'packages' => [
            [
                'name'     => 'Starter',
                'slug'     => 'starter',
                'price'    => 80,
                'list_price' => 150,
                'prefix'   => 'from',
                'unit'     => 'once-off',
                'tagline'  => 'Get your business online, properly.',
                'featured' => false,
                'badge'    => null,
                'features' => [
                    ['text' => '5 pages'],
                    ['text' => 'Custom CMS — you update it'],
                    ['text' => 'Search Console, Analytics, Clarity', 'strong' => true],
                    ['text' => 'Google Business Profile optimised'],
                ],
                'note'     => null,
            ],
            [
                'name'     => 'Business',
                'slug'     => 'business',
                'price'    => 150,
                'list_price' => 300,
                'prefix'   => 'from',
                'unit'     => 'once-off',
                'tagline'  => 'Be found by people who don’t know your name yet.',
                'featured' => true,
                'badge'    => 'Most chosen',
                'features' => [
                    ['text' => '12 pages'],
                    ['text' => 'Blog you control'],
                    ['text' => 'Custom CMS — you update it'],
                    ['text' => 'Search Console, Analytics, Clarity'],
                    ['text' => 'Full AEO setup — named in AI answers', 'strong' => true],
                    ['text' => 'Google Business Profile optimised'],
                    ['text' => '30 days support'],
                ],
                'note'     => null,
            ],
            [
                'name'     => 'Growth',
                'slug'     => 'growth',
                'price'    => 320,
                'list_price' => 500,
                'prefix'   => 'from',
                'unit'     => 'once-off',
                'tagline'  => 'Sell online. Take bookings. Get paid.',
                'featured' => false,
                'badge'    => null,
                'features' => [
                    ['text' => '25 pages, or a full online store'],
                    ['text' => 'Online store or booking system'],
                    ['text' => 'Paynow and EcoCash payments', 'strong' => true],
                    ['text' => 'Blog you control'],
                    ['text' => 'Custom CMS — you update it'],
                    ['text' => 'Search Console, Analytics, Clarity'],
                    ['text' => 'Full AEO setup — named in AI answers'],
                    ['text' => 'Google Business Profile optimised'],
                    ['text' => '60 days support'],
                ],
                'note'     => null,
            ],
        ],

        // Domain + hosting renewal, paid by the client directly to the host.
        // null until the real Webzim figure is confirmed — the note drops the
        // number rather than publishing a guess.
        'hosting_cost' => null, // e.g. 45  (US$ per year)

        // ── Guarantee (block 10) ────────────────────────────────────────────
        // Terms: free consultation, 20% deposit, balance on approval. The
        // hero subhead, the FAQ answer and the section heading all restate
        // these — change them together or the page argues with itself.
        'guarantees' => [
            [
                'title' => 'Free consultation and advice.',
                'body'  => 'We look at what you have, tell you what you actually need, and quote it. No charge, no obligation — even if the answer is that you don’t need us yet.',
            ],
            [
                'title' => '20% deposit to start.',
                'body'  => 'The balance only when you approve the finished site. No full payment upfront, ever.',
            ],
            [
                'title' => 'Deposit back if the design isn’t right.',
                'body'  => 'We show you the design before we build it. If it isn’t right, we redo it. If the second version still isn’t right, take your deposit and we part as friends.',
            ],
            [
                'title' => 'Late is free.',
                'body'  => 'If your site isn’t live by the date we promised, you don’t pay the balance.',
            ],
            [
                'title' => 'Fourteen days after launch, anything broken is fixed free.',
                'body'  => 'Not “support.” Fixed.',
            ],
        ],

        // ── Coverage (block 11) ─────────────────────────────────────────────
        'cities' => ['Harare', 'Bulawayo', 'Mutare', 'Gweru', 'Masvingo', 'Victoria Falls'],

        // ── Insight Plan (block 12) ─────────────────────────────────────────
        // Pitched at handover, not during the sale — the section stays on the
        // page for the buyer who reads everything.
        'insight_plan' => [
            'price' => 45,
            'items' => [
                'Monthly report in plain English — who visited, what they searched, where they left',
                'We name the page costing you the most enquiries',
                'One conversion fix a month to whatever page is leaking',
                'Content updates — prices, services, photos, staff',
                'Small layout changes on request',
                'Security updates and uptime monitoring',
                'Hosting renewal reminders',
            ],
            'terms' => 'Cancel any time, 30 days’ notice.',
            'close' => 'One extra customer a month covers it twice over.',
        ],

        // ── Straight answers (block 14) ─────────────────────────────────────
        // Seven, not seventeen. A landing page FAQ earns its place by removing
        // a reason not to buy; anything the page already answers in a section
        // of its own was cut. The full pool lives in config('fignoc.faqs') and
        // on the main site.
        //
        // Deliberately dropped here: cost-in-Zimbabwe comparison (the pricing
        // block covers it), .co.zw domains, Paynow/EcoCash (a Growth bullet),
        // what AEO is (it has its own chapter), who writes the content,
        // instalments, "what if I already have a website" (the closing block is
        // exactly that offer), and whether the Insight Plan is optional (its
        // own section says so).
        'faqs' => [
            [
                'q' => 'Why is this more than the $50 websites I’ve seen?',
                'a' => 'Those are templates with your logo dropped in. Ours are coded from scratch, load fast on mobile data, and are built so Google and AI engines can read them. A $50 site that brings no enquiries didn’t cost you $50 — it cost you a year of being invisible.',
            ],
            [
                'q' => 'What if you take my deposit and disappear?',
                'a' => 'Fair question in this market. The deposit is 20%, the balance is only due once you approve the finished site, and the deposit comes back if the design isn’t right. Our own platforms are live at recruitment263.co.zw, cv263.co.zw and nestzim.co.zw — click any of them. We’re not hard to find.',
            ],
            [
                'q' => 'Do I own my website?',
                'a' => 'Completely. Code, domain and hosting account, all in your name. You pay the host directly and we put the renewal date in writing, so you are never locked to us.',
            ],
            [
                'q' => 'Can I update it myself?',
                'a' => 'Yes. That’s what the custom CMS is for, and we record a walkthrough at handover. If you’d rather we handled it, that’s the Insight Plan.',
            ],
            [
                'q' => 'How long does it take?',
                'a' => 'Starter, 7–10 working days once you’ve sent your content. Business, about two weeks. Growth, two to four.',
            ],
            [
                'q' => 'Why not WordPress, Wix or Squarespace?',
                'a' => 'You can. WordPress works but ends up loaded with plugins that slow it down and charge licences every year. Wix and Squarespace are slower, generic and harder to rank — and you never own the result: stop paying and the site disappears. We build on Laravel or Django, and the code is yours.',
            ],
            [
                'q' => 'Isn’t WhatsApp Business enough?',
                'a' => 'WhatsApp is great for customers who already found you. It can’t rank on Google, it can’t be cited by ChatGPT, and it can’t sell to someone at 11pm.',
            ],
        ],
    ],
];
