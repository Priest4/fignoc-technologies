{{-- Services index (brief §7.2) — Build / Rank / Grow. --}}
<x-layout
    title="Services"
    description="Fignoc is a software company and growth agency: we build websites, web systems, custom software and ecommerce, then rank you on Google and get you named in AI answers.">

    <x-breadcrumbs :items="[
        ['label' => 'Home', 'url' => route('home')],
        ['label' => 'Services', 'url' => route('services')],
    ]" />

    @php
        $groupMeta = [
            'Build' => ['no' => '01', 'blurb' => 'Websites, web systems, custom software and online stores — engineered for performance, not dragged out of a template.'],
            'Rank'  => ['no' => '02', 'blurb' => 'Get found where Zimbabwe now searches — Google, and the AI answer engines your customers ask first.'],
            'Grow'  => ['no' => '03', 'blurb' => 'Turn visibility into enquiries with accountable paid media and customer-journey optimisation.'],
        ];
    @endphp

    <header class="section container-x">
        <span class="eyebrow reveal">Services</span>
        <h1 class="display mt-5 reveal" style="max-width: 16ch;">Build it. Rank it. Grow it.</h1>
        <p class="mt-6 max-w-2xl text-lg leading-8 reveal" style="color: var(--color-body);">
            One team for the whole job — a software company that builds the platform, and a growth agency that makes sure your customers (and the AI they ask) find you.
        </p>
    </header>

    @foreach (['Build', 'Rank', 'Grow'] as $cat)
        @php $items = $grouped->get($cat, collect()); @endphp
        <section class="section container-x" style="border-top: 1px solid var(--color-line-soft);" aria-labelledby="grp-{{ $cat }}">
            <div class="grid gap-10 lg:grid-cols-[0.7fr_1.6fr]">
                <div class="reveal">
                    <span class="card-index">{{ $groupMeta[$cat]['no'] }}</span>
                    <h2 id="grp-{{ $cat }}" class="mt-2" style="font-size: 1.9rem;">{{ $cat }}</h2>
                    <p class="mt-4 max-w-sm leading-7" style="color: var(--color-body);">{{ $groupMeta[$cat]['blurb'] }}</p>
                    @if ($cat === 'Rank')
                        <p class="mt-5 font-semibold" style="color: var(--color-accent-deep);">We optimise for AI answers, not just blue links.</p>
                    @endif
                </div>
                <div class="grid gap-6 sm:grid-cols-2" data-reveal-group>
                    @foreach ($items as $s)
                        <div class="reveal">
                            <x-card
                                variant="service"
                                :title="$s->name"
                                :tag="$s->tag"
                                :description="$s->description"
                                :href="route('services.show', $s)"
                                :featured="$s->is_featured" />
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endforeach

    <x-cta-band />
</x-layout>
