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
        Schema::create('not_found_logs', function (Blueprint $table) {
            $table->id();
            $table->string('url');
            $table->string('referer')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent')->nullable();
            $table->unsignedInteger('hit_count')->default(1);
            $table->timestamp('first_seen_at');
            $table->timestamp('last_seen_at');
            $table->timestamps();

            $table->index('url');
            $table->index('last_seen_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('not_found_logs');
    }
};