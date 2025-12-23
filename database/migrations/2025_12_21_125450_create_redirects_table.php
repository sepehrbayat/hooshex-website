<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('redirects', function (Blueprint $table) {
            $table->id();
            $table->string('source_url')->unique();
            $table->index('source_url');
            $table->string('target_url');
            $table->unsignedSmallInteger('status_code')->default(301); // 301, 302, 307
            $table->unsignedInteger('hit_count')->default(0);
            $table->timestamp('last_accessed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('redirects');
    }
};