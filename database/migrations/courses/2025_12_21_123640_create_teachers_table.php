<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('teachers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('slug')->unique();
            $table->text('bio')->nullable();
            $table->string('specialty')->nullable();
            $table->json('social_links')->nullable(); // {twitter, linkedin, website, etc}
            $table->string('avatar_path')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->timestamp('published_at')->nullable();
            $table->timestamps();

            $table->index('published_at');
            $table->index('is_featured');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('teachers');
    }
};