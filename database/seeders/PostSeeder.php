<?php

namespace Database\Seeders;

use App\Models\Post;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

/** Starter Insights posts (brief §7.10) — AEO/GEO topics that double as
 *  Fignoc's own answer-engine surface. Add more via the DB or a future admin. */
class PostSeeder extends Seeder
{
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        Post::truncate();
        Schema::enableForeignKeyConstraints();

        $posts = [
            [
                'slug' => 'what-is-answer-engine-optimisation',
                'title' => 'What is Answer Engine Optimisation (AEO)? A Zimbabwean business guide',
                'excerpt' => 'AEO is how your business becomes the answer AI tools give — not just a link in a list. Here is what it means, and why it matters in Zimbabwe now.',
                'cover_path' => 'images/tech-network.jpg',
                'author' => 'Gift Simau',
                'read_minutes' => 4,
                'published_at' => '2026-07-10 09:00:00',
                'body' => <<<'MD'
When a customer wants to find a business today, they increasingly *ask* rather than *search*. They type a full question into ChatGPT, Gemini, Perplexity, or read Google's AI Overview at the top of the page — and they act on the single answer they get back.

**Answer Engine Optimisation (AEO)** is the practice of making your business that answer.

## Why it matters in Zimbabwe

Data is expensive and time is short. When someone asks an AI "who builds ecommerce sites in Harare?", they rarely scroll ten blue links — they trust the named answer. If that answer is a competitor, the click never reaches you.

## What AEO actually involves

- Clear **entity signals** so engines know who you are and what you do
- **Question-shaped content** and FAQs an answer engine can lift directly
- **Structured data** (FAQPage, Organization, Product) machines can parse
- Being consistently described the same way across the web

AEO does not replace SEO — it extends it to the way people search now. If you want to be recommended, not just listed, that is the work.

*Fignoc is Zimbabwe's only agency specialising in AEO and GEO. [Start a project](/contact) to see how AI engines currently describe your business.*
MD,
            ],
            [
                'slug' => 'aeo-vs-seo-vs-geo',
                'title' => 'AEO vs SEO vs GEO: what is the difference?',
                'excerpt' => 'Three acronyms, three jobs. Here is a plain-language breakdown of SEO, AEO and GEO — and why you need all three.',
                'cover_path' => 'images/proof/seo-results.jpg',
                'author' => 'Gift Simau',
                'read_minutes' => 3,
                'published_at' => '2026-07-15 09:00:00',
                'body' => <<<'MD'
They sound similar, but they target different moments in how people find information.

## SEO — Search Engine Optimisation
Getting your pages to rank in the classic list of results. Still essential — it is the foundation the others build on.

## AEO — Answer Engine Optimisation
Getting your business named in the *direct answer*: Google's AI Overviews, ChatGPT, Gemini, Perplexity. The user asks a question; you are the reply.

## GEO — Generative Engine Optimisation
Shaping how large language models *generate and cite* information about you — the sources they trust, the way they summarise your category, whether they recommend you at all.

## Do you need all three?

Yes. SEO earns the ranking, AEO wins the answer, GEO shapes the recommendation. They stack. Most Zimbabwean businesses do some SEO and no AEO or GEO — which is exactly the gap Fignoc was built to close.

*Want the full picture for your business? [Talk to us](/contact).*
MD,
            ],
            [
                'slug' => 'show-up-in-ai-answers-zimbabwe',
                'title' => 'How Zimbabwean businesses can show up in AI answers',
                'excerpt' => 'Five practical moves to make your business more likely to be named when someone asks an AI assistant about your category.',
                'cover_path' => 'images/people/afr-woman-laptop.jpg',
                'author' => 'Gift Simau',
                'read_minutes' => 5,
                'published_at' => '2026-07-20 09:00:00',
                'body' => <<<'MD'
You cannot buy your way into an AI answer, but you can earn it. Here are five moves that make a real difference.

1. **Answer real questions, in full.** Publish clear, question-led content that resolves what your customers actually ask. Answer engines reward pages that resolve intent.
2. **Add structured data.** Mark up your organisation, products and FAQs so machines can read them unambiguously.
3. **Be consistent everywhere.** Your name, category, location and contact details should match across your site, directories and profiles — mixed signals confuse engines.
4. **Earn trusted mentions.** Generative engines lean on sources they trust. Being referenced by credible third parties strengthens how models describe you.
5. **Measure the output.** Regularly check how ChatGPT, Gemini and Google describe your business — then close the gaps.

None of this is a one-off. It is an ongoing discipline — the same one we apply to our own platforms.

*Fignoc builds this into every project. [Start a project](/contact) and we'll audit how AI currently sees you.*
MD,
            ],
            [
                'slug' => 'ecommerce-zimbabwe-paynow-ecocash-seo',
                'title' => 'Ecommerce in Zimbabwe: Paynow, EcoCash — and getting found',
                'excerpt' => 'A store that takes local payments is only half the job. Here is how to build ecommerce that converts on mobile and ranks where Zimbabwean buyers search.',
                'cover_path' => 'images/live/shop263.jpg',
                'author' => 'Cleopas Kandeya',
                'read_minutes' => 6,
                'published_at' => '2026-07-28 09:00:00',
                'body' => <<<'MD'
Zimbabwean shoppers do not browse the way Silicon Valley decks assume. Data is metered. Phones are the primary screen. Checkout has to speak **Paynow**, **EcoCash** and USD — not only international cards.

## Build for the real cart

- Mobile-first layouts that stay fast on mid-range Android
- Clear stock, delivery and price signals above the fold
- Local payment rails wired into a conversion-tracked funnel

A beautiful catalogue that fails at payment is not a store. It is a brochure.

## Then make the store discoverable

SEO still matters: category pages, product schema, and content that answers “where can I buy X in Harare?”. Layer AEO on top so AI assistants can recommend you by name when someone asks for a trusted local shop.

## What we run ourselves

Shop263 is our own store stack — payments, POS thinking, and search built in. We do not sell theory; we sell what we operate.

*[Start a project](/contact) if you want a store that takes money and gets found.*
MD,
            ],
            [
                'slug' => 'ngo-websites-zimbabwe-that-get-found',
                'title' => 'NGO websites in Zimbabwe that people can actually find',
                'excerpt' => 'Programmes, legal aid and donations only help if communities and partners can find them. How we think about information systems for NGOs.',
                'cover_path' => 'images/live/wlsa.jpg',
                'author' => 'Cleopas Kandeya',
                'read_minutes' => 5,
                'published_at' => '2026-08-01 09:00:00',
                'body' => <<<'MD'
An NGO site is not a brochure for donors in London alone. It is often the public front door for **programmes**, **research**, **legal-aid information** and **donations** — for people searching on a phone in Harare.

## Clarity beats decoration

Structure content around the questions people ask: Who do you help? How do I get support? How do I donate? Where are you working this year?

## Make it machine-readable

Structured data, clean FAQs and consistent naming help search engines — and increasingly AI answers — cite your organisation accurately. That is how partners and beneficiaries find the right page instead of a dead PDF.

## Proof we maintain

We built and maintain the content platform for **WLSA Zimbabwe** — programmes, research and donations in one maintainable system.

*Building for impact? [Talk to us](/contact).*
MD,
            ],
            [
                'slug' => 'google-ai-overview-harare-businesses',
                'title' => 'Google AI Overviews: what Harare businesses should do now',
                'excerpt' => 'AI Overviews sit above the classic blue links. Here is a practical checklist so your business can be named — not skipped — when locals ask Google.',
                'cover_path' => 'images/proof/ai-overview.jpg',
                'author' => 'Gift Simau',
                'read_minutes' => 4,
                'published_at' => '2026-08-02 09:00:00',
                'body' => <<<'MD'
When Google shows an **AI Overview**, many people never scroll to result number four. The overview becomes the answer. If your competitors are named and you are not, you lost the click before the SERP finished loading.

## What earns a mention

- Pages that answer the question completely, in plain language
- Clear entity signals: who you are, where you are, what you sell
- FAQ and Organization markup engines can parse
- Corroboration across the web (consistent NAP, credible mentions)

## What does not work

Stuffing keywords into thin pages. Buying fake reviews. Copying US playbooks that ignore Zimbabwean search language and payment realities.

## Our own proof

Recruitment263 is cited in Google AI Overviews for job-related queries — because we treat answer-engine work as product work, not a side SEO checklist.

*[Start a project](/contact) for an audit of how Overviews currently describe you.*
MD,
            ],
        ];

        foreach ($posts as $p) {
            Post::create($p);
        }
    }
}
