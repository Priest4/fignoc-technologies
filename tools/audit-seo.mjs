/**
 * SEO audit of the live site.
 *
 *   node tools/audit-seo.mjs [https://www.fignoc.co.zw]
 *
 * Crawls the sitemap and checks the things that actually decide whether a page
 * can rank: status, indexability, canonical correctness, title and description
 * length and uniqueness, one H1, structured-data validity, image alts, internal
 * link depth. Reports facts, not a score.
 */
import { chromium } from 'playwright';

const origin = (process.argv[2] || 'https://www.fignoc.co.zw').replace(/\/$/, '');
const browser = await chromium.launch();
const ctx = await browser.newContext({ userAgent: 'FignocSeoAudit/1.0 (+internal)' });
const page = await ctx.newPage();

const findings = [];
const add = (level, url, msg) => findings.push({ level, url, msg });

/* ── robots.txt and sitemap ───────────────────────────────────────────── */
const robotsRes = await ctx.request.get(origin + '/robots.txt').catch(() => null);
const robots = robotsRes && robotsRes.ok() ? await robotsRes.text() : null;
console.log('robots.txt: ' + (robots ? robotsRes.status() + ', ' + robots.split('\n').length + ' lines' : 'MISSING'));
if (!robots) add('WARN', '/robots.txt', 'no robots.txt — crawlers get no sitemap hint and no crawl guidance');
else {
  if (!/sitemap:/i.test(robots)) add('WARN', '/robots.txt', 'does not declare Sitemap:');
  if (/^\s*Disallow:\s*\/\s*$/im.test(robots)) add('FAIL', '/robots.txt', 'Disallow: / blocks the whole site');
}

const smRes = await ctx.request.get(origin + '/sitemap.xml');
const sitemapXml = smRes.ok() ? await smRes.text() : '';
const urls = [...sitemapXml.matchAll(/<loc>([^<]+)<\/loc>/g)].map(m => m[1]);
console.log('sitemap.xml: ' + smRes.status() + ', ' + urls.length + ' URLs\n');
if (!urls.length) { add('FAIL', '/sitemap.xml', 'no URLs found'); }

/* ── Per-page ─────────────────────────────────────────────────────────── */
const seenTitles = new Map();
const seenDescs = new Map();
const rows = [];

