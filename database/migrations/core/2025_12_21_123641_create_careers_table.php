<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('careers', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('location')->nullable();
            $table->string('type', 20)->default('remote'); // remote, hybrid, on_site
            $table->text('short_description')->nullable();
            $table->longText('description')->nullable();
            $table->string('application_link', 500)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamp('published_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();

            $table->index('published_at');
            $table->index('is_active');
            $table->index('type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('careers');
    }
};