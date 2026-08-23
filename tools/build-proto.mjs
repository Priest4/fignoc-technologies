/**
 * Builds landing-prototype.html — a single review file for /website-design.
 *
 *   node tools/build-proto.mjs
 *
 * It inlines the REAL resources/css/landing.css, the REAL Satoshi font and the
 * REAL page JavaScript, and points at the REAL optimised images, so what you see
 * is what the Blade template renders. The only things restated here are the
 * design tokens the page inherits from app.css and the markup Blade generates
 * from loops.
 *
 * Re-run after editing landing.css, lp-script.blade.php or proto-body.html.
 * Run `node tools/optimise-images.mjs` first if the images changed.
 */
import fs from 'node:fs';
import path from 'node:path';

const HERE = path.dirname(new URL(import.meta.url).pathname.replace(/^\/([A-Za-z]:)/, '$1'));
const root = process.cwd();

const read = p => fs.readFileSync(path.join(root, p), 'utf8');
const dataUri = (p, mime) => `data:${mime};base64,` + fs.readFileSync(path.join(root, p)).toString('base64');

const landingCss = read('resources/css/landing.css');
let body = fs.readFileSync(path.join(HERE, 'proto-body.html'), 'utf8');

/* The page's real JavaScript, lifted out of its Blade wrapper. */
const pageJs = read('resources/views/components/lp-script.blade.php')
  .replace(/\{\{--[\s\S]*?--\}\}/g, '')
  .trim();

/* Icons repeated through the markup, kept as tokens so the body stays readable. */
const icons = {
  '@@TICK@@': '<svg viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 10.5 8 14.5 16 6"/></svg>',
  '@@EXTICON@@': '<svg viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M6 3H3.5v9.5H13V10"/><path d="M9.5 3H13v3.5"/><path d="M7 9l6-6"/></svg>',
  '@@WAICON@@': '<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12.04 2C6.58 2 2.13 6.45 2.13 11.91c0 2.1.55 4.05 1.6 5.77L2 22l4.45-1.17a9.86 9.86 0 0 0 5.59 1.72c5.46 0 9.91-4.45 9.91-9.91 0-2.65-1.03-5.14-2.9-7.01A9.82 9.82 0 0 0 12.04 2Zm4.52 12.99c-.25-.12-1.47-.72-1.69-.81-.23-.08-.39-.12-.56.13-.16.25-.64.8-.78.97-.14.16-.29.18-.54.06-.25-.12-1.05-.39-1.99-1.23-.74-.66-1.23-1.47-1.38-1.72-.14-.25-.01-.38.11-.5.11-.11.25-.29.37-.43.12-.14.16-.25.25-.41.08-.16.04-.31-.02-.43-.06-.12-.56-1.34-.76-1.84-.2-.48-.4-.42-.56-.43h-.47c-.16 0-.43.06-.65.31-.22.25-.86.84-.86 2.05 0 1.21.88 2.38 1 2.54.12.16 1.73 2.64 4.19 3.7.59.25 1.04.4 1.4.51.59.19 1.12.16 1.54.1.47-.07 1.47-.6 1.68-1.18.21-.58.21-1.07.14-1.18-.06-.11-.22-.17-.47-.29Z"/></svg>',
};
for (const [token, svg] of Object.entries(icons)) body = body.split(token).join(svg);

/* ══ Blocks Blade generates from loops ═════════════════════════════════════
   Same data, same order, same alternating sides as the @foreach in
   pages/landing/website.blade.php. */
