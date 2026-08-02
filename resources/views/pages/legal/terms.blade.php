{{-- Terms (brief §7.11). Placeholder copy — TODO: legal review before launch. --}}
<x-layout title="Terms of Service" description="The terms governing use of the Fignoc Technologies website and services.">

    <x-breadcrumbs :items="[
        ['label' => 'Home', 'url' => route('home')],
        ['label' => 'Terms', 'url' => route('terms')],
    ]" />

    <article class="section container-x">
        <div style="max-width: 46rem;">
            <span class="eyebrow">Legal</span>
            <h1 class="display mt-5">Terms of Service</h1>
            <p class="mt-4" style="color: var(--color-muted);">Last updated: {{ date('F Y') }}</p>

            {{-- TODO (brief §7.11 / §11): replace with lawyer-reviewed terms before launch. --}}
            <div class="prose mt-10">
                <p><strong>This is placeholder copy pending legal review.</strong> It must be reviewed by a qualified professional before the site goes live.</p>

                <h2>Using this website</h2>
                <p>By accessing this website you agree to use it lawfully and not to misuse, disrupt, or attempt to gain unauthorised access to it.</p>

                <h2>Our services</h2>
                <p>Descriptions of products and services on this site are for information. Specific engagements are governed by a separate written agreement between you and Fignoc Technologies.</p>

                <h2>Intellectual property</h2>
                <p>Unless stated otherwise, content on this site is owned by Fignoc Technologies. Software we build for clients under a signed agreement is owned as set out in that agreement.</p>

                <h2>Liability</h2>
                <p>The site is provided "as is". To the extent permitted by law, we are not liable for losses arising from use of the site.</p>

                <h2>Contact</h2>
                <p>Questions about these terms? Email {{ config('fignoc.brand.email') }} or use our <a href="{{ route('contact') }}">contact page</a>.</p>
            </div>
        </div>
    </article>
</x-layout>
