{{-- Contact (brief §7.9 + §8). WhatsApp-first, email, form, Harare location, FAQ. --}}
@php
    $brand = config('fignoc.brand');
    $waRaw = preg_replace('/\D+/', '', $brand['whatsapp'] ?? '');
    $phoneTel = preg_replace('/\D+/', '', $brand['phone'] ?? '');
@endphp
<x-layout
    title="Contact"
    description="Start a project with Fignoc Technologies — message us on WhatsApp, email sales@fignoc.co.zw, or send the form. Based in Harare, Zimbabwe. We reply within one business day.">

    <x-breadcrumbs :items="[
        ['label' => 'Home', 'url' => route('home')],
        ['label' => 'Contact', 'url' => route('contact')],
    ]" />

    {{-- Intro --}}
    <header class="section container-x contact-intro">
        <div class="contact-intro-grid">
            <div>
                <span class="eyebrow reveal">Contact</span>
                <h1 class="display mt-5 reveal" style="max-width: 14ch;">Let's build something that wins.</h1>
                <p class="mt-6 max-w-xl text-lg leading-8 reveal" style="color: var(--color-body);">
                    Tell us what you're building — or where you want to be found. Free consultation, clear scope, and a reply within one business day.
                </p>
                <div class="mt-7 flex flex-wrap gap-2 reveal">
                    @foreach (['Free consultation', 'Reply in 1 business day', 'You own the code', 'No lock-in'] as $chip)
                        <span class="contact-trust">
                            <svg viewBox="0 0 16 16" fill="none" aria-hidden="true"><path d="M3 8.5l3 3 7-7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            {{ $chip }}
                        </span>
                    @endforeach
                </div>
            </div>
            <aside class="reveal contact-sla surface" aria-label="Response promise">
                <span class="icon-tile"><x-ficon name="zap" /></span>
                <p class="contact-sla-kicker">Response promise</p>
                <p class="contact-sla-value">Within 1 business day</p>
                <p class="contact-sla-copy">WhatsApp is fastest. Prefer email or the form — same team, same reply window.</p>
            </aside>
        </div>
    </header>

    {{-- Channels + form --}}
    <section class="container-x section contact-main" style="padding-top: 0;">
        <div class="contact-layout">
            {{-- Channels --}}
            <div class="reveal contact-channels">
                <h2 class="contact-aside-title">Talk to us directly</h2>
                <p class="contact-aside-sub">Pick the channel that suits you — we read every message.</p>

                <div class="contact-channel-list">
                    @if ($waRaw)
                        <a href="https://wa.me/{{ $waRaw }}?text={{ rawurlencode('Hi Fignoc — I\'d like to start a project.') }}"
                           target="_blank" rel="noopener noreferrer"
                           class="contact-channel contact-channel--wa">
                            <span class="contact-channel-ico" aria-hidden="true">
                                <svg viewBox="0 0 24 24" width="22" height="22" fill="currentColor"><path d="M17.47 14.38c-.3-.15-1.77-.87-2.04-.97-.27-.1-.47-.15-.67.15-.2.3-.77.97-.95 1.17-.17.2-.35.22-.65.07-.3-.15-1.26-.46-2.4-1.48-.89-.79-1.49-1.77-1.66-2.07-.17-.3-.02-.46.13-.61.13-.13.3-.35.45-.52.15-.17.2-.3.3-.5.1-.2.05-.37-.02-.52-.08-.15-.67-1.62-.92-2.22-.24-.58-.49-.5-.67-.51h-.57c-.2 0-.52.07-.8.37-.27.3-1.04 1.02-1.04 2.48s1.07 2.88 1.22 3.08c.15.2 2.1 3.2 5.08 4.49.71.31 1.26.49 1.69.63.71.23 1.36.2 1.87.12.57-.09 1.77-.72 2.02-1.42.25-.7.25-1.3.17-1.42-.07-.12-.27-.2-.57-.35z"/><path d="M12.04 2C6.5 2 2 6.48 2 12c0 1.77.46 3.45 1.28 4.92L2 22l5.22-1.37A9.96 9.96 0 0 0 12.04 22C17.58 22 22 17.52 22 12S17.58 2 12.04 2zm0 18.15c-1.6 0-3.09-.43-4.38-1.18l-.31-.19-3.1.81.83-3.02-.2-.31A8.13 8.13 0 0 1 3.9 12c0-4.49 3.65-8.14 8.14-8.14 4.49 0 8.14 3.65 8.14 8.14 0 4.49-3.65 8.15-8.14 8.15z"/></svg>
                            </span>
                            <span class="contact-channel-body">
                                <span class="contact-channel-label">WhatsApp <span class="chip chip-live" style="margin-left:.35rem;">Fastest</span></span>
                                <span class="contact-channel-value">Message {{ $brand['phone'] }}</span>
                                <span class="contact-channel-hint">Usually replies during business hours</span>
                            </span>
                            <span class="contact-channel-arrow" aria-hidden="true">→</span>
                        </a>
                    @endif

                    <a href="mailto:{{ $brand['email'] }}" class="contact-channel">
                        <span class="contact-channel-ico"><x-ficon name="mail" :size="20" /></span>
                        <span class="contact-channel-body">
                            <span class="contact-channel-label">Email</span>
                            <span class="contact-channel-value">{{ $brand['email'] }}</span>
                            <span class="contact-channel-hint">For briefs, docs and formal enquiries</span>
                        </span>
                        <span class="contact-channel-arrow" aria-hidden="true">→</span>
                    </a>

                    <a href="tel:{{ $phoneTel }}" class="contact-channel">
                        <span class="contact-channel-ico"><x-ficon name="phone" :size="20" /></span>
                        <span class="contact-channel-body">
                            <span class="contact-channel-label">Phone</span>
                            <span class="contact-channel-value">{{ $brand['phone'] }}</span>
                            <span class="contact-channel-hint">Call or save for later</span>
                        </span>
                        <span class="contact-channel-arrow" aria-hidden="true">→</span>
                    </a>

                    <div class="contact-channel contact-channel--static">
                        <span class="contact-channel-ico"><x-ficon name="pin" :size="20" /></span>
                        <span class="contact-channel-body">
                            <span class="contact-channel-label">Studio</span>
                            <span class="contact-channel-value">{{ $brand['address'] }}</span>
                            <span class="contact-channel-hint">Harare · Zimbabwe</span>
                        </span>
                    </div>
                </div>
            </div>

            {{-- Form --}}
            <div class="reveal">
                @if (session('success'))
                    <div class="contact-success" role="status">
                        <span class="icon-tile"><x-ficon name="check" /></span>
                        <div>
                            <p class="contact-success-title">Thanks — your message is on its way.</p>
                            <p class="contact-success-copy">We'll reply within one business day. Prefer WhatsApp for a faster back-and-forth.</p>
                        </div>
                    </div>
                @endif

                <form action="{{ route('contact.store') }}" method="POST" class="contact-form surface">
                    @csrf
                    <div class="contact-form-head">
                        <h2 class="contact-form-title">Send a project brief</h2>
                        <p class="contact-form-sub">A few details are enough. We'll come back with next steps — no obligation.</p>
                    </div>

                    <div class="contact-form-grid">
                        <div>
                            <label for="name" class="contact-label">Your name</label>
                            <input id="name" name="name" type="text" required maxlength="100" value="{{ old('name') }}"
                                   class="form-field" autocomplete="name" placeholder="Jane Doe">
                            @error('name') <p class="form-error">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="email" class="contact-label">Work email</label>
                            <input id="email" name="email" type="email" required maxlength="150" value="{{ old('email') }}"
                                   class="form-field" autocomplete="email" placeholder="you@company.com">
                            @error('email') <p class="form-error">{{ $message }}</p> @enderror
                        </div>
                        <div class="contact-form-span">
                            <label for="service" class="contact-label">What do you need?</label>
                            <select id="service" name="service" required class="form-field">
                                <option value="" disabled {{ old('service') ? '' : 'selected' }}>Select a service…</option>
                                <optgroup label="Build">
                                    <option value="Website development" @selected(old('service') === 'Website development')>Website development &amp; design</option>
                                    <option value="Web systems & applications" @selected(old('service') === 'Web systems & applications')>Web systems &amp; applications</option>
                                    <option value="Custom software" @selected(old('service') === 'Custom software')>Custom software development</option>
                                    <option value="NGO information systems" @selected(old('service') === 'NGO information systems')>NGO information systems</option>
                                    <option value="Ecommerce" @selected(old('service') === 'Ecommerce')>Ecommerce store</option>
                                </optgroup>
                                <optgroup label="Rank">
                                    <option value="SEO" @selected(old('service') === 'SEO')>SEO</option>
                                    <option value="AEO" @selected(old('service') === 'AEO')>AEO — Answer Engine Optimisation</option>
                                    <option value="GEO" @selected(old('service') === 'GEO')>GEO — Generative Engine Optimisation</option>
                                    <option value="Content strategy & audit" @selected(old('service') === 'Content strategy & audit')>Content strategy &amp; audit</option>
                                </optgroup>
                                <optgroup label="Grow">
                                    <option value="Google Ads" @selected(old('service') === 'Google Ads')>Google Ads</option>
                                    <option value="Social media ads" @selected(old('service') === 'Social media ads')>Social media ads</option>
                                    <option value="Customer journey optimisation" @selected(old('service') === 'Customer journey optimisation')>Customer journey optimisation</option>
                                </optgroup>
                                <option value="Not sure — need advice" @selected(old('service') === 'Not sure — need advice')>Not sure — I need advice</option>
                            </select>
                            @error('service') <p class="form-error">{{ $message }}</p> @enderror
                        </div>
                        <div class="contact-form-span">
                            <label for="message" class="contact-label">Your message</label>
                            <textarea id="message" name="message" rows="5" required minlength="10" maxlength="2000"
                                      class="form-field" placeholder="What are you trying to achieve? Timeline or budget hints help — optional.">{{ old('message') }}</textarea>
                            @error('message') <p class="form-error">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="contact-form-foot">
                        <button type="submit" class="btn btn-primary">
                            Send message
                            <svg viewBox="0 0 16 16" fill="none" aria-hidden="true"><path d="M3 8h10M9 4l4 4-4 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </button>
                        <p class="contact-form-note">No spam. We only use your details to reply about this enquiry.</p>
                    </div>
                </form>
            </div>
        </div>
    </section>

    {{-- What happens next --}}
    <section class="section contact-next-band" aria-labelledby="next-heading">
        <div class="container-x">
            <div class="mx-auto text-center reveal" style="max-width: 40rem;">
                <span class="eyebrow" style="justify-content: center;">What happens next</span>
                <h2 id="next-heading" class="display mt-4" style="font-size: clamp(1.6rem, 3vw, 2.2rem);">From message to clear next step.</h2>
            </div>
            <div class="mt-10 grid gap-5 sm:grid-cols-3" data-reveal-group>
                <div class="reveal step-card contact-next-card">
                    <span class="card-index">01</span>
                    <h3 class="mt-3" style="font-size: 1.15rem;">We read your brief</h3>
                    <p class="mt-2 leading-7 text-sm" style="color: var(--color-body);">Same-day triage during business hours — WhatsApp, email or form, same inbox.</p>
                </div>
                <div class="reveal step-card contact-next-card">
                    <span class="card-index">02</span>
                    <h3 class="mt-3" style="font-size: 1.15rem;">Free discovery call</h3>
                    <p class="mt-2 leading-7 text-sm" style="color: var(--color-body);">We clarify the goal, constraints and whether we're the right fit — no pressure.</p>
                </div>
                <div class="reveal step-card contact-next-card">
                    <span class="card-index">03</span>
                    <h3 class="mt-3" style="font-size: 1.15rem;">Honest scope &amp; quote</h3>
                    <p class="mt-2 leading-7 text-sm" style="color: var(--color-body);">Clear deliverables, timeline and price up front. You own everything we ship.</p>
                </div>
            </div>
        </div>
    </section>

    <div style="border-top: 1px solid var(--color-line-soft);">
        <x-faq :items="$faqs" eyebrow="FAQ" heading="Before you ask." />
    </div>

    @push('head')
    <script type="application/ld+json">{!! json_encode([
        '@context' => 'https://schema.org',
        '@type' => 'ContactPage',
        'name' => 'Contact Fignoc Technologies',
        'url' => route('contact'),
        'mainEntity' => [
            '@type' => 'Organization',
            'name' => $brand['name'],
            'email' => $brand['email'],
            'telephone' => '+' . ($waRaw ?: $phoneTel),
            'address' => [
                '@type' => 'PostalAddress',
                'streetAddress' => $brand['address'],
                'addressLocality' => 'Harare',
                'addressCountry' => 'ZW',
            ],
        ],
    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
    @endpush
</x-layout>
