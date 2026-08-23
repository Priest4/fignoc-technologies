/**
 * Playwright audit for the /website-design landing page.
 *
 *   node tools/audit-landing.mjs
 *
 * Runs against the generated review file (landing-prototype.html), which is
 * byte-identical in CSS and markup to what the Blade template renders. Checks
 * layout integrity, image delivery, accessibility basics, interaction and
 * Core Web Vitals proxies at three viewports, and writes screenshots to
 * storage/audit/.
 */
import fs from 'node:fs';
import path from 'node:path';
import { pathToFileURL } from 'node:url';
import { chromium } from 'playwright';

const root = process.cwd();
const target = pathToFileURL(path.join(root, 'landing-prototype.html')).href;
const outDir = path.join(root, 'storage', 'audit');
fs.mkdirSync(outDir, { recursive: true });

const VIEWPORTS = [
  { name: 'mobile',  width: 390,  height: 844,  dpr: 3, label: 'iPhone-class phone' },
  { name: 'tablet',  width: 820,  height: 1180, dpr: 2, label: 'tablet portrait' },
  { name: 'desktop', width: 1440, height: 900,  dpr: 2, label: 'laptop' },
];

const findings = [];
const add = (level, area, msg) => findings.push({ level, area, msg });

const browser = await chromium.launch();

