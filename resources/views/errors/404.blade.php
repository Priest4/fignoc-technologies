{{-- 404 (brief §7.12) — on-brand, links back to Home + main sections. --}}
<x-layout title="Page not found" description="The page you were looking for could not be found.">
    <section class="section container-x" style="min-height: 60vh; display: flex; align-items: center;">
        <div style="max-width: 40rem;">
            <p class="display" style="font-size: clamp(3rem, 12vw, 6rem); color: var(--color-line-strong);">404</p>
            <h1 class="display mt-2" style="font-size: 2rem;">This page went looking for itself.</h1>
            <p class="mt-4 text-lg leading-8" style="color: var(--color-body);">
                The page you wanted isn't here. Let's get you back on track.
            </p>
            <div class="mt-8 flex flex-wrap gap-3">
                <a href="{{ route('home') }}" class="btn btn-primary">Back to home</a>
                <a href="{{ route('work') }}" class="btn btn-ghost">See the work</a>
            </div>
            <nav class="mt-10 flex flex-wrap gap-x-6 gap-y-2 text-sm" aria-label="Helpful links">
                <a href="{{ route('services') }}" class="link-accent">Services</a>
                <a href="{{ route('products') }}" class="link-accent">Products</a>
                <a href="{{ route('work') }}" class="link-accent">Work</a>
                <a href="{{ route('about') }}" class="link-accent">About</a>
                <a href="{{ route('contact') }}" class="link-accent">Contact</a>
            </nav>
        </div>
    </section>
</x-layout>
