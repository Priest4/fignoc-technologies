/* ---------------------------------------------------------------------------
   Fignoc — global motion / scroll-engineering module (brief §5).

   register / init / destroy pattern. One module owns all page animation:
     • Entrance reveals + count-up + condensing header  → all breakpoints
     • Cinematic hero pin-and-scrub                    → desktop ≥1025
     • Cinematic hero mobile pin (compact gallery)     → < 1025
     • Heavy choreography (hero pin-and-dock)           → desktop ≥1025 only
   Animates transform + opacity only. Rebuilds on significant resize.
   prefers-reduced-motion: reduce  → everything static, no pins, no transforms.
--------------------------------------------------------------------------- */
import { gsap } from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';

gsap.registerPlugin(ScrollTrigger);

const DESKTOP = 1025;
const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

const state = {
  vw: 0,
  rem: 16,
  ctx: null,          // gsap.context() for easy teardown
  observers: [],      // IntersectionObservers to disconnect
  cleanups: [],       // misc event listeners to remove
  built: false,
};

/* ---- helpers ------------------------------------------------------------ */
function cacheEnv() {
  state.vw = window.innerWidth;
  state.rem = parseFloat(getComputedStyle(document.documentElement).fontSize) || 16;
}
const isDesktop = () => window.innerWidth >= DESKTOP;

/* ---- entrance reveals (all breakpoints) --------------------------------- */
function initReveals() {
  const els = document.querySelectorAll('.reveal');
  if (!els.length) return;

  if (reduceMotion) {
    els.forEach((el) => el.classList.add('is-visible'));
    return;
  }

  const io = new IntersectionObserver(
    (entries) => {
      entries.forEach((e) => {
        if (!e.isIntersecting) return;
        const el = e.target;
        const delay = parseFloat(el.dataset.revealDelay || 0);
        // Stagger children of a [data-reveal-group] on 70ms cadence.
        el.style.transitionDelay = `${delay}ms`;
        el.classList.add('is-visible');
        io.unobserve(el);
      });
    },
    { threshold: 0.12, rootMargin: '0px 0px -8% 0px' }
  );

  document.querySelectorAll('[data-reveal-group]').forEach((group) => {
    group.querySelectorAll('.reveal').forEach((el, i) => {
      el.dataset.revealDelay = String(i * 70);
    });
  });

  els.forEach((el) => io.observe(el));
  state.observers.push(io);
}

/* ---- count-up stats ----------------------------------------------------- */
function initCountUp() {
  const els = document.querySelectorAll('[data-countup]');
  if (!els.length) return;

  const run = (el) => {
    const to = parseFloat(el.dataset.countup);
    const suffix = el.dataset.countupSuffix || '';
    const prefix = el.dataset.countupPrefix || '';
    if (reduceMotion || Number.isNaN(to)) {
      el.textContent = `${prefix}${el.dataset.countup}${suffix}`;
      return;
    }
    const obj = { v: 0 };
    gsap.to(obj, {
      v: to,
      duration: 1.4,
      ease: 'power2.out',
      onUpdate() {
        el.textContent = `${prefix}${Math.round(obj.v)}${suffix}`;
      },
    });
  };

  const io = new IntersectionObserver(
    (entries) => {
      entries.forEach((e) => {
        if (!e.isIntersecting) return;
        run(e.target);
        io.unobserve(e.target);
      });
    },
    { threshold: 0.5 }
  );
  els.forEach((el) => io.observe(el));
  state.observers.push(io);
}

/* ---- condensing + hide-on-scroll header --------------------------------- */
function initHeader() {
  const header = document.querySelector('[data-site-header]');
  if (!header) return;

  let lastY = window.scrollY;
  let ticking = false;

  const update = () => {
    const y = window.scrollY;
    header.classList.toggle('is-scrolled', y > 8);

    // Hide on scroll-down, reappear on scroll-up. Stay visible when the
    // mobile menu is open (data-menu-open set by the header component).
    const menuOpen = header.dataset.menuOpen === 'true';
    if (!menuOpen && !reduceMotion) {
      if (y > lastY && y > 120) header.classList.add('is-hidden');
      else header.classList.remove('is-hidden');
    } else {
      header.classList.remove('is-hidden');
    }
    lastY = y;
    ticking = false;
  };

  const onScroll = () => {
    if (!ticking) {
      window.requestAnimationFrame(update);
      ticking = true;
    }
  };
  window.addEventListener('scroll', onScroll, { passive: true });
  state.cleanups.push(() => window.removeEventListener('scroll', onScroll));
  update();
}

/* ---- desktop hero pin-and-dock (home) ----------------------------------- */
/* Contract with the Home hero markup:
     [data-hero-pin]     — section that pins
     [data-hero-console] — the "answer console" panel that shrinks + docks
     [data-hero-dock]    — empty placeholder slot below where it docks        */