const CHAPTERS = [
  {
    id: 'fast',
    heading: 'Fast on any network',
    body: ['Most Zimbabwean websites are built on WordPress and loaded with plugins until they crawl. Ours are coded from scratch on Laravel or Django. So the customer on the last dollar of an Econet bundle actually sees your business &mdash; instead of a white screen, and then your competitor.'],
    photo: 'people/afr-man-stairs.jpg',
    alt: 'A customer checking a business website on a phone while out.',
    link: 'web-development',
  },
  {
    id: 'cms',
    heading: 'Change your own prices in two minutes.',
    body: ['Your own content management system, built around how your business works. New price, new service, new photos &mdash; from your phone, in two minutes. No waiting three days for a developer. No $20 invoice to fix a typo.'],
    photo: 'people/afr-woman-laptop.jpg',
    alt: 'A business owner updating her own website on a laptop.',
    link: 'web-systems',
  },
  {
    id: 'see',
    heading: 'You see everything',
    body: [
      'Every website we build ships with Google Search Console, Google Analytics and Microsoft Clarity &mdash; installed, connected and explained.',
      'You&rsquo;ll see the words people typed into Google before they found you. You&rsquo;ll see the visitor who scrolled to your contact form, hesitated, and left. Most business owners are guessing why enquiries are low. You won&rsquo;t have to.',
    ],
    photo: 'people/dev-night.jpg',
    alt: 'Reading site analytics late in the evening.',
    link: 'customer-journey-optimisation',
  },
  {
    id: 'aeo',
    heading: 'Named in AI answers',
    body: [
      'When someone asks ChatGPT or Gemini who does what you do in Harare, your business should be in the answer &mdash; not buried on page two of Google.',
      'We build the structure and markup that makes that possible. It&rsquo;s the same work we do on our own platforms, which is why Recruitment263 gets cited by name.',
    ],
    photo: 'proof/ai-overview.jpg',
    alt: 'A Google AI Overview citing Recruitment263 by name in its answer.',
    link: 'aeo',
  },
  {
    id: 'who',
    heading: 'Websites for shops, services and schools',
    body: ['Retailers, salons, lodges, clinics, driving schools, hardware suppliers, law firms, NGOs. If your customers look you up before they call you, you need this.'],
    photo: 'people/team-office.jpg',
    alt: 'A small team working together in an office.',
    link: 'ecommerce',
  },
];

const PACKAGES = [
  { name: 'Starter', price: 80, list: 150, unit: 'once-off' },
  { name: 'Business', price: 150, list: 300, unit: 'once-off' },
  { name: 'Growth', price: 320, list: 500, unit: 'once-off' },
];
const OFFER = { label: 'Launch offer', ends: '22 October 2026', endsIso: '2026-10-22',
  note: 'Our first sixty days. After that these go back to standard pricing.' };
const BUDGETS = ['Under $150', '$150 – $320', '$320 – $600', '$600 – $1,500', 'Over $1,500', 'Not sure yet'];
const WA = '263786280414';

body = body.replace('  <x-chapters></x-chapters>', CHAPTERS.map((c, i) => `  <!-- ══ ${i + 3} · ${c.heading.toUpperCase()} -->
  <section class="lp-ch lp-ch--split${i % 2 ? ' is-flip' : ''}" id="${c.id}" aria-labelledby="lp-${c.id}-h">
    <div class="lp-wrap">
      <div class="lp-ch-copy">
        <h2 id="lp-${c.id}-h" class="lp-h2">${c.heading}</h2>
        <div class="lp-copy lp-lede">
${c.body.map(p => `          <p>${p}</p>`).join('\n')}
        </div>
        <div class="lp-cta-row">
          <button type="button" class="lp-btn lp-btn--primary" data-quote>Get started</button>
          <a href="https://www.fignoc.co.zw/services/${c.link}" target="_blank" rel="noopener" class="lp-btn lp-btn--quiet">How it works</a>
        </div>
      </div>
      <div class="lp-ch-media">
        <x-img data-key="${c.photo}" data-role="split" data-alt="${c.alt}"></x-img>
      </div>
    </div>
  </section>`).join('\n\n'));

