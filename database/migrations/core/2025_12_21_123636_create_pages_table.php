<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pages', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('excerpt')->nullable();
            $table->json('content_blocks')->nullable(); // Structured landing page data
            $table->string('template', 50)->default('default'); // Template identifier
            $table->boolean('is_published')->default(false);
            $table->timestamp('published_at')->nullable();
            $table->timestamps();

            $table->index('published_at');
            $table->index('is_published');
            $table->index('slug');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pages');
    }
};