function initHeroDock() {
  const pin = document.querySelector('[data-hero-pin]');
  const consoleEl = document.querySelector('[data-hero-console]');
  const dock = document.querySelector('[data-hero-dock]');
  if (!pin || !consoleEl || !dock) return;
  if (reduceMotion || !isDesktop()) return; // static stacked below 1025

  ScrollTrigger.create({
    trigger: pin,
    start: 'top top',
    end: '+=90%',
    pin: true,
    scrub: 0.6,
    anticipatePin: 1,
    onUpdate(self) {
      const p = self.progress;
      const from = consoleEl.getBoundingClientRect();
      const to = dock.getBoundingClientRect();
      // Interpolate the console toward the dock slot (transform-only).
      const dx = (to.left - from.left) * p;
      const dy = (to.top - from.top) * p;
      const scale = 1 - 0.28 * p;
      gsap.set(consoleEl, {
        x: dx,
        y: dy,
        scale,
        transformOrigin: 'top left',
        borderRadius: `${2 + 10 * p}px`,
      });
      gsap.set(dock, { opacity: p });
    },
  });
}

/* ---- cinematic hero (home) — fullscreen video collapses to a centred
   rectangle while two image rows assemble above & below. Desktop + motion only.
   Contract with markup:
     [data-cine]          — pinned section
     [data-cine-video]    — video box (starts scaled to cover, shrinks to 1)
     [data-cine-headline] — big headline over the fullscreen video (fades out)
     [data-cine-caption]  — end-state heading beside the shrunk video (fades in)
     [data-cine-top]/[-bottom] — the two image rows (slide + drift in)          */
function initCineHero() {
  const hero = document.querySelector('[data-cine]');
  if (!hero) return;
  const video = hero.querySelector('[data-cine-video]');
  const headline = hero.querySelector('[data-cine-headline]');
  const title = hero.querySelector('[data-cine-title]');
  const rowTop = hero.querySelector('[data-cine-top]');
  const rowBottom = hero.querySelector('[data-cine-bottom]');
  if (!video || reduceMotion || !isDesktop()) return;

  // Load + play the hero MP4 only on desktop (mobile stays poster-only for data/CWV).
  const videoEl = video.querySelector('[data-cine-video-el], video');
  const sourceEl = videoEl?.querySelector('source[data-src]');
  if (videoEl && sourceEl && !sourceEl.getAttribute('src')) {
    sourceEl.setAttribute('src', sourceEl.getAttribute('data-src'));
    videoEl.load();
    videoEl.play().catch(() => {});
  }

  // Framed start (Elementor-style): the video fills the hero at scale 0.92 so the
  // brand gradient shows around it, then shrinks + fades as the gallery assembles.
  gsap.set(video, { scale: 0.92, transformOrigin: 'center center' });
  gsap.set(rowTop, { opacity: 0, yPercent: -55 });
  gsap.set(rowBottom, { opacity: 0, yPercent: 55 });
  gsap.set(title, { opacity: 0, y: 16 });
  gsap.set(headline, { opacity: 1 });

  const tl = gsap.timeline({
    scrollTrigger: {
      trigger: hero, start: 'top top', end: '+=175%',
      pin: true, scrub: 0.7, anticipatePin: 1,
    },
  });
  tl.to(headline, { opacity: 0, scale: 0.94, ease: 'none', duration: 0.35 }, 0)
    .to(video, { scale: 0.5, ease: 'none', duration: 0.72 }, 0)
    .to(video, { opacity: 0, ease: 'none', duration: 0.28 }, 0.5)   // video shrinks + fades out
    .to(rowTop, { opacity: 1, yPercent: 0, ease: 'none', duration: 0.85 }, 0.22)
    .to(rowBottom, { opacity: 1, yPercent: 0, ease: 'none', duration: 0.85 }, 0.22)
    .to(title, { opacity: 1, y: 0, ease: 'none', duration: 0.4 }, 0.66);
}

/* ---- cinematic hero (mobile / tablet) — same story as desktop in one viewport:
   pin + scrub: headline/video fade, compact 2×2 gallery rows assemble, title in.
   Avoids the commercial-killing long product-card list. ----------------------- */
function initCineHeroMobile() {
  const hero = document.querySelector('[data-cine]');
  if (!hero || reduceMotion || isDesktop()) return;

  const video = hero.querySelector('[data-cine-video]');
  const headline = hero.querySelector('[data-cine-headline]');
  const title = hero.querySelector('[data-cine-title]');
  const rowTop = hero.querySelector('[data-cine-top]');
  const rowBottom = hero.querySelector('[data-cine-bottom]');
  if (!video || !headline || !rowTop || !rowBottom) return;

  gsap.set(video, { scale: 1, opacity: 1, transformOrigin: 'center center' });
  gsap.set(rowTop, { opacity: 0, yPercent: -28 });
  gsap.set(rowBottom, { opacity: 0, yPercent: 28 });
  gsap.set(title, { opacity: 0, y: 14 });
  gsap.set(headline, { opacity: 1, scale: 1 });

  const tl = gsap.timeline({
    scrollTrigger: {
      trigger: hero,
      start: 'top top',
      end: '+=135%',
      pin: true,
      scrub: 0.65,
      anticipatePin: 1,
    },
  });

  tl.to(headline, { opacity: 0, scale: 0.96, ease: 'none', duration: 0.32 }, 0)
    .to(video, { scale: 0.78, ease: 'none', duration: 0.58 }, 0)
    .to(video, { opacity: 0, ease: 'none', duration: 0.26 }, 0.4)
    .to(rowTop, { opacity: 1, yPercent: 0, ease: 'none', duration: 0.72 }, 0.18)
    .to(rowBottom, { opacity: 1, yPercent: 0, ease: 'none', duration: 0.72 }, 0.18)
    .to(title, { opacity: 1, y: 0, ease: 'none', duration: 0.36 }, 0.55);
}

