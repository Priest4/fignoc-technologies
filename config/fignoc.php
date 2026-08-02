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
];
