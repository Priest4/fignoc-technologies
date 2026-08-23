/**
 * Conversion audit for the /website-design landing page.
 *
 *   node tools/audit-cro.mjs
 *
 * Measures the things landing-page principles are actually about — attention
 * ratio, scroll depth to proof and to price, how many ways off the page there
 * are, form friction, CTA coverage across the scroll — instead of asserting
 * them. Reads the generated prototype, which carries the real markup and CSS.
 */
import fs from 'node:fs';
import path from 'node:path';
import { pathToFileURL } from 'node:url';
import { chromium } from 'playwright';

const root = process.cwd();
const target = pathToFileURL(path.join(root, 'landing-prototype.html')).href;

const VIEWPORTS = [
  { name: 'mobile', width: 390, height: 844, dpr: 3 },
  { name: 'desktop', width: 1440, height: 900, dpr: 2 },
];

const browser = await chromium.launch();

for (const vp of VIEWPORTS) {
  const ctx = await browser.newContext({ viewport: { width: vp.width, height: vp.height }, deviceScaleFactor: vp.dpr });
  const page = await ctx.newPage();
  await page.goto(target, { waitUntil: 'load' });
  await page.waitForTimeout(500);

  const m = await page.evaluate(() => {
    const abs = el => el ? Math.round(el.getBoundingClientRect().top + window.scrollY) : null;
    const h = document.documentElement.scrollHeight;

    // Every clickable thing, split into "advances the sale" vs "leaves".
    const links = [...document.querySelectorAll('a[href]')];
    const external = links.filter(a => /^https?:/.test(a.getAttribute('href')) && !a.href.includes('wa.me'));
    const sameTab = external.filter(a => a.target !== '_blank');
    const wa = links.filter(a => a.href.includes('wa.me'));
    const anchors = links.filter(a => (a.getAttribute('href') || '').startsWith('#'));
    const converters = [...document.querySelectorAll('[data-quote], button[type="submit"]')];

    const sectionOf = el => {
      const s = el.closest('section');
      const hEl = s && s.querySelector('h1, h2');
      return hEl ? hEl.textContent.trim().slice(0, 40) : '(none)';
    };

    // Scroll position of each named milestone.
    const milestone = sel => abs(document.querySelector(sel));

    // Longest stretch of scroll with no way to convert.
    const ctaTops = converters.map(abs).sort((a, b) => a - b);
    let gapPx = 0, gapAfter = 0, prev = 0;
    for (const t of ctaTops) {
      if (t - prev > gapPx) { gapPx = t - prev; gapAfter = prev; }
      prev = t;
    }
    if (h - prev > gapPx) { gapPx = h - prev; gapAfter = prev; }

    const fields = [...document.querySelectorAll('#lp-quote input:not([type=hidden]):not([tabindex="-1"]), #lp-quote select, #lp-quote textarea')];
    const checkFields = [...document.querySelectorAll('#lp-check input:not([type=hidden]):not([tabindex="-1"])')];

    return {
      height: h,
      screens: +(h / window.innerHeight).toFixed(1),
      counts: {
        convertActions: converters.length,
        whatsappLinks: wa.length,
        externalExits: external.length,
        sameTabExits: sameTab.length,
        inPageAnchors: anchors.length,
      },
      exits: external.map(a => ({ href: a.getAttribute('href').replace(/^https?:\/\/(www\.)?/, ''), text: a.textContent.trim().slice(0, 22), section: sectionOf(a) })),
      milestones: {
        h1: milestone('h1'),
        heroCta: milestone('.lp-hero [data-quote]'),
        price: milestone('#pricing'),
        firstProofNumber: milestone('.lp-figs'),
        searchConsoleShot: milestone('.lp-frame'),
        liveWork: milestone('.lp-rail'),
        reviews: milestone('.lp-reviews'),
        guarantee: milestone('.lp-guar'),
        faq: milestone('.lp-faq'),
        closingForm: milestone('#lp-check'),
      },
      biggestCtaGap: { px: gapPx, afterPx: gapAfter },
      forms: {
        dialogVisibleFields: fields.length,
        dialogRequired: fields.filter(f => f.required).length,
        closingFields: checkFields.length,
        closingRequired: checkFields.filter(f => f.required).length,
      },
      wordCount: (document.body.innerText.match(/\S+/g) || []).length,
    };
  });

  const pct = v => v == null ? '  —  ' : (Math.round((v / m.height) * 100) + '%').padStart(5);

  console.log(`\n══ ${vp.name.toUpperCase()}  ${vp.width}x${vp.height}`);
  console.log(`   page ${m.height}px (${m.screens} screens) · ${m.wordCount} words`);
  console.log(`   convert actions ${m.counts.convertActions} · WhatsApp links ${m.counts.whatsappLinks} · ` +
              `off-page links ${m.counts.externalExits} (${m.counts.sameTabExits} same-tab) · anchors ${m.counts.inPageAnchors}`);
  // Only same-tab exits actually lose the visitor; new-tab links leave the
  // landing page loaded behind them.
  console.log(`   attention ratio  ${m.counts.convertActions} : ${m.counts.sameTabExits} (convert : truly leaves)`);
  console.log('\n   scroll depth to each milestone');
  for (const [k, v] of Object.entries(m.milestones)) {
    console.log(`     ${k.padEnd(19)} ${String(v ?? '—').padStart(6)}px  ${pct(v)}`);
  }
  console.log(`\n   longest stretch with no CTA: ${m.biggestCtaGap.px}px ` +
              `(${Math.round((m.biggestCtaGap.px / m.height) * 100)}% of page, starting at ${m.biggestCtaGap.afterPx}px)`);
  console.log(`   dialog: ${m.forms.dialogVisibleFields} fields (${m.forms.dialogRequired} required) · ` +
              `closing form: ${m.forms.closingFields} fields (${m.forms.closingRequired} required)`);

  if (vp.name === 'desktop') {
    console.log('\n   off-page exits:');
    for (const e of m.exits) {
      console.log(`     ${e.text.padEnd(24)} -> ${e.href.padEnd(40)} in "${e.section}"`);
    }
  }

  await ctx.close();
}

await browser.close();

/* Measurement tracking is a landing-page fundamental, and this one sells it. */
const cfg = fs.readFileSync('config/fignoc.php', 'utf8');
const ga4 = /'ga4' => null/.test(cfg) ? 'NOT SET' : 'set';
const gsc = /'search_console_verification' => null/.test(cfg) ? 'NOT SET' : 'set';
const clarity = /clarity/i.test(cfg) ? 'referenced' : 'NOT PRESENT';
console.log('\n══ MEASUREMENT');
console.log(`   GA4 measurement ID          ${ga4}`);
console.log(`   Search Console verification ${gsc}`);
console.log(`   Microsoft Clarity           ${clarity}`);