/* The Get-started dialog, mirroring components/lp-quote.blade.php. */
body = body.replace('  <x-quote></x-quote>', `  <dialog class="lp-modal" id="lp-quote" aria-labelledby="lp-quote-h" data-auto-open="false">
    <form method="POST" action="#" class="lp-modal-card" id="lp-quote-form" data-wa="${WA}">
      <div class="lp-modal-head">
        <div>
          <h2 id="lp-quote-h" class="lp-modal-title">Let&rsquo;s scope your website</h2>
          <p class="lp-modal-sub">Three answers is enough to start. We reply within one business day with a straight answer on what fits and what it costs.</p>
        </div>
        <button type="button" class="lp-modal-x" data-quote-close aria-label="Close">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" aria-hidden="true"><path d="M6 6l12 12M18 6L6 18"/></svg>
        </button>
      </div>

      <div class="lp-modal-body">
        <div class="lp-field">
          <label for="q-name">Your name <span aria-hidden="true">*</span></label>
          <input id="q-name" name="name" type="text" required autocomplete="name" maxlength="100">
        </div>
        <div class="lp-field">
          <label for="q-business">Business name <span class="lp-field-opt">optional</span></label>
          <input id="q-business" name="business" type="text" autocomplete="organization" maxlength="120">
        </div>
        <div class="lp-field">
          <label for="q-phone">WhatsApp number <span aria-hidden="true">*</span></label>
          <input id="q-phone" name="phone" type="tel" required autocomplete="tel" inputmode="tel" placeholder="077 000 0000" maxlength="30">
        </div>
        <div class="lp-field">
          <label for="q-email">Email <span class="lp-field-opt">optional</span></label>
          <input id="q-email" name="email" type="email" autocomplete="email" maxlength="150">
        </div>
        <div class="lp-field lp-field--wide">
          <label for="q-website">Website to analyse <span class="lp-field-opt">optional &mdash; leave blank if you don&rsquo;t have one</span></label>
          <input id="q-website" name="website" type="text" inputmode="url" placeholder="yourbusiness.co.zw" maxlength="200">
        </div>
        <div class="lp-field">
          <label for="q-package">What kind of website</label>
          <select id="q-package" name="package">
${PACKAGES.map(p => `            <option value="${p.name}">${p.name} &mdash; $${p.price} ${p.unit}</option>`).join('\n')}
            <option value="Online store">Online store</option>
            <option value="Booking system">Booking system</option>
            <option value="Not sure yet" selected>Not sure yet &mdash; advise me</option>
          </select>
        </div>
        <div class="lp-field">
          <label for="q-budget">Budget <span class="lp-field-opt">optional</span></label>
          <select id="q-budget" name="budget">
            <option value="">Prefer not to say</option>
${BUDGETS.map(b => `            <option value="${b}">${b}</option>`).join('\n')}
          </select>
        </div>
        <div class="lp-field lp-field--wide">
          <label for="q-goal">What do you need it to do? <span aria-hidden="true">*</span></label>
          <textarea id="q-goal" name="goal" rows="3" required minlength="10" maxlength="2000" placeholder="More enquiries, sell online, take bookings, show our prices…"></textarea>
        </div>
        <div class="lp-hp" aria-hidden="true">
          <label for="q-company-url">Company URL</label>
          <input id="q-company-url" name="company_url" type="text" tabindex="-1" autocomplete="off">
        </div>
      </div>

      <div class="lp-modal-foot">
        <button type="submit" class="lp-btn lp-btn--primary">Send enquiry</button>
        <button type="button" class="lp-btn lp-btn--quiet" data-quote-wa>
          ${icons['@@WAICON@@']}
          Send on WhatsApp instead
        </button>
        <p class="lp-modal-note">No spam, no mailing list. We reply once, to the number you gave us.</p>
      </div>
    </form>
  </dialog>`);

/* Guarantee list and FAQ, generated from the same data as the Blade @foreach so
   the prototype cannot drift from the page. */
const GUARANTEES = [
  ['Free consultation and advice.', 'We look at what you have, tell you what you actually need, and quote it. No charge, no obligation &mdash; even if the answer is that you don&rsquo;t need us yet.'],
  ['20% deposit to start.', 'The balance only when you approve the finished site. No full payment upfront, ever.'],
  ['Deposit back if the design isn&rsquo;t right.', 'We show you the design before we build it. If it isn&rsquo;t right, we redo it. If the second version still isn&rsquo;t right, take your deposit and we part as friends.'],
  ['Late is free.', 'If your site isn&rsquo;t live by the date we promised, you don&rsquo;t pay the balance.'],
  ['Fourteen days after launch, anything broken is fixed free.', 'Not &ldquo;support.&rdquo; Fixed.'],
];

const FAQS = [
  ['Why is this more than the $50 websites I&rsquo;ve seen?', 'Those are templates with your logo dropped in. Ours are coded from scratch, load fast on mobile data, and are built so Google and AI engines can read them. A $50 site that brings no enquiries didn&rsquo;t cost you $50 &mdash; it cost you a year of being invisible.'],
  ['What if you take my deposit and disappear?', 'Fair question in this market. The deposit is 20%, the balance is only due once you approve the finished site, and the deposit comes back if the design isn&rsquo;t right. Our own platforms are live at recruitment263.co.zw, cv263.co.zw and nestzim.co.zw &mdash; click any of them. We&rsquo;re not hard to find.'],
  ['Do I own my website?', 'Completely. Code, domain and hosting account, all in your name. You pay the host directly and we put the renewal date in writing, so you are never locked to us.'],
  ['Can I update it myself?', 'Yes. That&rsquo;s what the custom CMS is for, and we record a walkthrough at handover. If you&rsquo;d rather we handled it, that&rsquo;s the Insight Plan.'],
  ['How long does it take?', 'Starter, 7&ndash;10 working days once you&rsquo;ve sent your content. Business, about two weeks. Growth, two to four.'],
  ['Why not WordPress, Wix or Squarespace?', 'You can. WordPress works but ends up loaded with plugins that slow it down and charge licences every year. Wix and Squarespace are slower, generic and harder to rank &mdash; and you never own the result: stop paying and the site disappears. We build on Laravel or Django, and the code is yours.'],
  ['Isn&rsquo;t WhatsApp Business enough?', 'WhatsApp is great for customers who already found you. It can&rsquo;t rank on Google, it can&rsquo;t be cited by ChatGPT, and it can&rsquo;t sell to someone at 11pm.'],
];

