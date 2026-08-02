<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Brief §2/§7: the site grows from flat listings to nested detail pages
 * (services ×8, products ×5, work ×6) that cross-link Work ↔ Products.
 * We add slugs for routing, a status (live | in-dev), cross-link slugs, and a
 * single `detail` JSON column per table for the bespoke long-form page copy.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('services', function (Blueprint $table) {
            if (! Schema::hasColumn('services', 'slug'))        $table->string('slug')->nullable()->unique()->after('name');
            if (! Schema::hasColumn('services', 'category'))    $table->string('category')->nullable()->after('tag');      // Build | Rank | Grow
            if (! Schema::hasColumn('services', 'group_no'))    $table->string('group_no')->nullable()->after('category'); // 01 | 02 | 03
            if (! Schema::hasColumn('services', 'is_featured')) $table->boolean('is_featured')->default(false)->after('group_no');
            if (! Schema::hasColumn('services', 'detail'))      $table->json('detail')->nullable()->after('description');
        });

        Schema::table('products', function (Blueprint $table) {
            if (! Schema::hasColumn('products', 'status'))       $table->string('status')->default('live')->after('tag'); // live | in-dev
            if (! Schema::hasColumn('products', 'headline'))     $table->string('headline')->nullable()->after('description');
            if (! Schema::hasColumn('products', 'who_for'))      $table->json('who_for')->nullable()->after('features');
            if (! Schema::hasColumn('products', 'work_slug'))    $table->string('work_slug')->nullable()->after('who_for');    // cross-link → case study
            if (! Schema::hasColumn('products', 'external_url')) $table->string('external_url')->nullable()->after('work_slug'); // live product URL
            if (! Schema::hasColumn('products', 'detail'))       $table->json('detail')->nullable()->after('external_url');
        });

        Schema::table('portfolio_items', function (Blueprint $table) {
            if (! Schema::hasColumn('portfolio_items', 'slug'))        $table->string('slug')->nullable()->unique()->after('name');
            if (! Schema::hasColumn('portfolio_items', 'status'))      $table->string('status')->default('live')->after('type'); // live | in-dev
            if (! Schema::hasColumn('portfolio_items', 'summary'))     $table->string('summary')->nullable()->after('description');
            if (! Schema::hasColumn('portfolio_items', 'product_slug')) $table->string('product_slug')->nullable()->after('project_url'); // cross-link → product
            if (! Schema::hasColumn('portfolio_items', 'detail'))      $table->json('detail')->nullable()->after('product_slug');
        });
    }

    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->dropColumn(['slug', 'category', 'group_no', 'is_featured', 'detail']);
        });
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['status', 'headline', 'who_for', 'work_slug', 'external_url', 'detail']);
        });
        Schema::table('portfolio_items', function (Blueprint $table) {
            $table->dropColumn(['slug', 'status', 'summary', 'product_slug', 'detail']);
        });
    }
};
