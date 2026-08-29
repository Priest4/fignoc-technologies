<?php

namespace Database\Seeders;

use App\Models\PortfolioItem;
use App\Models\Product;
use App\Models\Service;
use App\Models\TeamMember;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

/**
 * Single source of truth for all marketing content (brief §7).
 * Re-runnable: truncates then re-inserts. The retired sub-brand is not referenced.
 */
class FignocSeeder extends Seeder
{
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        Service::truncate();
        Product::truncate();
        PortfolioItem::truncate();
        TeamMember::truncate();
        Schema::enableForeignKeyConstraints();

        $this->seedServices();
        $this->seedProducts();
        $this->seedWork();
        $this->seedTeam();

        $this->applyCopyOverrides();
    }

    /** Overlay polished CRO copy from database/data/*.php over the seeded rows. */
    private function applyCopyOverrides(): void
    {
        $svcPath = database_path('data/service_copy.php');
        if (is_file($svcPath)) {
            foreach (require $svcPath as $slug => $c) {
                if ($s = Service::where('slug', $slug)->first()) {
                    $s->description = $c['description'];
                    $s->detail = $c['detail'];
                    $s->save();
                }
            }
        }
        $prodPath = database_path('data/product_copy.php');
        if (is_file($prodPath)) {
            foreach (require $prodPath as $slug => $c) {
                if ($p = Product::where('slug', $slug)->first()) {
                    $p->headline = $c['headline'];
                    $p->description = $c['description'];
                    $p->features = $c['features'];
                    $p->who_for = $c['who_for'];
                    $p->detail = ['long' => $c['long']];
                    $p->save();
                }
            }
        }
    }

    /** 8 services grouped Build / Rank / Grow (brief §7.2). AEO + GEO featured. */
    private function seedServices(): void
    {
        $services = [
            [
                'name' => 'Website development & design',
                'slug' => 'web-development',
                'tag' => 'Websites',
                'category' => 'Build',
                'group_no' => '01',
                'is_featured' => false,
                'description' => 'Fast, crawlable, conversion-focused websites — engineered to load quickly on Zimbabwean mobile data and rank from day one.',
                'detail' => [
                    'what_it_is' => 'Custom websites and landing pages built on modern, lightweight stacks — no bloated page-builders. Semantic HTML, real performance budgets, and SEO baked in from the first line of code.',
                    'who_for' => 'Businesses replacing a slow WordPress site, startups launching a first credible web presence, and organisations that need a site fast enough to actually rank and convert.',
                    'delivers' => [
                        'Design and build tuned to your brand and audience',
                        'Core Web Vitals performance budget (LCP < 2.5s on 3G)',
                        'Right-sized responsive images (WebP/AVIF) for costly mobile data',
                        'Technical SEO and structured data from launch',
                        'Accessible, keyboard-navigable, semantic markup',
                    ],
                    'why' => 'This very site is the proof — it is engineered to beat a typical page-builder build on Core Web Vitals and crawlability. We build the same way for you.',
                    'faqs' => [
                        ['q' => 'Does Fignoc build ecommerce stores?', 'a' => 'Yes — we build both marketing sites and full online stores tuned for the Zimbabwean market, with local payment and delivery handling. See our <a class="link-accent" href="/services/ecommerce/">ecommerce service</a>.'],
                        ['q' => 'How fast can a site go live?', 'a' => 'A focused marketing site can launch in a few weeks. We scope timelines honestly during a first conversation.'],
                    ],
                    'related' => ['web-systems', 'custom-software'],
                ],
            ],
            [
                'name' => 'Web systems & applications',
                'slug' => 'web-systems',
                'tag' => 'Web apps',
                'category' => 'Build',
                'group_no' => '01',
                'is_featured' => false,
                'description' => 'Custom web applications and internal systems — portals, dashboards, booking and management tools built around how your organisation actually works.',
                'detail' => [
                    'what_it_is' => 'Bespoke web-based systems that go well beyond a brochure site: customer portals, admin dashboards, booking and membership platforms, internal tools and workflow apps — engineered on solid, maintainable stacks.',
                    'who_for' => 'Organisations outgrowing spreadsheets and manual processes, and businesses that need software shaped to their workflow instead of an off-the-shelf compromise.',
                    'delivers' => [
                        'Requirements and workflow mapping',
                        'Custom web application design and build',
                        'Secure authentication, roles and permissions',
                        'Dashboards, reporting and third-party integrations',
                        'Deployment, maintenance and support',
                    ],
                    'why' => 'We run our own production platforms — Recruitment263 and NestZim — so we build client systems with the same engineering discipline, not throwaway prototypes.',
                    'faqs' => [
                        ['q' => 'What kind of web systems do you build?', 'a' => 'Portals, dashboards, booking and membership systems, and internal admin tools — anything your workflow needs that off-the-shelf software cannot do.'],
                        ['q' => 'Can you integrate with tools we already use?', 'a' => 'Yes — we build APIs and integrations so the system connects to payments, messaging and your existing tools.'],
                    ],
                    'related' => ['custom-software', 'web-development'],
                ],
            ],
            [
                'name' => 'App development',
                'slug' => 'app-development',
                'tag' => 'Mobile',
                'category' => 'Build',
                'group_no' => '01',
                'is_featured' => false,
                'description' => 'Android and iOS apps for Zimbabwean businesses — built once, running on both, with local payments wired in.',
                'detail' => [
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
                        ['q' => 'How much does an app cost in Zimbabwe?', 'a' => 'Ours start at $1,500 and are quoted properly after a free scoping call. Around the market you will see $800 to $1,200 for something simple and far more for anything with payments or accounts. The honest answer is that it depends on what the app has to do, which is what the scoping call is for.'],
                        ['q' => 'Do I need both Android and iOS?', 'a' => 'Most Zimbabwean businesses need Android first — it is the overwhelming majority of phones here. We build from one codebase, so iOS is there when you want it rather than a second project.'],
                        ['q' => 'Can the app take EcoCash payments?', 'a' => 'Yes. Paynow covers EcoCash, OneMoney, InnBucks, ZimSwitch and cards through one integration, and it works in an app the same way it works on a website.'],
                        ['q' => 'Would a website do instead?', 'a' => 'Often, yes — and we will say so. A fast mobile website costs a fraction of an app and needs nothing installed. An app earns its cost when people come back often, or when you need push notifications, offline use or the phone hardware.'],
                    ],
                    'related' => ['custom-software', 'web-systems', 'ecommerce'],
                ],
            ],
            [
                'name' => 'Custom software development',
                'slug' => 'custom-software',
                'tag' => 'Software',
                'category' => 'Build',
                'group_no' => '01',
                'is_featured' => false,
                'description' => 'Bespoke software engineered from scratch — SaaS platforms, APIs, POS and mobile apps, built for the Zimbabwean market.',
                'detail' => [
                    'what_it_is' => 'End-to-end custom software development: SaaS products, APIs and integrations, point-of-sale, and mobile apps — designed, built, deployed and maintained by the team behind Fignoc\'s own platforms.',
                    'who_for' => 'Businesses and startups with a software idea, or a process that needs a purpose-built product rather than a template.',
                    'delivers' => [
                        'Product discovery and technical scoping',
                        'Full-stack software design and engineering',
                        'APIs, integrations and local payments (Paynow/EcoCash)',
                        'Mobile apps (iOS/Android) where needed',
                        'Deployment, monitoring and ongoing support',
                    ],
                    'why' => 'We are a software company first — Fignoc builds and runs its own SaaS products, so bespoke builds get real production experience, not guesswork.',
                    'faqs' => [
                        ['q' => 'Do you build mobile apps too?', 'a' => 'Yes — we build mobile apps (including Flutter) alongside web platforms and APIs.'],
                        ['q' => 'Do we own the software you build?', 'a' => 'Yes. Bespoke software we build for you is yours, with a clean handover and optional ongoing support.'],
                    ],
                    'related' => ['web-systems', 'web-development'],
                ],
            ],
            [
                'name' => 'NGO information systems',
                'slug' => 'ngo-systems',
                'tag' => 'NGO sector',
                'category' => 'Build',
                'group_no' => '01',
                'is_featured' => false,
                'description' => 'Information-management systems built for NGOs — case and beneficiary records, M&E reporting, and donor-ready data, done securely.',
                'detail' => [
                    'what_it_is' => 'Custom information systems for the NGO and development sector: beneficiary and case management, programme and monitoring-and-evaluation (M&E) reporting, secure records, and the donor-ready data and dashboards funders expect — built around how your organisation and its reporting actually work.',
                    'who_for' => 'NGOs, CBOs and development organisations outgrowing spreadsheets, and teams that need secure, reportable data for programmes and donors.',
                    'delivers' => [
                        'Beneficiary, case and programme data models',
                        'Secure records with role-based access and permissions',
                        'Monitoring & evaluation (M&E) reporting and dashboards',
                        'Donor and grant reporting exports',
                        'A public website or content platform where needed',
                    ],
                    'why' => 'We already build and maintain the platform for WLSA Zimbabwe — so we understand NGO reporting, the sensitivity of the data, and working to donor requirements.',
                    'faqs' => [
                        ['q' => 'Does Fignoc work with NGOs?', 'a' => 'Yes — we built and maintain the website and content platform for WLSA Zimbabwe, and we develop information systems for the NGO sector.'],
                        ['q' => 'Is beneficiary data kept secure?', 'a' => 'Yes — systems are built with role-based access, secure storage and the confidentiality NGO data requires.'],
                    ],
                    'related' => ['custom-software', 'web-systems'],
                ],
            ],
            [
                'name' => 'Ecommerce stores',
                'slug' => 'ecommerce',
                'tag' => 'Commerce',
                'category' => 'Build',
                'group_no' => '01',
                'is_featured' => false,
                'description' => 'Online stores tuned for the Zimbabwean market — EcoCash and USD checkout, mobile-first, and built to be found.',
                'detail' => [
                    'what_it_is' => 'Storefronts and full ecommerce platforms with local payment methods, inventory, and delivery built in — plus the technical SEO that makes products discoverable in search and AI answers.',
                    'who_for' => 'Retailers moving online, businesses outgrowing a social-media-only shopfront, and brands that need a store customers can actually pay through locally.',
                    'delivers' => [
                        'Branded storefront and product catalogue',
                        'Paynow, EcoCash and USD-cash checkout flows',
                        'Inventory, orders and delivery-zone handling',
                        'Product structured data for search + shopping surfaces',
                        'Mobile-first performance for on-the-go shoppers',
                    ],
                    'why' => 'We run our own ecommerce SaaS (Shop263), so we build stores from hard-won experience with what actually converts in Zimbabwe.',
                    'faqs' => [
                        ['q' => 'Which payment methods can customers use?', 'a' => 'Paynow and EcoCash for local payments, plus USD-cash-on-delivery flows where that suits the business.'],
                        ['q' => 'Can you migrate my existing product list?', 'a' => 'Yes — we import existing catalogues and set up inventory during the build.'],
                    ],
                    'related' => ['web-development', 'customer-journey-optimisation'],
                ],
            ],
            [
                'name' => 'SEO',
                'slug' => 'seo',
                'tag' => 'Search',
                'category' => 'Rank',
                'group_no' => '02',
                'is_featured' => false,
                'description' => 'Technical + content SEO that earns durable Google rankings — the foundation everything else builds on.',
                'detail' => [
                    'what_it_is' => 'Search Engine Optimisation is the work of making Google understand, trust, and rank your pages: technical health, structured data, content that matches real search intent, and authority over time.',
                    'who_for' => 'Businesses that want steady, compounding traffic instead of renting every click through ads.',
                    'delivers' => [
                        'Technical audit and fixes (crawl, speed, indexing)',
                        'Keyword and intent research for your market',
                        'On-page optimisation and structured data',
                        'Content architecture and internal linking',
                        'Measurement via Search Console and GA4',
                    ],
                    'why' => 'SEO is the base layer. We extend it into AEO and GEO — the way Zimbabweans increasingly search — which almost no local agency does.',
                    'faqs' => [
                        ['q' => 'How is SEO different from AEO and GEO?', 'a' => 'SEO ranks your pages in the classic list of blue links. <a class="link-accent" href="/services/aeo/">AEO</a> gets you named in AI answer boxes; <a class="link-accent" href="/services/geo/">GEO</a> makes generative engines cite you. We do all three.'],
                        ['q' => 'How long until I see results?', 'a' => 'Technical wins can land quickly; content-driven ranking typically compounds over months. We set honest expectations up front.'],
                    ],
                    'related' => ['aeo', 'geo'],
                ],
            ],
            [
                'name' => 'AEO — Answer Engine Optimisation',
                'slug' => 'aeo',
                'tag' => 'Featured',
                'category' => 'Rank',
                'group_no' => '02',
                'is_featured' => true,
                'description' => "Get named in the answers AI gives. Zimbabwe's only agency built for answer-engine optimisation.",
                'detail' => [
                    'what_it_is' => "Answer Engine Optimisation is the practice of getting your business named directly in the answers AI assistants and search answer-boxes give — Google AI Overviews, ChatGPT, Perplexity, Gemini and Copilot. When a Zimbabwean asks an AI “who builds X in Harare?”, AEO is the work that makes the answer you.",
                    'who_for' => 'Any business that wants to be recommended, not merely listed — especially in categories where buyers now ask an AI before they ask Google.',
                    'delivers' => [
                        'Entity and knowledge-graph setup so engines know exactly who you are',
                        'Question-shaped content and FAQs that answer engines can lift verbatim',
                        'Structured data (FAQPage, Organization, Product) machines can parse',
                        'Topic clusters that make you the obvious source in your category',
                        'Monitoring of how AI engines describe and cite your business',
                    ],
                    'why' => "Fignoc is Zimbabwe's only agency specialising in AEO and GEO. We don't bolt it onto SEO as an afterthought — we build for the way people actually search now.",
                    'faqs' => [
                        ['q' => 'What is answer-engine optimisation (AEO), and why does it matter in Zimbabwe?', 'a' => 'AEO makes your business the answer AI tools give when someone asks a question in your category. As more Zimbabweans ask ChatGPT, Gemini and Google AI Overviews directly, being the cited answer matters more than being link #4 on page one.'],
                        ['q' => 'How is AEO different from SEO — and what is GEO?', 'a' => 'SEO ranks pages in the classic results list. AEO gets you into the direct answer. <a class="link-accent" href="/services/geo/">GEO</a> (Generative Engine Optimisation) makes generative models generate and cite content about you. They stack — we do all three.'],
                        ['q' => 'How do I start?', 'a' => '<a class="link-accent" href="/contact">Start a project</a> and we\'ll audit how AI engines currently describe your business.'],
                    ],
                    'related' => ['geo', 'seo'],
                ],
            ],
            [
                'name' => 'GEO — Generative Engine Optimisation',
                'slug' => 'geo',
                'tag' => 'Featured',
                'category' => 'Rank',
                'group_no' => '02',
                'is_featured' => true,
                'description' => 'Become the source generative AI cites and recommends when it writes about your market.',
                'detail' => [
                    'what_it_is' => 'Generative Engine Optimisation shapes how large language models represent your business when they generate answers, comparisons and recommendations. Where AEO targets the direct answer box, GEO targets the underlying model’s knowledge and citations.',
                    'who_for' => 'Brands that want to be the reference generative tools reach for — and to control how they are summarised when they are.',
                    'delivers' => [
                        'Authoritative, citable content designed for model ingestion',
                        'Consistent entity signals across the web and structured data',
                        'Comparison and “best of” content that positions you correctly',
                        'Presence on the third-party sources models trust',
                        'Tracking of how generative engines represent and cite you',
                    ],
                    'why' => "Together with AEO, this is Fignoc's core differentiator — and we are Zimbabwe's only AEO/GEO agency.",
                    'faqs' => [
                        ['q' => 'Is GEO just SEO with a new name?', 'a' => 'No. SEO optimises for ranked links; GEO optimises for how generative models understand and reproduce information about you, including which sources they cite.'],
                        ['q' => 'Can you influence what ChatGPT says about my business?', 'a' => 'We can strongly shape it — by strengthening the entity signals, structured data and trusted third-party sources these models draw on, then monitoring the output.'],
                    ],
                    'related' => ['aeo', 'seo'],
                ],
            ],
            [
                'name' => 'Content strategy & audit',
                'slug' => 'content-strategy',
                'tag' => 'Content',
                'category' => 'Rank',
                'group_no' => '02',
                'is_featured' => false,
                'description' => 'Plan what to publish and audit what you already have — so your content earns rankings, answers real questions, and converts.',
                'detail' => [
                    'what_it_is' => 'Content strategy defines what you publish, for whom, and why; a content audit reviews your existing pages to find what ranks, what is thin or missing, and what to fix. Together they turn content from guesswork into a system — and feed directly into SEO, AEO and GEO.',
                    'who_for' => 'Businesses producing content with no plan, sites with pages that do not rank, and teams who want content that answers the questions their customers — and AI engines — actually ask.',
                    'delivers' => [
                        'Content audit of your existing site (what works, what is thin, what is missing)',
                        'Keyword, question and intent research',
                        'A content plan and editorial calendar',
                        'Topic clusters that build topical authority',
                        'Briefs your team — or ours — can write to',
                    ],
                    'why' => 'Content is what search and answer engines actually read. Our content work is built to feed AEO/GEO from day one — not generic blogging.',
                    'faqs' => [
                        ['q' => 'What does a content audit cover?', 'a' => 'We review every important page for relevance, search performance, gaps and duplication, then give you a prioritised list of what to keep, improve, merge or remove.'],
                        ['q' => 'Do you write the content too?', 'a' => 'We can — or we provide the strategy, research and briefs and your team writes to them.'],
                    ],
                    'related' => ['seo', 'aeo'],
                ],
            ],
            [
                'name' => 'Google Ads',
                'slug' => 'google-ads',
                'tag' => 'Paid Media',
                'category' => 'Grow',
                'group_no' => '03',
                'is_featured' => false,
                'description' => 'Precision-targeted search and display campaigns that turn budget into measurable enquiries.',
                'detail' => [
                    'what_it_is' => 'Managed Google Ads — search, display and remarketing — structured around the keywords and intents that actually bring your buyers, with conversion tracking so every dollar is accountable.',
                    'who_for' => 'Businesses that need enquiries now, or want paid to complement the slower compounding of SEO.',
                    'delivers' => [
                        'Account structure, keywords and negative lists',
                        'Conversion tracking and GA4 integration',
                        'Ad copy and landing-page alignment',
                        'Budget pacing and bid management',
                        'Clear monthly reporting on cost-per-enquiry',
                    ],
                    'why' => 'We connect ads to a fast, well-built landing experience — so you are not paying for clicks that a slow page then loses.',
                    'faqs' => [
                        ['q' => 'Does Fignoc run Google Ads and social media ads in Zimbabwe?', 'a' => 'Yes — both. We manage Google Ads and paid social, tuned to Zimbabwean audiences and budgets.'],
                        ['q' => 'What budget do I need?', 'a' => 'We work to your budget and are honest about what it can realistically achieve before you commit.'],
                    ],
                    'related' => ['social-ads', 'customer-journey-optimisation'],
                ],
            ],
            [
                'name' => 'Social media ads',
                'slug' => 'social-ads',
                'tag' => 'Paid Media',
                'category' => 'Grow',
                'group_no' => '03',
                'is_featured' => false,
                'description' => 'Meta and platform advertising that reaches Zimbabwean audiences where they already scroll.',
                'detail' => [
                    'what_it_is' => 'Paid campaigns on Facebook, Instagram and other platforms — creative, targeting and measurement — designed to build awareness and drive action among local audiences.',
                    'who_for' => 'Consumer brands, events and businesses whose customers live on social, and who want reach that converts rather than vanity impressions.',
                    'delivers' => [
                        'Audience and creative strategy',
                        'Campaign build and optimisation',
                        'Pixel / conversion tracking setup',
                        'Retargeting flows',
                        'Reporting tied to real outcomes',
                    ],
                    'why' => 'We treat social ads as one step in the customer journey — not a silo — so spend connects to a coherent path to enquiry.',
                    'faqs' => [
                        ['q' => 'Which platforms do you run?', 'a' => 'Primarily Meta (Facebook + Instagram), with others where your audience actually is.'],
                        ['q' => 'Can you produce the creative too?', 'a' => 'Yes — we can handle creative and copy alongside targeting and measurement.'],
                    ],
                    'related' => ['google-ads', 'customer-journey-optimisation'],
                ],
            ],
            [
                'name' => 'Customer journey optimisation',
                'slug' => 'customer-journey-optimisation',
                'tag' => 'Growth',
                'category' => 'Grow',
                'group_no' => '03',
                'is_featured' => false,
                'description' => 'Map and fix the path from first click to customer — so more of the traffic you already have converts.',
                'detail' => [
                    'what_it_is' => 'A structured audit of the whole journey — awareness, consideration, conversion, retention — that finds where people drop off and fixes the highest-impact leaks first.',
                    'who_for' => 'Businesses getting traffic but not enough enquiries, and teams who want growth without simply spending more on ads.',
                    'delivers' => [
                        'Journey and funnel mapping',
                        'Analytics and drop-off analysis',
                        'Prioritised, ranked list of fixes',
                        'Landing-page and messaging improvements',
                        'Before/after measurement',
                    ],
                    'why' => 'We built a SaaS platform for journey audits — so this is a repeatable method, not guesswork.',
                    'faqs' => [
                        ['q' => 'Is this a one-off or ongoing?', 'a' => 'It can be either — a one-off audit with a prioritised action list, or an ongoing optimisation partnership.'],
                        ['q' => 'What do I get at the end?', 'a' => 'A clear, ranked set of findings and fixes tied to the metrics that matter to your business.'],
                    ],
                    'related' => ['google-ads', 'seo'],
                ],
            ],
        ];

        foreach ($services as $i => $s) {
            Service::create(array_merge($s, ['sort_order' => $i, 'is_active' => true]));
        }
    }

    /** 5 sellable platforms (brief §7.7). WLSA is Work-only, not a product. */
    private function seedProducts(): void
    {
        $products = [
            [
                'name' => 'CV263',
                'slug' => 'cv263',
                'tag' => 'CV & Résumé Builder',
                'status' => 'live',
                'description' => "Zimbabwe's own CV and résumé builder — guided, ATS-friendly, with AI writing help.",
                'headline' => 'Build a job-winning CV in minutes — made for Zimbabwe.',
                'features' => [
                    'Guided two-pane builder with live preview',
                    '15+ ATS-friendly templates',
                    'Real PDF and Word export',
                    'AI writing help',
                    'Tailor-to-a-job match & gap score',
                    'Honest billing, no auto-renew (Paynow/EcoCash)',
                ],
                'who_for' => ['Jobseekers who need a professional CV fast', 'Graduates entering the market', 'Anyone tailoring a CV to a specific job'],
                'work_slug' => 'cv263',
                'external_url' => 'https://cv263.co.zw',
                'screenshot_path' => 'images/live/cv263.jpg',
                'detail' => ['long' => 'CV263 walks you through a professional CV and cover letter in a guided, two-pane wizard with live preview, then exports a genuine ATS-friendly PDF or Word file. AI writing help drafts and sharpens your wording, and a tailor-to-a-job tool scores how well your CV matches a specific listing and shows the gaps to close.'],
                'sort_order' => 0,
            ],
            [
                'name' => 'Recruitment263',
                'slug' => 'recruitment263',
                'tag' => 'Recruitment Platform',
                'status' => 'live',
                'description' => 'A national job board spanning every Zimbabwean sector — free to post and browse, engineered for search.',
                'headline' => 'Zimbabwe’s jobs, tenders and consultancies — in one place.',
                'features' => [
                    'Jobs, consultancies, tenders & internships',
                    'Guest + registered employer posting',
                    'Admin approval & anti-scam moderation',
                    'Full technical SEO (JobPosting schema, sitemap, RSS)',
                    'Auto-expiry on deadline',
                    'Newsletter + blog',
                ],
                'who_for' => ['Employers across corporate, government, NGO, UN and donor sectors', 'Jobseekers browsing verified roles', 'Recruiters and consultancies'],
                'work_slug' => 'recruitment263',
                'external_url' => 'https://recruitment263.co.zw',
                'screenshot_path' => 'images/live/recruitment263.jpg',
                'detail' => ['long' => 'Recruitment263 is a national job board covering every Zimbabwean sector — corporate, government, NGO, UN, donors and consultancies. Posting and browsing are free, listings are moderated to keep scams out, and the whole platform is engineered for technical SEO with JobPosting structured data, a sitemap and RSS so roles are found the moment they go live.'],
                'sort_order' => 1,
            ],
            [
                'name' => 'Shop263',
                'slug' => 'shop263',
                'tag' => 'E-Commerce SaaS',
                'status' => 'live',
                'description' => 'Any Zimbabwean business can launch a branded online shop in minutes — with local checkout built in.',
                'headline' => 'Launch a real online shop in minutes.',
                'features' => [
                    'Branded storefront live fast',
                    'Inventory & catalogue',
                    'Paynow, EcoCash & USD-cash checkout',
                    'Mini POS Lite',
                    'Shared marketplace',
                    'Multi-shop, multi-currency',
                ],
                'who_for' => ['Retailers moving online', 'Social-media sellers wanting a real store', 'Businesses needing local checkout'],
                'work_slug' => 'shop263',
                'external_url' => 'https://shop263.co.zw',
                'screenshot_path' => 'images/live/shop263.jpg',
                'detail' => ['long' => 'Shop263 (OurShopsZim) is a multi-tenant ecommerce platform: any Zimbabwean business can spin up a branded storefront in minutes, manage inventory, take payment through Paynow, EcoCash or USD cash, run a lightweight POS, and appear in a shared marketplace — all multi-shop and multi-currency.'],
                'sort_order' => 2,
            ],
            [
                'name' => 'NestZim',
                'slug' => 'nestzim',
                'tag' => 'Property Rentals',
                'status' => 'live',
                'description' => 'A Zimbabwean rental marketplace with verified listings, map search and direct landlord messaging.',
                'headline' => 'Find your next place — verified, mapped, message direct.',
                'features' => [
                    'Verified listings',
                    'Map search & filters',
                    'Direct landlord/agent messaging',
                    'Student-accommodation filters',
                    'Manager dashboard & analytics',
                    'iOS/Android app with real-time alerts',
                ],
                'who_for' => ['Renters and students', 'Landlords and agents', 'Property managers'],
                'work_slug' => 'nestzim',
                'external_url' => 'https://nestzim.co.zw',
                'screenshot_path' => 'images/live/nestzim.jpg',
                'detail' => ['long' => 'NestZim is a rental marketplace for Zimbabwe: renters filter verified listings by location and budget, search on a map, and message landlords or agents directly. Dedicated student-accommodation filters, a manager dashboard, and a mobile app with real-time alerts round out the platform.'],
                'sort_order' => 3,
            ],
            [
                'name' => 'NiceJob',
                'slug' => 'nicejob',
                'tag' => 'Freelance Marketplace',
                'status' => 'live',
                'description' => 'A mobile-first skills marketplace and reputation platform — with a direct-deal model and no in-app cut.',
                'headline' => 'Hire skills. Build a reputation. Keep your money.',
                'features' => [
                    'Freelancer profiles & portfolios',
                    'Two-way reviews & reputation',
                    'Real-time chat',
                    'Job alerts & push',
                    'Flutter app',
                    'Direct-deal model — no in-app payment cut',
                ],
                'who_for' => ['Freelancers and tradespeople', 'Clients hiring local skills', 'Anyone building a work reputation'],
                'work_slug' => 'nicejob',
                'external_url' => 'https://www.nicejob.co.zw/',
                'screenshot_path' => 'images/people/afr-man-stairs.jpg',
                'detail' => ['long' => 'NiceJob is a mobile-first skills marketplace and reputation platform: freelancers show profiles and portfolios, clients post work, both sides message in real time and leave two-way reviews. Its direct-deal model takes no cut of the payment — the platform earns trust, not transaction fees.'],
                'sort_order' => 4,
            ],
        ];

        foreach ($products as $p) {
            Product::create(array_merge($p, ['is_active' => true]));
        }
    }

    /** 6 case studies (brief §7.4–7.5). Featured: CV263, Recruitment263, NestZim. */
    private function seedWork(): void
    {
        // TODO (brief §11): replace stock imagery with real product screenshots.
        $work = [
            [
                'name' => 'Recruitment263',
                'slug' => 'recruitment263',
                'type' => 'Job Board',
                'status' => 'live',
                'is_featured' => true,
                'is_coming_soon' => false,
                'summary' => 'A national job board spanning every Zimbabwean sector.',
                'description' => 'A national job board across every Zimbabwean sector — free to post and browse, and engineered for search.',
                'technologies' => ['Laravel', 'Filament', 'MySQL', 'JSON-LD SEO'],
                'project_url' => 'https://recruitment263.co.zw',
                'product_slug' => 'recruitment263',
                'image_path' => 'images/live/recruitment263.jpg',
                'detail' => [
                    'challenge' => 'Zimbabwe’s job listings were scattered across Facebook groups, WhatsApp and PDFs — hard to search, easy to scam, and invisible to Google.',
                    'built' => [
                        'A national job board covering corporate, government, NGO, UN, donor and consultancy roles',
                        'Free guest and registered employer posting with admin approval and anti-scam moderation',
                        'Full technical SEO: JobPosting structured data, sitemap and RSS',
                        'Automatic listing expiry on the application deadline',
                        'A Filament admin panel for moderation and a newsletter + blog',
                    ],
                    'outcome' => 'A single, credible, searchable home for Zimbabwean jobs — live and in daily use.',
                ],
                'sort_order' => 0,
            ],
            [
                'name' => 'NestZim',
                'slug' => 'nestzim',
                'type' => 'Property Platform',
                'status' => 'live',
                'is_featured' => true,
                'is_coming_soon' => false,
                'summary' => 'A rental marketplace with verified listings and map search.',
                'description' => 'A Zimbabwean rental marketplace: verified listings, map search, and direct landlord/agent messaging.',
                'technologies' => ['Web + Mobile', 'Maps', 'Paynow'],
                'project_url' => 'https://nestzim.co.zw',
                'product_slug' => 'nestzim',
                'image_path' => 'images/live/nestzim.jpg',
                'detail' => [
                    'challenge' => 'Renters faced unverified listings, middle-men and no easy way to filter by what actually mattered — location, budget, and student housing.',
                    'built' => [
                        'A rental marketplace with verified listings',
                        'Map-based search with location and budget filters',
                        'Direct landlord and agent messaging',
                        'Dedicated student-accommodation filters',
                        'A manager dashboard with analytics and mobile apps with real-time alerts',
                    ],
                    'outcome' => 'A trusted, map-first way to find rentals in Zimbabwe — live on web and mobile.',
                    // TODO (brief §11): confirm exact NestZim technology stack.
                ],
                'sort_order' => 1,
            ],
            [
                'name' => 'CV263',
                'slug' => 'cv263',
                'type' => 'SaaS · Careers',
                'status' => 'live',
                'is_featured' => true,
                'is_coming_soon' => false,
                'summary' => "Zimbabwe's own guided CV and résumé builder.",
                'description' => 'A guided CV and cover-letter builder with ATS-friendly export and AI writing help.',
                'technologies' => ['Laravel', 'Chromium PDF', 'Alpine.js', 'Claude AI', 'Paynow'],
                'project_url' => 'https://cv263.co.zw',
                'product_slug' => 'cv263',
                'image_path' => 'images/live/cv263.jpg',
                'detail' => [
                    'challenge' => 'Most CV tools are foreign, paywalled, and produce files that fail the applicant-tracking systems employers actually use.',
                    'built' => [
                        'A guided two-pane wizard with live preview',
                        'ATS-friendly CV and cover-letter templates',
                        'Genuine PDF and Word export (Chromium rendering)',
                        'AI writing help powered by Claude',
                        'A tailor-to-a-job match and gap score, with honest Paynow billing and no auto-renew',
                    ],
                    'outcome' => 'Live at cv263.co.zw and in daily use by Zimbabwean jobseekers.',
                ],
                'sort_order' => 2,
            ],
            [
                'name' => 'WLSA Zimbabwe',
                'slug' => 'wlsa-zimbabwe',
                'type' => 'NGO Website / CMS',
                'status' => 'live',
                'is_featured' => false,
                'is_coming_soon' => false,
                'summary' => 'Website and content platform for Women and Law in Southern Africa (Zimbabwe).',
                'description' => 'A website and content platform for WLSA Zimbabwe — programmes, legal-aid information, research and donations.',
                'technologies' => ['Laravel', 'Blade', 'MySQL'],
                'project_url' => 'https://wlsazim.co.zw',
                'product_slug' => null,
                'image_path' => 'images/live/wlsa.jpg',
                'detail' => [
                    'challenge' => 'A respected NGO needed to publish programmes, legal-aid information, research and news — and take donations — without depending on a developer for every update.',
                    'built' => [
                        'A public website covering programmes, legal aid, research and news',
                        'A content platform the team can update themselves',
                        'Donation and newsletter capture',
                        'Accessible, credible design fit for an institutional audience',
                    ],
                    'outcome' => 'A live, self-managed platform serving WLSA Zimbabwe and the people it supports.',
                ],
                'sort_order' => 3,
            ],
            [
                'name' => 'Shop263',
                'slug' => 'shop263',
                'type' => 'E-Commerce SaaS',
                'status' => 'live',
                'is_featured' => false,
                'is_coming_soon' => false,
                'summary' => 'A multi-tenant platform to launch a Zimbabwean shop in minutes.',
                'description' => 'A multi-tenant ecommerce platform — any business launches a branded shop with local checkout in minutes.',
                'technologies' => ['Django', 'PostgreSQL (multi-tenant)', 'Celery/Redis', 'HTMX', 'Paynow'],
                'project_url' => 'https://shop263.co.zw',
                'product_slug' => 'shop263',
                'image_path' => 'images/live/shop263.jpg',
                'detail' => [
                    'challenge' => 'Small Zimbabwean businesses sell on social media because building a real store — with local payment — has been too slow and costly.',
                    'built' => [
                        'A multi-tenant platform where any business launches a storefront in minutes',
                        'Inventory, catalogue and order management',
                        'Paynow, EcoCash and USD-cash checkout',
                        'A lightweight POS and a shared marketplace, multi-shop and multi-currency',
                    ],
                    'outcome' => 'Live at shop263.co.zw — any business can open a branded shop in minutes.',
                ],
                'sort_order' => 4,
            ],
            [
                'name' => 'NiceJob',
                'slug' => 'nicejob',
                'type' => 'Skills Marketplace',
                'status' => 'live',
                'is_featured' => false,
                'is_coming_soon' => false,
                'summary' => 'A mobile-first skills marketplace and reputation platform.',
                'description' => 'A mobile-first skills marketplace with two-way reviews, real-time chat and a no-cut direct-deal model.',
                'technologies' => ['Node/TypeScript', 'Express', 'Prisma/PostgreSQL', 'Flutter', 'Next.js', 'Socket.io'],
                'project_url' => 'https://www.nicejob.co.zw/',
                'product_slug' => 'nicejob',
                'image_path' => 'images/people/afr-man-stairs.jpg',
                'detail' => [
                    'challenge' => 'Freelancers and clients had no trusted local place to find each other, prove reputation, and deal directly without losing a cut to the platform.',
                    'built' => [
                        'Freelancer profiles and portfolios',
                        'Two-way reviews and a reputation system',
                        'Real-time chat and push job alerts',
                        'A Flutter mobile app and Next.js web front',
                        'A direct-deal model that takes no cut of payments',
                    ],
                    'outcome' => 'Live at nicejob.co.zw — freelancers and clients dealing directly, with no cut taken on payments.',
                ],
                'sort_order' => 5,
            ],
        ];

        foreach ($work as $w) {
            PortfolioItem::create($w);
        }
    }

    /** Team (brief §7.8). TODO (§11): confirm names + roles before launch. */
    private function seedTeam(): void
    {
        $team = [
            [
                'name' => 'Gift Simau',
                'role' => 'Founder & Lead Strategist',
                'specialisms' => 'Frontend · SEO · AEO/GEO · Content',
                'description' => 'Founder and lead strategist. Drives frontend development, search and answer-engine strategy, content, and client relationships across every Fignoc venture.',
                'photo_path' => 'images/stock-photo-happy-african-american-programmer-smiling-camera-while-sitting-workplace-office.jpg',
                'sort_order' => 0,
            ],
            [
                'name' => 'Cleopas Kandeya',
                'role' => 'Technical Co-Founder',
                'specialisms' => 'Backend · APIs · Platform Architecture',
                'description' => 'Technical co-founder and full-stack engineer. Architects the backends, databases and APIs that power Fignoc\'s products and client deployments.',
                'photo_path' => 'images/stock-photo-african-american-developer-sitting-his-workplace-concentrating-writing-computer-codes.jpg',
                'sort_order' => 1,
            ],
        ];

        foreach ($team as $member) {
            TeamMember::create(array_merge($member, ['is_active' => true]));
        }
    }
}
