<?php

namespace App\Support;

/**
 * Responsive source sets for images under /public/images.
 *
 * tools/optimise-images.mjs writes a width ladder (WebP + progressive JPEG)
 * into public/images/opt/ along with manifest.json. This reads that manifest so
 * the markup and the files on disk cannot drift: change the ladder in the
 * script, re-run it, and every <x-lp-img> updates with no template edits.
 *
 * When the optimiser has not been run — a fresh clone, or a newly added
 * screenshot — for() returns null and the component falls back to the
 * untouched original. The page is never broken by a missing variant.
 */
class ImageSet
{
    /** @var array<string, array>|null Manifest, read once per request. */
    protected static ?array $manifest = null;

    public static function manifest(): array
    {
        if (static::$manifest === null) {
            $path = public_path('images/opt/manifest.json');

            static::$manifest = is_file($path)
                ? (json_decode((string) file_get_contents($path), true) ?: [])
                : [];
        }

        return static::$manifest;
    }

    /**
     * Build the srcset pair for an image, keyed by its path relative to
     * /public/images (e.g. 'proof/seo-results.jpg').
     *
     * @return array{webp: string, jpg: string, fallback: string, width: int, height: int}|null
     */
    public static function for(string $src): ?array
    {
        $entry = static::manifest()[$src] ?? null;

        if (! $entry || empty($entry['variants'])) {
            return null;
        }

        $base = str_replace('/', '-', (string) preg_replace('/\.[^.]+$/', '', $src));

        $webp = [];
        $jpg = [];

        foreach ($entry['variants'] as $variant) {
            $w = $variant['w'];
            $webp[] = asset("images/opt/{$base}-{$w}.webp") . " {$w}w";
            $jpg[] = asset("images/opt/{$base}-{$w}.jpg") . " {$w}w";
        }

        $largest = end($entry['variants']);

        return [
            'webp' => implode(', ', $webp),
            'jpg' => implode(', ', $jpg),
            // Widest JPEG rung: the src browsers without srcset support fetch.
            'fallback' => asset("images/opt/{$base}-{$largest['w']}.jpg"),
            // Intrinsic dimensions of the ORIGINAL, so the reserved box has the
            // true aspect ratio and nothing shifts when the image arrives.
            'width' => (int) $entry['width'],
            'height' => (int) $entry['height'],
        ];
    }

    /** Reset the memo. Tests only. */
    public static function flush(): void
    {
        static::$manifest = null;
    }
}