const guarHtml = GUARANTEES.map(([title, copy], i) => `          <div class="lp-guar-item">
            <span class="lp-guar-n" aria-hidden="true">${i + 1}</span>
            <div>
              <h3 class="lp-h3">${title}</h3>
              <p>${copy}</p>
            </div>
          </div>`).join('\n');

body = body.replace(
  '        <x-guarantees></x-guarantees>',
  `        <div class="lp-guar-list">\n${guarHtml}\n        </div>`
);

/* Client reviews — the real, client-supplied quotes from config. Quote lengths
   differ a lot (330 / 204 / 136 chars), which is exactly what the card layout
   has to cope with, so they are reproduced verbatim rather than trimmed. */
const REVIEWS = [
  {
    sector: 'NGO',
    quote: 'The old site was WordPress and every year something else wanted paying for &mdash; the gallery plugin, the forms plugin, the one that was supposed to make it faster. And it still took forever to open. Now we have a proper blog section, we put up photos from every workshop ourselves, and it loads. No plugin invoices at all this year.',
    name: 'Tendai Kandeya', role: 'Communications Officer', business: 'local NGO',
  },
  {
    sector: 'Online store',
    quote: 'Everything was in one WhatsApp thread. I&rsquo;d scroll back trying to work out who had actually paid. Paynow and EcoCash went live in week two &mdash; now orders come in overnight and I read them in the morning.',
    name: 'Rachel Ncube', role: 'Shop owner', business: '',
  },
  {
    sector: 'Services',
    quote: 'Search Console showed people searching for something we do but never advertised. We added one page. Two enquiries a month became eleven.',
    name: 'Farai Chikwava', role: 'Marketing Officer', business: 'local service business',
  },
];

const reviewHtml = REVIEWS.map(r => `        <figure class="lp-review">
          <span class="lp-review-chip">${r.sector}</span>
          <blockquote class="lp-review-quote">${r.quote}</blockquote>
          <figcaption class="lp-review-by">
            <b>${r.name}</b>
            ${r.role}${r.business ? ', ' + r.business : ''}
          </figcaption>
        </figure>`).join('\n');

body = body.replace(
  '      <x-reviews></x-reviews>',
  `      <div class="lp-reviews" style="margin-top:1.5rem;">
${reviewHtml}
      </div>`
);

const faqHtml = FAQS.map(([q, a], i) => `        <details${i === 0 ? ' open' : ''}>
          <summary>${q}</summary>
          <div class="lp-faq-a">${a}</div>
        </details>`).join('\n');

body = body.replace(
  '      <x-faqs></x-faqs>',
  `      <div class="lp-faq">\n${faqHtml}\n      </div>`
);


/* The launch-offer band. Mirrors the @if ($offerLive) block in Blade — on the
   real page it disappears by itself once the fixed end date passes. */
body = body.replace('      <x-offer></x-offer>', `      <div class="lp-offer-band">
        <b>${OFFER.label}</b>
        <span>${OFFER.note} Ends <time datetime="${OFFER.endsIso}">${OFFER.ends}</time>.</span>
      </div>`);

/* ══ Responsive images ════════════════════════════════════════════════════
   Same `sizes` strings as the Blade templates, so the prototype exercises the
   real srcset selection rather than one inlined copy. */
const SIZES = {
  hero:   '(min-width: 960px) 896px, 92vw',
  laptop: '(min-width: 1024px) 560px, (min-width: 640px) 88vw, 92vw',
  phone:  '168px',
  split:  '(min-width: 900px) 560px, 92vw',
  card:   '(min-width: 1024px) 560px, 74vw',
};

