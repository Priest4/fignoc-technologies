/**
 * Capture our live platforms at the exact shape each mockup frame expects.
 *
 *   node tools/capture-devices.mjs
 *
 * Why explicit viewports rather than Playwright's device presets: the iPhone 13
 * preset uses a 664px-tall viewport (it reserves room for browser chrome), so
 * its screenshot comes out at ratio 0.587. The phone frame in the hero is
 * 390:844 (0.462), so `object-fit: cover` was cropping the sides off every
 * mobile capture — the Sign In button and half the search field disappeared.
 *
 * Each pass below matches its frame's aspect ratio exactly, so nothing is
 * cropped and nothing is stretched:
 *
 *   phone   390x844   -> 0.462  == .lp-phone-screen   aspect-ratio 390/844
 *   tablet  768x1024  -> 0.750  == .lp-tablet-screen  aspect-ratio 3/4
 *   laptop  1440x900  -> 1.600  == .lp-laptop-screen  aspect-ratio 16/10
 *
 * Needs network access. Sites that fail are reported and skipped, leaving any
 * existing file in place rather than replacing it with a blank frame.
 */
import fs from 'node:fs';
import path from 'node:path';
import { chromium } from 'playwright';

const SITES = [
  { slug: 'recruitment263', url: 'https://recruitment263.co.zw/' },
  { slug: 'cv263',          url: 'https://www.cv263.co.zw/' },
  { slug: 'nestzim',        url: 'https://www.nestzim.co.zw/' },
  { slug: 'shop263',        url: 'https://www.shop263.co.zw/' },
  { slug: 'nicejob',        url: 'https://www.nicejob.co.zw/' },
  { slug: 'fignoconline',   url: 'https://fignoconline.co.zw/' },
  { slug: 'fignoc',         url: 'https://www.fignoc.co.zw/' },
];

/* Optional slug filter: `node tools/capture-devices.mjs shop263` recaptures one
   site instead of waiting on all of them. */
const only = process.argv.slice(2).filter(a => !a.startsWith('-'));
const TARGETS = only.length ? SITES.filter(s => only.includes(s.slug)) : SITES;

const PASSES = [
  {
    name: 'mobile',
    dir: 'public/images/live/mobile',
    viewport: { width: 390, height: 844 },
    dpr: 3,
    mobile: true,
    ua: 'Mozilla/5.0 (iPhone; CPU iPhone OS 17_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.0 Mobile/15E148 Safari/604.1',
  },
  {
    name: 'tablet',
    dir: 'public/images/live/tablet',
    viewport: { width: 768, height: 1024 },
    dpr: 2,
    mobile: true,
    ua: 'Mozilla/5.0 (iPad; CPU OS 17_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.0 Mobile/15E148 Safari/604.1',
  },
  {
    name: 'laptop',
    dir: 'public/images/live/laptop',
    viewport: { width: 1440, height: 900 },
    dpr: 2,
    mobile: false,
    ua: null,
  },
];

for (const p of PASSES) fs.mkdirSync(p.dir, { recursive: true });

const browser = await chromium.launch();
const results = [];

for (const pass of PASSES) {
  for (const site of TARGETS) {
    const ctx = await browser.newContext({
      viewport: pass.viewport,
      deviceScaleFactor: pass.dpr,
      isMobile: pass.mobile,
      hasTouch: pass.mobile,
      ...(pass.ua ? { userAgent: pass.ua } : {}),
      locale: 'en-ZW',
      timezoneId: 'Africa/Harare',
      reducedMotion: 'reduce',   // freeze carousels and entrance animations
    });
    const page = await ctx.newPage();
    const file = path.join(pass.dir, site.slug + '.jpg');

    try {
      await page.goto(site.url, { waitUntil: 'domcontentloaded', timeout: 45000 });
      await page.waitForLoadState('networkidle', { timeout: 20000 }).catch(() => {});
      await page.waitForTimeout(1800);

      // Consent overlays would dominate a mockup this small: dismiss the common
      // shapes if present, carry on if not.
      await page.evaluate(() => {
        const words = /accept|agree|got it|ok, got|allow|dismiss|close/i;
        document.querySelectorAll('button, a, [role="button"]').forEach(el => {
          const t = (el.textContent || '').trim();
          if (t && t.length < 26 && words.test(t)) {
            const r = el.getBoundingClientRect();
            if (r.width > 40 && r.height > 20) el.click();
          }
        });
      }).catch(() => {});
      await page.waitForTimeout(400);

      // Viewport-only, never fullPage: the frame shows one screen.
      await page.screenshot({ path: file, type: 'jpeg', quality: 86 });

      const kb = (fs.statSync(file).size / 1024).toFixed(0);
      results.push({ pass: pass.name, slug: site.slug, ok: true, note: kb + ' KB' });
    } catch (err) {
      results.push({ pass: pass.name, slug: site.slug, ok: false, note: err.message.slice(0, 64) });
    }

    await ctx.close();
  }
}

await browser.close();

console.log('');
for (const pass of PASSES) {
  const ar = (pass.viewport.width / pass.viewport.height).toFixed(3);
  console.log(`${pass.name} — ${pass.viewport.width}x${pass.viewport.height} @${pass.dpr}x  (ratio ${ar})`);
  console.log('─'.repeat(58));
  for (const r of results.filter(r => r.pass === pass.name)) {
    console.log((r.ok ? '  ok    ' : '  FAIL  ') + r.slug.padEnd(18) + r.note);
  }
  console.log('');
}
console.log('Next: node tools/optimise-images.mjs && node tools/build-proto.mjs');
