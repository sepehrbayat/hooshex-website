<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('courses')) {
            Schema::create('courses', function (Blueprint $table) {
                $table->id();
                $table->foreignId('teacher_id')->nullable()->constrained('users')->nullOnDelete();
                $table->string('title');
                $table->string('slug')->unique();
                $table->text('short_description')->nullable();
                $table->longText('content')->nullable();
                $table->decimal('price', 12, 0)->default(0);
                $table->decimal('sale_price', 12, 0)->nullable();
                $table->string('sku', 100)->nullable();
                $table->string('intro_video_url', 500)->nullable();
                $table->string('thumbnail_path')->nullable();
                $table->string('duration', 50)->nullable();
                $table->string('status', 20)->default('draft');
                $table->boolean('is_featured')->default(false);
                $table->string('seo_title')->nullable();
                $table->text('seo_description')->nullable();
                $table->timestamp('published_at')->nullable();
                $table->softDeletes();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('chapters')) {
            Schema::create('chapters', function (Blueprint $table) {
                $table->id();
                $table->foreignId('course_id')->constrained()->cascadeOnDelete();
                $table->string('title');
                $table->unsignedInteger('sort_order')->default(0);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('lessons')) {
            Schema::create('lessons', function (Blueprint $table) {
                $table->id();
                $table->foreignId('chapter_id')->constrained()->cascadeOnDelete();
                $table->string('title');
                $table->string('video_url', 500)->nullable();
                $table->string('duration', 50)->nullable();
                $table->boolean('is_free_preview')->default(false);
                $table->unsignedInteger('sort_order')->default(0);
                $table->longText('content')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('enrollments')) {
            Schema::create('enrollments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->foreignId('course_id')->constrained()->cascadeOnDelete();
                $table->timestamp('enrolled_at')->useCurrent();
                $table->timestamp('expires_at')->nullable();
                $table->unique(['user_id', 'course_id']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('enrollments');
        Schema::dropIfExists('lessons');
        Schema::dropIfExists('chapters');
        Schema::dropIfExists('courses');
    }
};