const manifestPath = path.join(root, 'public/images/opt/manifest.json');
if (!fs.existsSync(manifestPath)) {
  console.error('Missing public/images/opt/manifest.json — run: node tools/optimise-images.mjs');
  process.exit(1);
}
const optManifest = JSON.parse(fs.readFileSync(manifestPath, 'utf8'));

body = body.replace(/<x-img\s([^>]*?)><\/x-img>/g, (whole, attrs) => {
  const attr = n => (attrs.match(new RegExp(`data-${n}="([^"]*)"`)) || [])[1];
  const key = attr('key');
  const role = attr('role');
  const alt = attr('alt') || '';
  const eager = attr('eager') === '1';
  const decorative = attr('decorative') === '1';

  const entry = optManifest[key];
  if (!entry) { console.error('No optimised variants for ' + key); process.exit(1); }
  if (!SIZES[role]) { console.error('No sizes string for role ' + role); process.exit(1); }

  const base = key.replace(/\.[^.]+$/, '').replace(/\//g, '-');
  const set = ext => entry.variants.map(v => `public/images/opt/${base}-${v.w}.${ext} ${v.w}w`).join(', ');
  const largest = entry.variants[entry.variants.length - 1];

  return `<picture>
            <source type="image/webp" srcset="${set('webp')}" sizes="${SIZES[role]}">
            <img src="public/images/opt/${base}-${largest.w}.jpg" srcset="${set('jpg')}"
                 sizes="${SIZES[role]}" width="${entry.width}" height="${entry.height}"
                 alt="${alt}"${decorative ? ' data-decorative' : ''} loading="${eager ? 'eager' : 'lazy'}"${eager ? ' fetchpriority="high"' : ''}
                 decoding="async">
          </picture>`;
});

const stray = body.match(/@@[A-Z0-9]+@@|<x-[a-z]/);
if (stray) { console.error('Unexpanded placeholder: ' + stray[0]); process.exit(1); }

/* ══ Tokens and base styles the page inherits from app.css ════════════════ */
const tokens = `
@font-face {
  font-family: 'Satoshi';
  src: url('${dataUri('public/fonts/satoshi-variable.woff2', 'font/woff2')}') format('woff2-variations'),
       url('${dataUri('public/fonts/satoshi-variable.woff2', 'font/woff2')}') format('woff2');
  font-weight: 300 900;
  font-display: swap;
  font-style: normal;
}
:root {
  --font-sans: 'Satoshi', system-ui, -apple-system, 'Segoe UI', Arial, sans-serif;
  --color-ink: #0B1F4D;
  --color-paper: #FFFFFF;
  --color-accent: #E63946;
  --color-accent-deep: #C81414;
  --color-navy: #002E9A;
  --color-navy-deep: #001F7A;
  --color-navy-mid: #163190;
  --color-navy-sky: #1A45B0;
  --color-brand-tint: #EAF0FB;
  --color-heading: #14213D;
  --color-body: #3F485A;
  --color-muted: #5C6675;
  --color-line: #D1D6DE;
  --color-line-soft: #EAECF0;
  --color-line-strong: #B4BAC6;
}
*, *::before, *::after { box-sizing: border-box; }
html { -webkit-text-size-adjust: 100%; scroll-behavior: smooth; }
html, body { overflow-x: clip; }
body {
  margin: 0;
  font-family: var(--font-sans);
  font-size: 16px;
  line-height: 1.6;
  -webkit-font-smoothing: antialiased;
}
h1, h2, h3, h4 { margin: 0; font-weight: 600; letter-spacing: -0.02em; line-height: 1.1; }
p { margin: 0; }
img { max-width: 100%; height: auto; }
code { font-family: ui-monospace, 'SF Mono', Menlo, monospace; font-size: 0.9em; }
:focus-visible { outline: 2px solid var(--color-accent); outline-offset: 3px; border-radius: 2px; }
`;

const out = `<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex, nofollow">
<title>Fignoc Website Landing</title>
<style>
/* REVIEW PROTOTYPE — generated by tools/build-proto.mjs.
   Do not hand-edit: change resources/css/landing.css and re-run the script. */
${tokens}
${landingCss}
</style>
</head>
<body class="is-bare">
${body}

${pageJs}
</body>
</html>
`;

fs.writeFileSync(path.join(root, 'landing-prototype.html'), out);
console.log('landing-prototype.html  ' + (out.length / 1024).toFixed(0) + ' KB');
