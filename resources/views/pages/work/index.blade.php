{{-- Work index (brief §7.4) — all 6 case studies, featured first, live vs in-dev. --}}
<x-layout
    title="Work"
    description="Selected work from Fignoc Technologies — live platforms like Recruitment263, NestZim and the WLSA Zimbabwe content platform, plus products in active development.">

    <x-breadcrumbs :items="[
        ['label' => 'Home', 'url' => route('home')],
        ['label' => 'Work', 'url' => route('work')],
    ]" />

    <header class="section container-x">
        <span class="eyebrow reveal">Work</span>
        <h1 class="display mt-5 reveal">Proof we deliver.</h1>
        <p class="mt-6 max-w-2xl text-lg leading-8 reveal" style="color: var(--color-body);">
            Real builds for real businesses — the problem, what we shipped, and what it does today. See what we can do for yours.
        </p>
    </header>

    @if ($featured->isNotEmpty())
        <section class="container-x pb-4" aria-label="Featured work">
            <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3" data-reveal-group>
                @foreach ($featured as $w)
                    <div class="reveal">
                        <x-card variant="work" :title="$w->name" :tag="$w->type"
                                :description="$w->summary ?? $w->description"
                                :href="route('work.show', $w)" :state="$w->status"
                                :image="$w->image_path" :mockup="$w->slug" featured />
                    </div>
                @endforeach
            </div>
        </section>
    @endif

    @if ($rest->isNotEmpty())
        <section class="section container-x" aria-label="More work">
            <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3" data-reveal-group>
                @foreach ($rest as $w)
                    <div class="reveal">
                        <x-card variant="work" :title="$w->name" :tag="$w->type"
                                :description="$w->summary ?? $w->description"
                                :href="route('work.show', $w)" :state="$w->status"
                                :image="$w->image_path" :mockup="$w->slug" />
                    </div>
                @endforeach
            </div>
        </section>
    @endif

    <x-cta-band />
</x-layout>
