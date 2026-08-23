/**
 * Render the main site's hero against the REAL compiled stylesheet.
 *
 *   npm run build && node tools/preview-mainsite.mjs
 *
 * Laravel cannot boot here (no vendor/, no .env), so the only honest way to see
 * whether the colour and texture changes to app.css actually landed is to load
 * the compiled public/build/assets/app-*.css into a page carrying the same
 * markup structure as pages/home.blade.php, and screenshot that.
 *
 * This is a verification harness, not a page we ship. It reproduces the hero's
 * class structure only — no Blade, no data, no JavaScript. The cinematic scroll
 * timeline is driven by motion.js on the real site; without it the hero renders
 * in its no-JS state, which is exactly the state we want to check the colours in.
 *
 * Writes storage/audit/mainsite-hero-{desktop,mobile}.png
 */
import fs from 'node:fs';
import path from 'node:path';
import { pathToFileURL } from 'node:url';
import { chromium } from 'playwright';

const root = process.cwd();
const outDir = path.join(root, 'storage', 'audit');
fs.mkdirSync(outDir, { recursive: true });

const built = fs.readdirSync(path.join(root, 'public/build/assets')).filter(f => /^app-.*\.css$/.test(f));
if (!built.length) {
  console.error('No compiled CSS. Run: npm run build');
  process.exit(1);
}
const cssHref = 'public/build/assets/' + built[0];
console.log('using ' + cssHref);

const cells = [
  'images/live/cv263.jpg', 'images/live/nestzim.jpg',
  'images/live/recruitment263.jpg', 'images/live/shop263.jpg',
];

const html = `<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex, nofollow">
<title>Main site hero — compiled CSS check</title>
<link rel="stylesheet" href="${cssHref}">
</head>
<body class="font-sans antialiased">
<section class="cine-hero">
  <div class="cine-stage">
    <div class="cine-row cine-row--top" aria-hidden="true">
      ${cells.map(c => `<span class="cine-cell"><img src="${c}" alt=""></span>`).join('\n      ')}
    </div>

    <div class="cine-center">
      <div class="cine-video">
        <div class="cine-scrim" aria-hidden="true"></div>
      </div>
      <div class="cine-headline">
        <h1 class="display" style="font-size: clamp(2.5rem, 6.2vw, 4.75rem); max-width: 15ch; margin-inline: auto; line-height: 1.03; color: var(--color-paper);">
          We build software that wins you customers.
        </h1>
        <p class="mt-6 text-lg leading-8" style="max-width: 44ch; margin-inline: auto; color: color-mix(in srgb, var(--color-on-dark) 84%, transparent);">
          Custom software, websites and stores — then the growth that gets you found and paid.
        </p>
        <div class="mt-8" style="display: flex; flex-wrap: wrap; gap: 0.75rem; justify-content: center;">
          <a href="#" class="btn btn-primary">Start a project</a>
          <a href="#" class="btn btn-outline-light">See the work</a>
        </div>
        <div class="mt-7" style="display: flex; flex-wrap: wrap; gap: 0.55rem; justify-content: center;">
          ${['Free consultation', 'You own the code', 'No lock-in', 'Reply within 1 business day']
            .map(t => `<span class="hero-chip">${t}</span>`).join('\n          ')}
        </div>
      </div>
    </div>

    <div class="cine-row cine-row--bottom" aria-hidden="true">
      ${cells.slice().reverse().map(c => `<span class="cine-cell"><img src="${c}" alt=""></span>`).join('\n      ')}
    </div>
  </div>
</section>

<section class="section showcase-grad">
  <div class="container-x">
    <span class="eyebrow">Etched texture on a light ground</span>
    <h2 class="display" style="font-size:2rem;margin-top:1rem;">Products we run</h2>
    <p class="mt-6" style="max-width:44ch;">This section exists to check the light-ground variant of the line texture and the new heading weight.</p>
  </div>
</section>
</body>
</html>
`;

const file = path.join(root, 'mainsite-preview.html');
fs.writeFileSync(file, html);

const browser = await chromium.launch();
for (const vp of [{ n: 'desktop', w: 1440, h: 900 }, { n: 'mobile', w: 390, h: 844 }]) {
  const ctx = await browser.newContext({ viewport: { width: vp.w, height: vp.h }, deviceScaleFactor: 2 });
  const page = await ctx.newPage();
  await page.goto(pathToFileURL(file).href, { waitUntil: 'load' });
  await page.waitForTimeout(600);

  const check = await page.evaluate(() => {
    const hero = document.querySelector('.cine-hero');
    const h1 = document.querySelector('h1');
    const after = getComputedStyle(hero, '::after');
    return {
      heroBackground: getComputedStyle(hero).backgroundImage.slice(0, 90),
      headingWeight: getComputedStyle(h1).fontWeight,
      headingTracking: getComputedStyle(h1).letterSpacing,
      etchedContent: after.content,
      etchedLayers: (after.backgroundImage.match(/gradient/g) || []).length,
    };
  });

  console.log(`\n${vp.n}`);
  console.log('  hero background   ' + check.heroBackground + '…');
  console.log('  h1 weight         ' + check.headingWeight + '   tracking ' + check.headingTracking);
  console.log('  etched ::after    content=' + check.etchedContent + '  gradient layers=' + check.etchedLayers);

  await page.screenshot({ path: path.join(outDir, `mainsite-hero-${vp.n}.png`) });
  await ctx.close();
}
await browser.close();

console.log('\nScreenshots: storage/audit/mainsite-hero-*.png');
console.log('Harness page: mainsite-preview.html (delete when done)');
