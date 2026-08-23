/**
 * Responsive image build for the landing page.
 *
 *   node tools/optimise-images.mjs
 *
 * The audit found the page shipping 1280–1442px originals into 348–540px
 * boxes: about 450 KB of pure waste on a phone, on a page whose own copy
 * complains about slow sites on Econet data. This generates a width ladder in
 * WebP (with a JPEG fallback) for every landing-page image, writing to
 * public/images/opt/ and leaving the originals untouched.
 *
 * Output naming: <name>-<width>.webp / <name>-<width>.jpg
 * The Blade template builds srcset from the same LADDER constant below, so the
 * markup and the files on disk can never drift.
 */
import fs from 'node:fs';
import path from 'node:path';
import sharp from 'sharp';

const SRC_DIR = 'public/images';
const OUT_DIR = 'public/images/opt';

/* Widths per role, chosen from the boxes the audit actually measured:
   hero frame  429px (mobile) → 540px (desktop)  → 2x need ≈ 1080
   showcase    348px (mobile) → 380px (tablet)   → 2x need ≈  760
   feature     full-bleed card up to ~1090px     → 2x need capped at source */
/* Two ladders is all the page needs now that every image is CONTAINED rather
   than full-bleed. Widest real box is the Search Console frame (~896 CSS px);
   everything else — laptop screen, chapter media, carousel cards, the phone
   screen — sits at 560 CSS px or below, so one shared ladder covers them at 2x
   without shipping a 1280px file to a 156px phone mockup. */
const LADDER = {
  hero:    [480, 720, 1080, 1440],
  content: [320, 560, 840, 1120, 1280],
  // Phone mockup screen ~190 CSS px, tablet ~230 CSS px: covered at 3x.
  phone:   [320, 480, 600],
  tablet:  [320, 480, 720],
};

const JOBS = [
  // Block 8 — the Search Console screenshot, presented large.
  { src: 'proof/seo-results.jpg',   role: 'hero' },

  // Carousel cards. These are the 1440x900 laptop captures, which match the
  // 16:10 card frame exactly — the old 1900x900 desktop shots were cropped.
  { src: 'live/laptop/fignoconline.jpg',   role: 'content' },
  { src: 'live/laptop/recruitment263.jpg', role: 'content' },
  { src: 'live/laptop/cv263.jpg',          role: 'content' },
  { src: 'live/laptop/nestzim.jpg',        role: 'content' },
  { src: 'live/laptop/shop263.jpg',        role: 'content' },
  { src: 'live/laptop/nicejob.jpg',        role: 'content' },

  // Hero phones — real 390x844 mobile renders.
  { src: 'live/mobile/recruitment263.jpg', role: 'phone' },
  { src: 'live/mobile/shop263.jpg',        role: 'phone' },

  // Chapter media (3–7).
  { src: 'people/afr-man-stairs.jpg',   role: 'content' },
  { src: 'people/afr-woman-laptop.jpg', role: 'content' },
  { src: 'people/dev-night.jpg',        role: 'content' },
  { src: 'proof/ai-overview.jpg',       role: 'content' },
  { src: 'people/team-office.jpg',      role: 'content' },
];

fs.mkdirSync(OUT_DIR, { recursive: true });

const kb = b => (b / 1024).toFixed(0) + ' KB';
let originalTotal = 0, optimisedMobileTotal = 0;
const manifest = {};

for (const job of JOBS) {
  const srcPath = path.join(SRC_DIR, job.src);
  if (!fs.existsSync(srcPath)) {
    if (!job.optional) console.log(`MISSING  ${job.src}`);
    else console.log(`skipped  ${job.src} (no screenshot yet)`);
    continue;
  }

  const meta = await sharp(srcPath).metadata();
  const srcBytes = fs.statSync(srcPath).size;
  originalTotal += srcBytes;

  const base = job.src.replace(/\.[^.]+$/, '').replace(/\//g, '-');
  // Never upscale: a 940px source gets no 1280px variant.
  const widths = LADDER[job.role].filter(w => w <= meta.width);
  if (!widths.length) widths.push(meta.width);

  console.log(`\n${job.src}  ${meta.width}x${meta.height}  ${kb(srcBytes)}  (${job.role})`);
  manifest[job.src] = { role: job.role, width: meta.width, height: meta.height, variants: [] };

  for (const w of widths) {
    const h = Math.round((meta.height / meta.width) * w);
    const webpPath = path.join(OUT_DIR, `${base}-${w}.webp`);
    const jpgPath  = path.join(OUT_DIR, `${base}-${w}.jpg`);

    await sharp(srcPath)
      .resize({ width: w, withoutEnlargement: true })
      .webp({ quality: 78, effort: 6 })
      .toFile(webpPath);

    await sharp(srcPath)
      .resize({ width: w, withoutEnlargement: true })
      // Progressive: the image paints top-down instead of holding a blank box
      // on a slow connection. mozjpeg trims another few percent.
      .jpeg({ quality: 80, progressive: true, mozjpeg: true })
      .toFile(jpgPath);

    const wb = fs.statSync(webpPath).size, jb = fs.statSync(jpgPath).size;
    manifest[job.src].variants.push({ w, h, webp: wb, jpg: jb });
    console.log(`  ${String(w).padStart(4)}px  webp ${kb(wb).padStart(7)}   jpg ${kb(jb).padStart(7)}`);
    if (w === widths[0]) optimisedMobileTotal += wb;
  }
}

fs.writeFileSync(path.join(OUT_DIR, 'manifest.json'), JSON.stringify(manifest, null, 2));

console.log('\n' + '─'.repeat(64));
console.log(`Originals shipped to every device : ${kb(originalTotal)}`);
console.log(`Smallest WebP rung (phone)        : ${kb(optimisedMobileTotal)}`);
console.log(`Saved on a phone                  : ${kb(originalTotal - optimisedMobileTotal)} ` +
  `(${Math.round((1 - optimisedMobileTotal / originalTotal) * 100)}% less)`);
console.log(`\nWritten to ${OUT_DIR}/ · originals untouched`);