/* ---- products horizontal scroll (home) — pin the section and drive the track
   left→right as the user scrolls on ALL devices; reduced-motion keeps the
   native swipe carousel (CSS fallback). -------------------------------------- */
function initHscroll() {
  const section = document.querySelector('[data-hscroll]');
  if (!section) return;
  const track = section.querySelector('[data-hscroll-track]');
  if (!track || reduceMotion) return;

  const amount = () => Math.max(0, track.scrollWidth - window.innerWidth);
  gsap.to(track, {
    x: () => -amount(),
    ease: 'none',
    scrollTrigger: {
      trigger: section,
      start: 'top top',
      end: () => '+=' + amount(),
      pin: true,
      scrub: isDesktop() ? 1 : 0.85,
      invalidateOnRefresh: true,
      anticipatePin: 1,
    },
  });
}

/* ---- pinned showcase (home) — centre scales down, four others reveal -------
   Contract with the markup:
     [data-showcase]  — section that pins
     .sc-center       — centre tile (starts large, scales to 1)
     .sc-corner       — the four surrounding tiles (fade + scale in)              */
function initShowcase() {
  const pin = document.querySelector('[data-showcase]');
  if (!pin) return;
  const center = pin.querySelector('.sc-center');
  const corners = pin.querySelectorAll('.sc-corner');
  if (!center || reduceMotion || !isDesktop()) return; // static grid otherwise

  gsap.set(center, { scale: 2.9, zIndex: 5, transformOrigin: 'center center' });
  gsap.set(corners, { opacity: 0, scale: 0.7, transformOrigin: 'center center' });

  const tl = gsap.timeline({
    scrollTrigger: {
      trigger: pin,
      start: 'top top',
      end: '+=115%',
      pin: true,
      scrub: 0.6,
      anticipatePin: 1,
    },
  });
  tl.to(center, { scale: 1, ease: 'none' }, 0)
    .to(corners, { opacity: 1, scale: 1, ease: 'none', stagger: 0.06 }, 0.15);
}

/* ---- lifecycle ---------------------------------------------------------- */
function build() {
  if (state.built) return;
  document.documentElement.classList.add('motion');

  state.ctx = gsap.context(() => {
    initHeroDock();
    initCineHero();
    initCineHeroMobile();
    initHscroll();
  });

  initReveals();
  initCountUp();
  initHeader();

  // Re-measure pinned triggers once fonts + images settle, so the pin starts
  // against final layout metrics.
  if (document.fonts && document.fonts.ready) {
    document.fonts.ready.then(() => ScrollTrigger.refresh());
  }

  state.built = true;
}

function destroy() {
  if (state.ctx) { state.ctx.revert(); state.ctx = null; }
  ScrollTrigger.getAll().forEach((t) => t.kill());
  state.observers.forEach((o) => o.disconnect());
  state.observers = [];
  state.cleanups.forEach((fn) => fn());
  state.cleanups = [];
  state.built = false;
}

function init() {
  cacheEnv();
  build();

  // Desktop: hold scroll until fonts + layout settle, then refresh triggers
  // so the pin measures against final metrics (prevents pin-jump, brief §5).
  if (isDesktop() && !reduceMotion && document.querySelector('[data-hero-pin]')) {
    document.body.style.overflow = 'hidden';
    const unlock = () => {
      document.body.style.overflow = '';
      ScrollTrigger.refresh();
    };
    if (document.fonts && document.fonts.ready) {
      document.fonts.ready.then(() => requestAnimationFrame(unlock));
    } else {
      requestAnimationFrame(unlock);
    }
  }
}

/* Rebuild on a significant width change (>20px); ignore mobile URL-bar
   height jitter that fires resize on vertical change only. */
function onResize() {
  const delta = Math.abs(window.innerWidth - state.vw);
  if (delta <= 20) return;
  destroy();
  init();
}

function boot() {
  init();
  let t;
  window.addEventListener('resize', () => {
    clearTimeout(t);
    t = setTimeout(onResize, 200);
  });
}

if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', boot);
} else {
  boot();
}

window.FignocMotion = { init, destroy, refresh: () => ScrollTrigger.refresh() };
