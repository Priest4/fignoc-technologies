<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Push the rewritten service copy to the live database.
 *
 * The seeder's applyCopyOverrides() reads database/data/service_copy.php, but a
 * seeder only runs on a fresh seed, so live rows keep whatever they were seeded
 * with. This applies the same file to the same rows.
 *
 * It reads the copy file rather than restating it, which is the one place a
 * migration should not be a self-contained snapshot: the file is the source of
 * truth for this copy, and duplicating a thousand words here would guarantee
 * the two drift apart. Re-runnable — it overwrites with whatever the file
 * currently says.
 *
 * Only the slugs listed. The other seven services keep their original copy;
 * rewriting everything in one migration would mean nobody reviewed any of it.
 */
return new class extends Migration
{
    /** Rewritten for search intent — see docs/SEO-KEYWORDS.md. */
    private const SLUGS = [
        'web-development',
        'ecommerce',
        'seo',
        'aeo',
        'geo',
    ];

    public function up(): void
    {
        $path = database_path('data/service_copy.php');

        if (! is_file($path)) {
            return;
        }

        $copy = require $path;

        foreach (self::SLUGS as $slug) {
            if (! isset($copy[$slug])) {
                continue;
            }

            DB::table('services')->where('slug', $slug)->update([
                'description' => $copy[$slug]['description'],
                'detail' => json_encode(
                    $copy[$slug]['detail'],
                    JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
                ),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Deliberately empty. The previous copy is not recorded anywhere to restore
     * from, and reverting would replace pages that answer real search queries
     * with ones that do not. Edit in the admin if any of it reads wrong.
     */
    public function down(): void
    {
        //
    }
};
