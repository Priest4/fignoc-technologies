{{-- About (brief §7.8). --}}
<x-layout
    title="About"
    description="Fignoc Technologies is a Harare digital product studio and growth agency — we build our own software platforms and serve clients, with a specialism no other Zimbabwean agency offers: AEO and GEO.">

    <x-breadcrumbs :items="[
        ['label' => 'Home', 'url' => route('home')],
        ['label' => 'About', 'url' => route('about')],
    ]" />

    <header class="section container-x">
        <span class="eyebrow reveal">About</span>
        <h1 class="display mt-5 reveal" style="max-width: 20ch;">A Harare studio that builds — and gets you found.</h1>
        <p class="mt-5 max-w-2xl text-lg leading-8 reveal" style="color: var(--color-body);">
            Fignoc Technologies is a digital product studio and growth agency in Harare. We build and run our own software platforms, and we bring that same engineering to client work — from custom systems to getting businesses found on Google and AI answer engines.
        </p>
    </header>

    {{-- Approach --}}
    <section class="section container-x" style="border-top: 1px solid var(--color-line-soft);" aria-labelledby="approach">
        <h2 id="approach" class="display reveal" style="font-size: 1.9rem;">How we work</h2>
        <div class="mt-7 grid gap-5 md:grid-cols-3" data-reveal-group>
            <div class="reveal step-card">
                <span class="icon-tile"><x-ficon name="flag" /></span>
                <h3 class="mt-4" style="font-size: 1.2rem;">Zimbabwe-first</h3>
                <p class="mt-3 leading-7" style="color: var(--color-body);">We build for local realities — mobile data costs, Paynow and EcoCash, and how Zimbabweans actually search — not adapted foreign templates.</p>
            </div>
            <div class="reveal step-card">
                <span class="icon-tile"><x-ficon name="zap" /></span>
                <h3 class="mt-4" style="font-size: 1.2rem;">Build velocity, real discipline</h3>
                <p class="mt-3 leading-7" style="color: var(--color-body);">We move fast with AI-accelerated development, backed by proper engineering — performance budgets, semantic code, and platforms built to last.</p>
            </div>
            <div class="reveal step-card">
                <span class="icon-tile"><x-ficon name="heart" /></span>
                <h3 class="mt-4" style="font-size: 1.2rem;">We eat our own cooking</h3>
                <p class="mt-3 leading-7" style="color: var(--color-body);">Recruitment263, NestZim and more are ours. We ship, run and grow real products — so client work benefits from hard-won production experience.</p>
            </div>
        </div>
    </section>

    {{-- Team (TODO §11: confirm names + roles before launch) --}}
    <section id="team" class="section container-x" style="border-top: 1px solid var(--color-line-soft);" aria-labelledby="team-h">
        <span class="eyebrow reveal">Team</span>
        <h2 id="team-h" class="display mt-4 reveal" style="font-size: 1.9rem;">The people behind Fignoc</h2>
        <div class="mt-7 grid gap-6 sm:grid-cols-2" data-reveal-group>
            @foreach ($team as $member)
                <article class="reveal card card-person">
                    @if ($member->photo_path)
                        <div class="card-person-media">
                            <img src="{{ asset(ltrim($member->photo_path, '/')) }}" alt="{{ $member->name }}"
                                 loading="lazy" decoding="async" width="320" height="360">
                        </div>
                    @endif
                    <div class="card-body">
                        <h3 class="card-title">{{ $member->name }}</h3>
                        <p style="color: var(--color-accent-deep); font-weight: 600; font-size: 0.9rem;">{{ $member->role }}</p>
                        @if ($member->specialisms)
                            <p class="text-sm" style="color: var(--color-muted);">{{ $member->specialisms }}</p>
                        @endif
                        <p class="card-desc">{{ $member->description }}</p>
                    </div>
                </article>
            @endforeach
        </div>
    </section>

    {{-- Differentiator --}}
    <section class="section container-x" style="border-top: 1px solid var(--color-line-soft);">
        <div class="reveal surface" style="background: var(--color-accent-tint); padding: 2.5rem;">
            <span class="eyebrow" style="margin:0;">What makes us different</span>
            <h2 class="mt-4" style="font-size: 1.7rem; max-width: 22ch;">We build for how people search now — including AI.</h2>
            <p class="mt-4 max-w-2xl leading-8" style="color: var(--color-body);">
                As people ask AI assistants instead of scrolling search results, being the <em>answer</em> matters more than being a link. We're the only agency in Zimbabwe that specialises in Answer Engine and Generative Engine Optimisation — and we build the software to back it up.
            </p>
            <a href="{{ route('services.show', 'aeo') }}" class="link-accent font-semibold mt-5 inline-block">Learn about AEO →</a>
        </div>
    </section>

    <x-cta-band />
</x-layout>
