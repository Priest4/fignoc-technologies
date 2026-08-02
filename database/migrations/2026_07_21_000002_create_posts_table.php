<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** Insights / blog (brief §7.10) — Markdown body rendered at display time. */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('title');
            $table->string('excerpt')->nullable();
            $table->text('body');                       // Markdown
            $table->string('author')->nullable();
            $table->unsignedSmallInteger('read_minutes')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->boolean('is_published')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
