{{--
  Site header with Elementor-style mega-menu dropdowns (Services, Products).
  Fixed, translucent-on-scroll, hide-on-scroll-down (motion.js). Hover opens
  the mega panels on desktop (with a small close delay); full-screen accordion
  menu on mobile.
--}}
@php
    $navServices = \App\Models\Service::active()->get()->groupBy('category');
    $navProducts = \App\Models\Product::active()->get();
    $catBlurb = ['Build' => 'Software, web & ecommerce', 'Rank' => 'Search, AEO & GEO', 'Grow' => 'Ads & conversion'];
    $svcIcon = [
        'web-development' => 'globe', 'web-systems' => 'server', 'custom-software' => 'code',
        'ngo-systems' => 'heart', 'ecommerce' => 'cart', 'seo' => 'search',
        'aeo' => 'sparkles', 'geo' => 'sparkles', 'content-strategy' => 'layers',
        'google-ads' => 'trending-up', 'social-ads' => 'trending-up', 'customer-journey-optimisation' => 'trending-up',
        'app-development' => 'phone',
    ];
    $prodIcon = ['cv263' => 'briefcase', 'recruitment263' => 'search', 'shop263' => 'cart', 'nestzim' => 'pin', 'nicejob' => 'heart'];
@endphp

<header
    data-site-header
    x-data="{ open:false, mega:null, t:null, show(m){ clearTimeout(this.t); this.mega=m; }, hide(){ this.t=setTimeout(()=>{ this.mega=null }, 160); } }"
    x-effect="$el.dataset.menuOpen = open; document.body.classList.toggle('menu-open', open)"
    @keydown.escape.window="open=false; mega=null"
    class="site-header"
    :class="{ 'is-menu-open': open }">
    <div class="nav-shell px-3 sm:px-4">
        <div class="nav-pill">
        <div class="nav-bar">
            <a href="{{ route('home') }}" class="wordmark" aria-label="Fignoc Technologies — home" @click="open=false">
                <span class="wordmark-name">Fignoc<span class="wordmark-dot">.</span></span>
                <span class="wordmark-sub">Technologies</span>
            </a>

            <nav class="hidden lg:flex items-center gap-8" aria-label="Primary">
                <a href="{{ route('home') }}" class="nav-link" @if(request()->routeIs('home')) aria-current="page" @endif>Home</a>
                <div @mouseenter="show('services')" @mouseleave="hide()" @focusin="show('services')" @focusout="hide()">
                    <a href="{{ route('services') }}" class="nav-link inline-flex items-center gap-1.5" aria-haspopup="true" :aria-expanded="(mega==='services').toString()" @if(request()->routeIs('services*')) aria-current="page" @endif>
                        Services
                        <svg class="nav-caret" viewBox="0 0 12 12" fill="none" aria-hidden="true" :style="mega==='services' ? 'transform:rotate(180deg)' : ''"><path d="M3 4.5 6 7.5 9 4.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </a>
                </div>
                <a href="{{ route('work') }}" class="nav-link" @if(request()->routeIs('work*')) aria-current="page" @endif>Work</a>
                <div @mouseenter="show('products')" @mouseleave="hide()" @focusin="show('products')" @focusout="hide()">
                    <a href="{{ route('products') }}" class="nav-link inline-flex items-center gap-1.5" aria-haspopup="true" :aria-expanded="(mega==='products').toString()" @if(request()->routeIs('products*')) aria-current="page" @endif>
                        Products
                        <svg class="nav-caret" viewBox="0 0 12 12" fill="none" aria-hidden="true" :style="mega==='products' ? 'transform:rotate(180deg)' : ''"><path d="M3 4.5 6 7.5 9 4.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </a>
                </div>
                <a href="{{ route('about') }}" class="nav-link" @if(request()->routeIs('about')) aria-current="page" @endif>About</a>
                <a href="{{ route('insights') }}" class="nav-link" @if(request()->routeIs('insights*')) aria-current="page" @endif>Insights</a>
                <a href="{{ route('contact') }}" class="nav-link" @if(request()->routeIs('contact')) aria-current="page" @endif>Contact</a>
            </nav>

            <div class="hidden lg:block">
                <a href="{{ route('contact') }}" class="btn btn-primary">Start a project
                    <svg viewBox="0 0 16 16" fill="none" aria-hidden="true"><path d="M3 8h10M9 4l4 4-4 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </a>
            </div>

            <button type="button"
                    class="nav-toggle lg:hidden"
                    @click="open=!open"
                    :aria-expanded="open.toString()"
                    aria-controls="mobile-menu"
                    :aria-label="open ? 'Close menu' : 'Open menu'">
                <span class="nav-toggle-box" aria-hidden="true">
                    <span class="nav-toggle-line"></span>
                    <span class="nav-toggle-line"></span>
                    <span class="nav-toggle-line"></span>
                </span>
            </button>
        </div>
        </div>
    </div>

    {{-- Mega: Services --}}
    <div x-show="mega==='services'" x-cloak x-transition.opacity.duration.150ms
         @mouseenter="show('services')" @mouseleave="hide()" @focusin="show('services')" @focusout="hide()" class="mega hidden lg:block">
        <div class="mega-inner grid grid-cols-3 gap-8">
            @foreach (['Build', 'Rank', 'Grow'] as $cat)
                <div>
                    <p class="mega-group-label">{{ $cat }} · {{ $catBlurb[$cat] ?? '' }}</p>
                    @foreach ($navServices->get($cat, collect()) as $s)
                        <a href="{{ route('services.show', $s) }}" class="mega-link">
                            <span class="ml-ico"><x-ficon :name="$svcIcon[$s->slug] ?? 'code'" :size="18" /></span>
                            <span><span class="ml-title">{{ $s->name }}</span><span class="ml-sub">{{ $s->description }}</span></span>
                        </a>
                    @endforeach
                </div>
            @endforeach
        </div>
    </div>

    {{-- Mega: Products --}}
    <div x-show="mega==='products'" x-cloak x-transition.opacity.duration.150ms
         @mouseenter="show('products')" @mouseleave="hide()" @focusin="show('products')" @focusout="hide()" class="mega hidden lg:block">
        <div class="mega-inner grid grid-cols-3 gap-4">
            @foreach ($navProducts as $p)
                <a href="{{ route('products.show', $p) }}" class="mega-link">
                    <span class="ml-ico"><x-ficon :name="$prodIcon[$p->slug] ?? 'briefcase'" :size="18" /></span>
                    <span><span class="ml-title">{{ $p->name }} @if($p->status !== 'live')<span class="chip chip-accent" style="margin-left:.25rem;">Soon</span>@endif</span><span class="ml-sub">{{ $p->tag }}</span></span>
                </a>
            @endforeach
            <a href="{{ route('products') }}" class="mega-link" style="align-items:center;">
                <span class="ml-title link-accent">All products →</span>
            </a>
        </div>
    </div>

    {{-- Full-screen mobile menu with accordions --}}
    <div id="mobile-menu"
         x-show="open"
         x-cloak
         x-transition:enter="mm-enter"
         x-transition:enter-start="mm-enter-start"
         x-transition:enter-end="mm-enter-end"
         x-transition:leave="mm-leave"
         x-transition:leave-start="mm-leave-start"
         x-transition:leave-end="mm-leave-end"
         class="mobile-menu lg:hidden"
         x-data="{ acc:null }"
         role="dialog"
         aria-modal="true"
         aria-label="Site menu">
        <div class="mobile-menu-panel">
            <nav class="mobile-nav" aria-label="Mobile">
                <a href="{{ route('home') }}" class="m-link" @click="open=false" @if(request()->routeIs('home')) aria-current="page" @endif>Home</a>

                <div class="m-group">
                    <button type="button" class="m-link m-link-btn" @click="acc = acc==='s' ? null : 's'" :aria-expanded="(acc==='s').toString()">
                        <span>Services</span>
                        <svg class="m-caret" width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true" :class="{ 'is-open': acc==='s' }"><path d="M6 9l6 6 6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </button>
                    <div class="m-acc-panel" :class="{ 'open': acc==='s' }">
                        <div class="m-acc-inner">
                            @foreach (['Build', 'Rank', 'Grow'] as $cat)
                                <p class="m-cat">{{ $cat }}</p>
                                @foreach ($navServices->get($cat, collect()) as $s)
                                    <a href="{{ route('services.show', $s) }}" class="m-sub" @click="open=false">
                                        <span class="m-sub-ico"><x-ficon :name="$svcIcon[$s->slug] ?? 'code'" :size="16" /></span>
                                        {{ $s->name }}
                                    </a>
                                @endforeach
                            @endforeach
                            <a href="{{ route('services') }}" class="m-sub m-sub-all" @click="open=false">All services →</a>
                        </div>
                    </div>
                </div>

                <a href="{{ route('work') }}" class="m-link" @click="open=false" @if(request()->routeIs('work*')) aria-current="page" @endif>Work</a>

                <div class="m-group">
                    <button type="button" class="m-link m-link-btn" @click="acc = acc==='p' ? null : 'p'" :aria-expanded="(acc==='p').toString()">
                        <span>Products</span>
                        <svg class="m-caret" width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true" :class="{ 'is-open': acc==='p' }"><path d="M6 9l6 6 6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </button>
                    <div class="m-acc-panel" :class="{ 'open': acc==='p' }">
                        <div class="m-acc-inner">
                            @foreach ($navProducts as $p)
                                <a href="{{ route('products.show', $p) }}" class="m-sub" @click="open=false">
                                    <span class="m-sub-ico"><x-ficon :name="$prodIcon[$p->slug] ?? 'briefcase'" :size="16" /></span>
                                    {{ $p->name }}
                                    @if ($p->status !== 'live')
                                        <span class="chip chip-accent" style="margin-left:auto;">Soon</span>
                                    @endif
                                </a>
                            @endforeach
                            <a href="{{ route('products') }}" class="m-sub m-sub-all" @click="open=false">All products →</a>
                        </div>
                    </div>
                </div>

                <a href="{{ route('about') }}" class="m-link" @click="open=false" @if(request()->routeIs('about')) aria-current="page" @endif>About</a>
                <a href="{{ route('insights') }}" class="m-link" @click="open=false" @if(request()->routeIs('insights*')) aria-current="page" @endif>Insights</a>
                <a href="{{ route('contact') }}" class="m-link" @click="open=false" @if(request()->routeIs('contact')) aria-current="page" @endif>Contact</a>
            </nav>

            <div class="mobile-menu-foot">
                <a href="{{ route('contact') }}" class="btn btn-primary w-full" @click="open=false">
                    Start a project
                    <svg viewBox="0 0 16 16" fill="none" aria-hidden="true"><path d="M3 8h10M9 4l4 4-4 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </a>
                <div class="mobile-menu-meta">
                    <a href="mailto:{{ config('fignoc.brand.email') }}">{{ config('fignoc.brand.email') }}</a>
                    <a href="tel:{{ preg_replace('/\D+/', '', config('fignoc.brand.phone')) }}">{{ config('fignoc.brand.phone') }}</a>
                </div>
            </div>
        </div>
    </div>
</header>