for (const url of urls) {
  const res = await page.goto(url, { waitUntil: 'domcontentloaded' }).catch(() => null);
  const status = res ? res.status() : 0;
  const path = url.replace(origin, '') || '/';

  if (status !== 200) {
    add('FAIL', path, 'sitemap lists it but it returns ' + status);
    rows.push({ path, status, title: 0, desc: 0, h1: 0, ld: 0, imgs: 0, noAlt: 0 });
    continue;
  }

  const d = await page.evaluate(() => {
    const meta = n => (document.querySelector(`meta[name="${n}"]`) || {}).content || '';
    const prop = n => (document.querySelector(`meta[property="${n}"]`) || {}).content || '';
    const canon = document.querySelector('link[rel=canonical]');
    const imgs = [...document.images];
    const lds = [...document.querySelectorAll('script[type="application/ld+json"]')];
    let ldTypes = [], ldBroken = 0;
    for (const s of lds) {
      try {
        const j = JSON.parse(s.textContent);
        const walk = o => {
          if (Array.isArray(o)) return o.forEach(walk);
          if (o && typeof o === 'object') {
            if (o['@type']) ldTypes.push(...[].concat(o['@type']));
            if (o['@graph']) walk(o['@graph']);
          }
        };
        walk(j);
      } catch { ldBroken++; }
    }
    return {
      title: document.title,
      desc: meta('description'),
      robots: meta('robots'),
      canonical: canon ? canon.href : null,
      h1s: [...document.querySelectorAll('h1')].map(h => h.textContent.trim()),
      ogTitle: prop('og:title'), ogImage: prop('og:image'),
      ldTypes: [...new Set(ldTypes)], ldBroken,
      imgCount: imgs.length,
      imgNoAlt: imgs.filter(i => !i.hasAttribute('alt')).length,
      internalLinks: [...document.querySelectorAll('a[href]')]
        .map(a => a.href).filter(h => h.startsWith(location.origin)).length,
      words: (document.body.innerText.match(/\S+/g) || []).length,
    };
  });

  rows.push({
    path, status,
    title: d.title.length, desc: d.desc.length,
    h1: d.h1s.length, ld: d.ldTypes.length,
    imgs: d.imgCount, noAlt: d.imgNoAlt, words: d.words,
  });

  if (/noindex/i.test(d.robots)) add('FAIL', path, 'meta robots says noindex but it is in the sitemap');
  if (!d.title) add('FAIL', path, 'no <title>');
  else if (d.title.length > 60) add('WARN', path, `title is ${d.title.length} chars — Google truncates near 60: "${d.title.slice(0, 70)}…"`);
  else if (d.title.length < 15) add('WARN', path, `title is only ${d.title.length} chars`);

  if (!d.desc) add('FAIL', path, 'no meta description');
  else if (d.desc.length > 165) add('WARN', path, `description is ${d.desc.length} chars — truncates near 155`);
  else if (d.desc.length < 70) add('WARN', path, `description is only ${d.desc.length} chars — thin for a snippet`);

  if (d.h1s.length === 0) add('FAIL', path, 'no H1');
  else if (d.h1s.length > 1) add('WARN', path, `${d.h1s.length} H1s`);

  if (!d.canonical) add('WARN', path, 'no canonical');
  else if (d.canonical.replace(/\/$/, '') !== url.replace(/\/$/, '')) {
    add('FAIL', path, `canonical points elsewhere: ${d.canonical}`);
  }

  if (d.ldBroken) add('FAIL', path, `${d.ldBroken} JSON-LD block(s) do not parse`);
  if (!d.ldTypes.length) add('WARN', path, 'no structured data');
  if (!d.ogImage) add('WARN', path, 'no og:image — link previews will be bare');
  if (d.imgNoAlt) add('WARN', path, `${d.imgNoAlt} of ${d.imgCount} images have no alt attribute`);
  if (d.words < 250) add('WARN', path, `only ${d.words} words — thin for a page meant to rank`);

  const tKey = d.title.toLowerCase();
  if (tKey) { seenTitles.set(tKey, [...(seenTitles.get(tKey) || []), path]); }
  const dKey = d.desc.toLowerCase();
  if (dKey) { seenDescs.set(dKey, [...(seenDescs.get(dKey) || []), path]); }
}

for (const [t, paths] of seenTitles) {
  if (paths.length > 1) add('FAIL', paths.join(', '), `duplicate <title>: "${t.slice(0, 60)}"`);
}
for (const [dsc, paths] of seenDescs) {
  if (paths.length > 1) add('WARN', paths.join(', '), `duplicate meta description across ${paths.length} pages`);
}

await browser.close();

/* ── Report ───────────────────────────────────────────────────────────── */
const pad = (s, n) => String(s).padEnd(n);
console.log(pad('PATH', 34) + pad('ST', 5) + pad('TITLE', 7) + pad('DESC', 6) + pad('H1', 4) + pad('LD', 4) + pad('IMG', 5) + pad('NOALT', 7) + 'WORDS');
console.log('-'.repeat(80));
for (const r of rows) {
  console.log(pad(r.path.slice(0, 33), 34) + pad(r.status, 5) + pad(r.title, 7) + pad(r.desc, 6) +
    pad(r.h1, 4) + pad(r.ld, 4) + pad(r.imgs, 5) + pad(r.noAlt, 7) + (r.words ?? '—'));
}

console.log('\n' + '='.repeat(80));
const fails = findings.filter(f => f.level === 'FAIL');
const warns = findings.filter(f => f.level === 'WARN');
for (const f of [...fails, ...warns]) {
  console.log(`[${f.level}] ${f.url}\n        ${f.msg}`);
}
console.log(`\n${fails.length} fail, ${warns.length} warn across ${rows.length} pages`);
