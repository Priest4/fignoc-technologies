{{-- Privacy policy (brief §7.11). Placeholder copy — TODO: legal review before launch. --}}
<x-layout title="Privacy Policy" description="How Fignoc Technologies collects, uses and protects your information.">

    <x-breadcrumbs :items="[
        ['label' => 'Home', 'url' => route('home')],
        ['label' => 'Privacy', 'url' => route('privacy')],
    ]" />

    <article class="section container-x">
        <div style="max-width: 46rem;">
            <span class="eyebrow">Legal</span>
            <h1 class="display mt-5">Privacy Policy</h1>
            <p class="mt-4" style="color: var(--color-muted);">Last updated: {{ date('F Y') }}</p>

            {{-- TODO (brief §7.11 / §11): replace with lawyer-reviewed policy before launch. --}}
            <div class="prose mt-10">
                <p><strong>This is placeholder copy pending legal review.</strong> It describes Fignoc Technologies' general approach and must be reviewed by a qualified professional before the site goes live.</p>

                <h2>Who we are</h2>
                <p>Fignoc Technologies ("we") is a digital product studio and growth agency based at {{ config('fignoc.brand.address') }}. You can contact us at {{ config('fignoc.brand.email') }}.</p>

                <h2>What we collect</h2>
                <p>When you contact us through the site, we collect the details you provide — such as your name, email address and message — so we can respond to your enquiry. We may also collect anonymous usage analytics to improve the site.</p>

                <h2>How we use your information</h2>
                <p>We use your information solely to respond to your enquiry, deliver services you request, and improve our website. We do not sell your personal information.</p>

                <h2>Analytics &amp; cookies</h2>
                <p>The site may use privacy-respecting analytics (e.g. Google Analytics) to understand aggregate usage. You can control cookies through your browser settings.</p>

                <h2>Your rights</h2>
                <p>You may request access to, correction of, or deletion of the personal information we hold about you by contacting {{ config('fignoc.brand.email') }}.</p>

                <h2>Contact</h2>
                <p>Questions about this policy? Email {{ config('fignoc.brand.email') }} or use our <a href="{{ route('contact') }}">contact page</a>.</p>
            </div>
        </div>
    </article>
</x-layout>
