/**
 * Build the favicon set from public/favicon.svg.
 *
 *   node tools/build-favicons.mjs
 *
 * public/favicon.ico was a 0-byte file — the layout linked it, so every browser
 * that prefers .ico got nothing. This renders the real sizes from the one SVG
 * source, so the mark can never drift between formats.
 *
 * Writes:
 *   favicon.ico            32x32, PNG-in-ICO (supported since Vista)
 *   favicon-32.png         classic <link rel="icon">
 *   favicon-192.png        Android home screen
 *   apple-touch-icon.png   180x180, iOS home screen (no transparency)
 *   favicon-512.png        PWA / large surfaces
 */
import fs from 'node:fs';
import path from 'node:path';
import sharp from 'sharp';

const pub = 'public';
const src = path.join(pub, 'favicon.svg');
if (!fs.existsSync(src)) { console.error('missing ' + src); process.exit(1); }

const svg = fs.readFileSync(src);
const png = (size) => sharp(svg, { density: 384 }).resize(size, size, { fit: 'contain' }).png({ compressionLevel: 9 });

const outputs = [
  ['favicon-32.png', 32],
  ['favicon-192.png', 192],
  ['favicon-512.png', 512],
];

for (const [name, size] of outputs) {
  await png(size).toFile(path.join(pub, name));
}

/* iOS ignores transparency and composites on black, so this one gets the brand
   navy painted in behind the mark rather than an alpha channel. */
await sharp(svg, { density: 384 })
  .resize(180, 180, { fit: 'contain' })
  .flatten({ background: '#0B1F4D' })
  .png({ compressionLevel: 9 })
  .toFile(path.join(pub, 'apple-touch-icon.png'));

/* A real .ico. The format is a 6-byte header, then one 16-byte directory entry
   per image, then the image payloads. Embedding a PNG keeps this to a few
   hundred bytes instead of hand-rolling a BMP with its upside-down rows and
   AND-mask. */
const icoPng = await png(32).toBuffer();

const header = Buffer.alloc(6);
header.writeUInt16LE(0, 0);   // reserved
header.writeUInt16LE(1, 2);   // type: 1 = icon
header.writeUInt16LE(1, 4);   // image count

const entry = Buffer.alloc(16);
entry.writeUInt8(32, 0);                  // width  (32; 0 would mean 256)
entry.writeUInt8(32, 1);                  // height
entry.writeUInt8(0, 2);                   // palette size — 0 for truecolour
entry.writeUInt8(0, 3);                   // reserved
entry.writeUInt16LE(1, 4);                // colour planes
entry.writeUInt16LE(32, 6);               // bits per pixel
entry.writeUInt32LE(icoPng.length, 8);    // payload size
entry.writeUInt32LE(6 + 16, 12);          // payload offset

fs.writeFileSync(path.join(pub, 'favicon.ico'), Buffer.concat([header, entry, icoPng]));

console.log('favicon set written to public/:');
for (const [name] of outputs) {
  console.log('  ' + name.padEnd(22) + fs.statSync(path.join(pub, name)).size + ' bytes');
}
console.log('  apple-touch-icon.png  ' + fs.statSync(path.join(pub, 'apple-touch-icon.png')).size + ' bytes');
console.log('  favicon.ico           ' + fs.statSync(path.join(pub, 'favicon.ico')).size + ' bytes');
