<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('author_id')->constrained('users')->cascadeOnDelete();
            $table->string('type', 20)->default('article');
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('excerpt')->nullable();
            $table->longText('content')->nullable();
            $table->string('thumbnail_path')->nullable();
            $table->string('status', 20)->default('draft');
            $table->string('seo_title')->nullable();
            $table->text('seo_description')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->index('published_at');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};