for (const vp of VIEWPORTS) {
  const ctx = await browser.newContext({
    viewport: { width: vp.width, height: vp.height },
    deviceScaleFactor: vp.dpr,
    reducedMotion: 'no-preference',
  });
  const page = await ctx.newPage();

  const consoleErrors = [];
  page.on('console', m => { if (m.type() === 'error') consoleErrors.push(m.text()); });
  page.on('pageerror', e => consoleErrors.push('pageerror: ' + e.message));

  await page.goto(target, { waitUntil: 'load' });
  await page.waitForTimeout(400);

  console.log(`\n══ ${vp.name.toUpperCase()} — ${vp.width}x${vp.height} @${vp.dpr}x (${vp.label})`);

  if (consoleErrors.length) {
    consoleErrors.forEach(e => add('FAIL', vp.name, 'console error: ' + e));
  }

  /* ── 1. Horizontal overflow ─────────────────────────────────────────── */
  const overflow = await page.evaluate(() => {
    const de = document.documentElement;
    const over = de.scrollWidth - de.clientWidth;
    const culprits = [];
    if (over > 1) {
      document.querySelectorAll('*').forEach(el => {
        const r = el.getBoundingClientRect();
        if (r.width > 0 && (r.right > de.clientWidth + 1 || r.left < -1)) {
          culprits.push({
            tag: el.tagName.toLowerCase(),
            cls: (el.className || '').toString().slice(0, 60),
            right: Math.round(r.right),
            left: Math.round(r.left),
          });
        }
      });
    }
    return { over, culprits: culprits.slice(0, 6) };
  });
  if (overflow.over > 1) {
    add('FAIL', vp.name, `page scrolls sideways by ${overflow.over}px — ` +
      overflow.culprits.map(c => `${c.tag}.${c.cls}@${c.left}..${c.right}`).join(', '));
  } else {
    console.log('  ✓ no horizontal overflow');
  }

  /* ── 2. Image delivery: rendered box vs intrinsic pixels ────────────── */
  // Scroll the full page first, or loading="lazy" images below the fold are
  // still un-fetched and read as failures.
  // Lazy images below the fold are simply un-fetched, which is correct
  // behaviour, not a failure. To measure DELIVERY (srcset choice, intrinsic
  // size, distortion) every image has to be resolved, so force them all eager
  // and wait for decode. Whether lazy-loading is applied is checked separately
  // from the `loading` attribute, which is read before this runs.
  const lazyPlan = await page.evaluate(() => Array.from(document.images).map(im => im.getAttribute('loading')));
  await page.evaluate(async () => {
    document.querySelectorAll('img[loading="lazy"]').forEach(im => { im.loading = 'eager'; });
    await Promise.all(Array.from(document.images).map(im => Promise.race([
      im.decode().catch(() => {}),
      new Promise(r => setTimeout(r, 4000)),
    ])));
  });
  await page.waitForTimeout(250);

  const imgs = await page.evaluate(() => Array.from(document.images).map(im => ({
    src: im.currentSrc || im.src,
    alt: im.alt,
    cssW: Math.round(im.getBoundingClientRect().width),
    cssH: Math.round(im.getBoundingClientRect().height),
    natW: im.naturalWidth,
    natH: im.naturalHeight,
    hasDims: im.hasAttribute('width') && im.hasAttribute('height'),
    loading: null, // filled from lazyPlan below
    fetchpriority: im.getAttribute('fetchpriority'),
    complete: im.complete && im.naturalWidth > 0,
    sizes: im.getAttribute('sizes'),
    srcset: im.getAttribute('srcset'),
    decorative: im.hasAttribute('data-decorative'),
  })));

  for (const [i, im] of imgs.entries()) {
    const id = `img#${i}`;
    if (!im.complete) { add('FAIL', vp.name, `${id} failed to load: ${im.src}`); continue; }
    if (!im.hasDims) add('FAIL', vp.name, `${id} has no width/height attributes — CLS risk`);
    if ((!im.alt || !im.alt.trim()) && !im.decorative) add('WARN', vp.name, `${id} has empty alt but is not marked decorative`);
    if (!im.srcset) add('WARN', vp.name, `${id} has no srcset — every device downloads the same file`);

    /* naturalWidth is DENSITY-CORRECTED once srcset/sizes are in play — for a
       1280px file chosen for a 1440px box it reports 1440, which makes it
       useless for judging sharpness. The real pixel width is in the filename
       of the rung the browser picked (…-1280.webp), so read it from there and
       compare against the device pixels the box actually needs. */
    const rung = (im.src.match(/-(\d+)\.(webp|jpe?g|png)$/) || [])[1];
    if (im.cssW > 0 && rung) {
      const raw = Number(rung);
      const need = im.cssW * vp.dpr;
      if (raw < need * 0.75) {
        add('WARN', vp.name, `${id} serves ${raw}px into a ${im.cssW}px box at ${vp.dpr}x ` +
          `(needs ~${Math.round(need)}px) — soft; source file is too small`);
      } else if (raw > need * 1.4) {
        add('WARN', vp.name, `${id} serves ${raw}px where ${Math.round(need)}px would do ` +
          `— wasted bytes, add a smaller rung`);
      }
    } else if (im.cssW > 0 && !rung) {
      add('WARN', vp.name, `${id} sharpness not measurable: ${im.src} has no width in its name`);
    }
    // Aspect-ratio distortion
    const natAr = im.natW / im.natH, cssAr = im.cssW / im.cssH;
    if (im.cssH > 0 && Math.abs(natAr - cssAr) / natAr > 0.02) {
      const el = imgs[i];
      // object-fit: cover is intentional on the showcase cards; only report
      // when the element has no object-fit.
      const fit = await page.evaluate(n => getComputedStyle(document.images[n]).objectFit, i);
      if (fit === 'fill') {
        add('FAIL', vp.name, `${id} is distorted: ${natAr.toFixed(2)} natural vs ${cssAr.toFixed(2)} rendered`);
      }
    }
  }
  imgs.forEach((im, i) => { im.loading = lazyPlan[i]; });
  const lazyCount = lazyPlan.filter(l => l === 'lazy').length;
  const eagerFirst = lazyPlan[0] !== 'lazy';
  if (!eagerFirst) add('WARN', vp.name, 'the LCP image is lazy-loaded');
  console.log(`  · ${lazyCount}/${imgs.length} images lazy, hero eager: ${eagerFirst}`);
  const loaded = imgs.filter(im => im.complete).length;
  console.log(`  · ${loaded}/${imgs.length} images loaded`);

  /* ── 3. Above-the-fold content ──────────────────────────────────────── */
  const fold = await page.evaluate(h => {
    const inFold = sel => {
      const el = document.querySelector(sel);
      if (!el) return null;
      const r = el.getBoundingClientRect();
      return { top: Math.round(r.top), bottom: Math.round(r.bottom), visible: r.top < h };
    };
    return {
      h1: inFold('h1'),
      cta: inFold('.lp-hero .lp-btn--primary'),
      proof: inFold('.lp-hero-visual .lp-laptop'),
      price: inFold('.lp-hero .lp-price-num'),
    };
  }, vp.height);

  for (const [k, v] of Object.entries(fold)) {
    if (!v) { add('FAIL', vp.name, `${k} not found in hero`); continue; }
    console.log(`  · ${k.padEnd(6)} top=${String(v.top).padStart(5)} ${v.visible ? 'ABOVE fold' : 'below fold'}`);
  }
  if (fold.cta && !fold.cta.visible) {
    add('FAIL', vp.name, `primary WhatsApp CTA starts ${fold.cta.top}px down — below the ${vp.height}px fold`);
  }
  if (fold.proof && !fold.proof.visible) {
    add('WARN', vp.name, `Search Console proof starts ${fold.proof.top}px down — below the fold ` +
      `(build note wants it above on mobile)`);
  }

  /* ── 4. Tap targets ─────────────────────────────────────────────────── */
  const smallTaps = await page.evaluate(() => {
    const out = [];
    document.querySelectorAll('a, button, summary').forEach(el => {
      const r = el.getBoundingClientRect();
      if (r.width === 0 || r.height === 0) return;
      // WCAG 2.5.8 exempts links sitting inline within a sentence — enlarging
      // them would break the line box they live in.
      if (getComputedStyle(el).display === 'inline' && el.closest('p')) return;
      if (r.height < 40) {
        out.push({
          tag: el.tagName.toLowerCase(),
          text: (el.textContent || '').trim().slice(0, 34),
          w: Math.round(r.width), h: Math.round(r.height),
        });
      }
    });
    return out;
  });
  if (smallTaps.length) {
    smallTaps.slice(0, 8).forEach(t =>
      add('WARN', vp.name, `tap target under 40px: <${t.tag}> "${t.text}" is ${t.w}x${t.h}`));
  } else {
    console.log('  ✓ all tap targets >= 40px tall');
  }

  /* ── 5. Interaction: hamburger + FAQ ────────────────────────────────── */
  const burger = page.locator('#lp-burger');
  const menu = page.locator('#lp-menu');
  await burger.click();
  await page.waitForTimeout(250);
  const openState = {
    aria: await burger.getAttribute('aria-expanded'),
    visible: await menu.isVisible(),
    links: await menu.locator('a').count(),
    menuBottom: await menu.evaluate(el => Math.round(el.getBoundingClientRect().bottom)),
  };
  if (openState.aria !== 'true' || !openState.visible) {
    add('FAIL', vp.name, `hamburger did not open (aria-expanded=${openState.aria}, visible=${openState.visible})`);
  } else {
    console.log(`  ✓ hamburger opens: ${openState.links} links, panel ends at ${openState.menuBottom}px`);
  }
  await page.screenshot({ path: path.join(outDir, `${vp.name}-menu-open.png`) });
  await page.keyboard.press('Escape');
  await page.waitForTimeout(200);
  if (await burger.getAttribute('aria-expanded') !== 'false') {
    add('FAIL', vp.name, 'Escape did not close the hamburger menu');
  }

  const faqOpen = page.locator('.lp-faq details[open]');
  if (await faqOpen.count() !== 1) {
    add('WARN', vp.name, `${await faqOpen.count()} FAQ items open by default (expected 1)`);
  }
  const secondSummary = page.locator('.lp-faq details').nth(1).locator('summary');
  await secondSummary.click();
  await page.waitForTimeout(150);
  if (await page.locator('.lp-faq details').nth(1).evaluate(el => el.open) !== true) {
    add('FAIL', vp.name, 'FAQ accordion did not open on click');
  } else {
    console.log('  ✓ FAQ accordion toggles');
  }

  /* ── 6. Focus visibility ────────────────────────────────────────────── */
  // Tab, not .focus() — :focus-visible only engages for keyboard interaction,
  // so a programmatic focus would report a ring that real users never see.
  await page.evaluate(() => window.scrollTo(0, 0));
  await page.locator('.lp-mark').focus();
  let focusRing = null;
  for (let i = 0; i < 8 && !focusRing; i++) {
    await page.keyboard.press('Tab');
    focusRing = await page.evaluate(() => {
      const el = document.activeElement;
      if (!el || !el.matches('a, button, summary')) return null;
      const s = getComputedStyle(el);
      const visible = s.outlineStyle !== 'none' && parseFloat(s.outlineWidth) > 0;
      return {
        on: (el.textContent || '').trim().slice(0, 24),
        outline: `${s.outlineWidth} ${s.outlineStyle} ${s.outlineColor}`,
        visible,
      };
    });
    if (focusRing && !focusRing.visible) focusRing = { ...focusRing, failed: true };
  }
  if (!focusRing || focusRing.failed) {
    add('FAIL', vp.name, `keyboard focus has no visible outline on "${focusRing?.on ?? 'first control'}"`);
  } else {
    console.log(`  ✓ keyboard focus ring on "${focusRing.on}" (${focusRing.outline})`);
  }

  /* ── 7. Layout shift + paint timings ────────────────────────────────── */
  const vitals = await page.evaluate(() => new Promise(resolve => {
    let cls = 0, lcp = 0, lcpEl = '';
    try {
      new PerformanceObserver(list => {
        for (const e of list.getEntries()) if (!e.hadRecentInput) cls += e.value;
      }).observe({ type: 'layout-shift', buffered: true });
      new PerformanceObserver(list => {
        const es = list.getEntries();
        const last = es[es.length - 1];
        if (last) { lcp = last.startTime; lcpEl = last.element ? last.element.tagName + '.' + (last.element.className || '') : ''; }
      }).observe({ type: 'largest-contentful-paint', buffered: true });
    } catch (e) {}
    setTimeout(() => {
      const nav = performance.getEntriesByType('navigation')[0];
      const paint = performance.getEntriesByType('paint');
      resolve({
        cls: Number(cls.toFixed(4)),
        lcp: Math.round(lcp),
        lcpEl: lcpEl.slice(0, 60),
        fcp: Math.round((paint.find(p => p.name === 'first-contentful-paint') || {}).startTime || 0),
        domContentLoaded: Math.round(nav ? nav.domContentLoadedEventEnd : 0),
      });
    }, 1200);
  }));
  console.log(`  · CLS=${vitals.cls}  LCP=${vitals.lcp}ms (${vitals.lcpEl})  FCP=${vitals.fcp}ms`);
  if (vitals.cls > 0.1) add('FAIL', vp.name, `CLS ${vitals.cls} exceeds the 0.1 good threshold`);
  else if (vitals.cls > 0.01) add('WARN', vp.name, `CLS ${vitals.cls} — some shift is happening`);

  /* ── 8. Heading order + landmarks ───────────────────────────────────── */
  const structure = await page.evaluate(() => {
    const hs = Array.from(document.querySelectorAll('h1,h2,h3,h4,h5,h6'))
      .map(h => ({ level: Number(h.tagName[1]), text: h.textContent.trim().slice(0, 50) }));
    return {
      headings: hs,
      h1Count: hs.filter(h => h.level === 1).length,
      main: document.querySelectorAll('main').length,
      lang: document.documentElement.lang,
      title: document.title,
      metaDesc: (document.querySelector('meta[name=description]') || {}).content || null,
      landmarks: {
        header: document.querySelectorAll('header').length,
        nav: document.querySelectorAll('nav').length,
        footer: document.querySelectorAll('footer').length,
      },
    };
  });
  if (structure.h1Count !== 1) add('FAIL', vp.name, `${structure.h1Count} <h1> elements (need exactly 1)`);
  let prev = 0, skips = [];
  for (const h of structure.headings) {
    if (prev && h.level > prev + 1) skips.push(`h${prev} -> h${h.level} at "${h.text}"`);
    prev = h.level;
  }
  if (skips.length) add('WARN', vp.name, 'heading level skipped: ' + skips.join('; '));
  if (vp.name === 'desktop') {
    console.log(`  · ${structure.headings.length} headings, ${structure.h1Count} h1, lang="${structure.lang}"`);
    if (!structure.main) add('WARN', vp.name, 'no <main> landmark');
  }

  /* ── 9. Contrast of the smallest text on each ground ────────────────── */
  const contrast = await page.evaluate(() => {
    const lum = c => {
      const [r, g, b] = c.map(v => { v /= 255; return v <= 0.03928 ? v / 12.92 : Math.pow((v + 0.055) / 1.055, 2.4); });
      return 0.2126 * r + 0.7152 * g + 0.0722 * b;
    };
    // Chromium serialises color-mix() as `color(srgb 0.04 0.09 0.23 / 1)` with
    // 0-1 components, and rgb()/rgba() with 0-255. Reading one as the other
    // reports near-black grounds that aren't there.
    const parse = s => {
      const nums = (s.match(/-?\d*\.?\d+(e-?\d+)?/g) || []).map(Number);
      if (/^color\(/.test(s)) return nums.slice(0, 3).map(v => Math.round(v * 255));
      return nums.slice(0, 3);
    };
    const alphaOf = s => {
      if (/transparent/.test(s)) return 0;
      const m = s.match(/[\/,]\s*(\d*\.?\d+)\s*\)\s*$/);
      // rgb()/color() with three components carry no alpha channel: opaque.
      if (!m) return 1;
      return /^rgba?\(/.test(s) && (s.match(/,/g) || []).length < 3 ? 1 : Number(m[1]);
    };
    // Walk up for the first ground that actually covers what's behind it. A
    // translucent tint (the results slot's 7% red wash) is not that ground.
    const bgOf = el => {
      let n = el;
      while (n && n !== document.documentElement) {
        const bg = getComputedStyle(n).backgroundColor;
        const p = parse(bg);
        if (p.length === 3 && alphaOf(bg) > 0.85) return p;
        n = n.parentElement;
      }
      return [255, 255, 255];
    };
    const ratio = (a, b) => {
      const l1 = lum(a), l2 = lum(b);
      return ((Math.max(l1, l2) + 0.05) / (Math.min(l1, l2) + 0.05));
    };
    const sels = ['.lp-metric-l', '.lp-trust', '.lp-site-desc', '.lp-foot-in', '.lp-tier-tag',
                  '.lp-slot p', '.lp-head-place', '.lp-menu-links a small', '.lp-frame-url'];
    return sels.map(s => {
      const el = document.querySelector(s);
      if (!el) return { sel: s, missing: true };
      const st = getComputedStyle(el);
      return {
        sel: s,
        size: parseFloat(st.fontSize),
        weight: st.fontWeight,
        ratio: Number(ratio(parse(st.color), bgOf(el)).toFixed(2)),
      };
    });
  });
  if (vp.name === 'desktop') {
    for (const c of contrast) {
      if (c.missing) continue;
      const large = c.size >= 24 || (c.size >= 18.66 && Number(c.weight) >= 700);
      const need = large ? 3 : 4.5;
      const ok = c.ratio >= need;
      console.log(`  ${ok ? '✓' : '✗'} ${c.sel.padEnd(24)} ${c.size}px  ratio ${c.ratio} (needs ${need})`);
      if (!ok) add('FAIL', 'contrast', `${c.sel} at ${c.size}px has contrast ${c.ratio}:1, WCAG AA needs ${need}:1`);
    }
  }

  /* ── 10. Full-page screenshot ───────────────────────────────────────── */
  await page.evaluate(() => window.scrollTo(0, 0));
  await page.waitForTimeout(200);
  await page.screenshot({ path: path.join(outDir, `${vp.name}-fold.png`) });
  await page.screenshot({ path: path.join(outDir, `${vp.name}-full.png`), fullPage: true });

  const pageHeight = await page.evaluate(() => document.documentElement.scrollHeight);
  console.log(`  · full page height ${pageHeight}px (${(pageHeight / vp.height).toFixed(1)} screens)`);

  await ctx.close();
}

/* ── Reduced-motion pass ──────────────────────────────────────────────── */
{
  const ctx = await browser.newContext({ viewport: { width: 1440, height: 900 }, reducedMotion: 'reduce' });
  const page = await ctx.newPage();
  await page.goto(target, { waitUntil: 'load' });
  const moves = await page.evaluate(() => {
    const el = document.querySelector('.lp-card');
    const before = el.getBoundingClientRect().top;
    el.dispatchEvent(new MouseEvent('mouseover', { bubbles: true }));
    return { transform: getComputedStyle(el).transform, before: Math.round(before) };
  });
  console.log(`\n══ REDUCED MOTION\n  · .lp-card transform on hover: ${moves.transform}`);
  await page.screenshot({ path: path.join(outDir, 'reduced-motion-fold.png') });
  await ctx.close();
}

await browser.close();

/* ── Report ───────────────────────────────────────────────────────────── */
console.log('\n' + '═'.repeat(78));
console.log('FINDINGS');
console.log('═'.repeat(78));

const dedup = [];
const seen = new Set();
for (const f of findings) {
  const key = f.level + '|' + f.msg;
  if (seen.has(key)) {
    dedup.find(d => d.level + '|' + d.msg === key).areas.push(f.area);
    continue;
  }
  seen.add(key);
  dedup.push({ ...f, areas: [f.area] });
}

const order = { FAIL: 0, WARN: 1 };
dedup.sort((a, b) => order[a.level] - order[b.level]);

if (!dedup.length) {
  console.log('No findings.');
} else {
  for (const f of dedup) {
    console.log(`[${f.level}] (${[...new Set(f.areas)].join(', ')}) ${f.msg}`);
  }
}
console.log('\n' + dedup.filter(f => f.level === 'FAIL').length + ' fail, ' +
  dedup.filter(f => f.level === 'WARN').length + ' warn');
console.log('Screenshots: storage/audit/');
