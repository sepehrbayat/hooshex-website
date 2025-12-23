<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ai_tools', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255);
            $table->string('slug', 200)->unique();
            $table->text('short_description')->nullable();
            $table->longText('content')->nullable();
            $table->string('website_url', 500)->nullable();
            $table->string('demo_url', 500)->nullable();
            $table->string('logo_path')->nullable();
            $table->string('pricing_type', 20)->nullable();
            $table->decimal('price', 12, 0)->nullable();
            $table->decimal('rating', 2, 1)->default(0);
            $table->unsignedInteger('users_count')->default(0);
            $table->decimal('success_rate', 5, 2)->nullable();
            $table->string('response_time', 50)->nullable();
            $table->json('languages')->nullable();
            $table->json('features')->nullable();
            $table->string('company', 255)->nullable();
            $table->boolean('is_verified')->default(false);
            $table->boolean('is_featured')->default(false);
            $table->boolean('has_course')->default(false);
            $table->foreignId('related_course_id')->nullable()->constrained('courses')->nullOnDelete();
            $table->timestamp('published_at')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->index('pricing_type');
            $table->index('published_at');
        });

        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('type')->default('ai_tool');
            $table->timestamps();
        });

        Schema::create('categorizables', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->morphs('categorizable');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('categorizables');
        Schema::dropIfExists('categories');
        Schema::dropIfExists('ai_tools');
    }
};

