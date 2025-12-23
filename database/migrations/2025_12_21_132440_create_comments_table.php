<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->morphs('commentable'); // Post, Lesson, etc.
            $table->foreignId('parent_id')->nullable()->constrained('comments')->cascadeOnDelete();
            $table->text('body');
            $table->enum('status', ['pending', 'approved', 'spam', 'trash'])->default('pending');
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('status');
            $table->index('commentable_type');
            $table->index('commentable_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('comments');
    }
};