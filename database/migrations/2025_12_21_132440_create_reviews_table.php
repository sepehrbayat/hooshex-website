<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->morphs('reviewable'); // AiTool, Course
            $table->unsignedTinyInteger('rating')->min(1)->max(5);
            $table->string('title')->nullable();
            $table->text('body')->nullable();
            $table->enum('status', ['pending', 'approved', 'spam'])->default('pending');
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['user_id', 'reviewable_type', 'reviewable_id']);
            $table->index('status');
            $table->index('rating');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};