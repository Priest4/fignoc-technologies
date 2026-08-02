{{-- Products index (brief §7.6) — 5 sellable platforms. --}}
<x-layout
    title="Products"
    description="Software platforms Fignoc builds and sells — Recruitment263, NestZim, CV263 and Shop263 are live, with NiceJob launching soon.">

    <x-breadcrumbs :items="[
        ['label' => 'Home', 'url' => route('home')],
        ['label' => 'Products', 'url' => route('products')],
    ]" />

    <header class="section container-x">
        <span class="eyebrow reveal">Products</span>
        <h1 class="display mt-5 reveal">Platforms that already work.</h1>
        <p class="mt-6 max-w-2xl text-lg leading-8 reveal" style="color: var(--color-body);">
            Real software we built, run and grow ourselves — use it today, and see exactly what we can build for your business.
        </p>
    </header>

    <section class="container-x section" style="padding-top: 0;">
        <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3" data-reveal-group>
            @foreach ($products as $p)
                <div class="reveal">
                    <x-card variant="product" :title="$p->name" :tag="$p->tag"
                            :description="$p->description" :href="route('products.show', $p)"
                            :state="$p->status" :image="$p->screenshot_path" :mockup="$p->slug" />
                </div>
            @endforeach
        </div>
    </section>

    <x-cta-band />
</x-layout>
