<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * NiceJob has launched at nicejob.co.zw.
 *
 * The seeder was updated in the same change, but a seeder only runs on a fresh
 * seed — the live database still held status = 'in-dev' and is_coming_soon = 1,
 * so /work/nicejob and /products/nicejob were still badged "Launching soon" and
 * withholding the outbound link. This carries the change to any environment the
 * deploy touches, because deploy.sh already runs migrate.
 *
 * Written against the query builder rather than the models: a data migration
 * should not care about model events, casts or scopes that may change later.
 * Every write is keyed on the slug and safe to run twice.
 */
return new class extends Migration
{
    private const URL = 'https://www.nicejob.co.zw/';

    public function up(): void
    {
        DB::table('products')->where('slug', 'nicejob')->update([
            'status' => 'live',
            'external_url' => self::URL,
            'updated_at' => now(),
        ]);

        $work = DB::table('portfolio_items')->where('slug', 'nicejob')->first();

        if ($work) {
            // 'outcome' still read "In active development — launching soon."
            $detail = json_decode($work->detail ?? '{}', true) ?: [];
            $detail['outcome'] = 'Live at nicejob.co.zw — freelancers and clients dealing directly, '
                . 'with no cut taken on payments.';

            DB::table('portfolio_items')->where('slug', 'nicejob')->update([
                'status' => 'live',
                'is_coming_soon' => false,
                'project_url' => self::URL,
                'detail' => json_encode($detail, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Deliberately empty. Rolling back would re-publish "launching soon" about a
     * product that is demonstrably live, which is worse than an irreversible
     * migration. Correct it in the admin if it ever needs changing.
     */
    public function down(): void
    {
        //
    }
};
