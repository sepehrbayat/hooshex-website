<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clicks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ai_tool_id')->constrained('ai_tools')->cascadeOnDelete();
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent')->nullable();
            $table->string('referer')->nullable();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamp('clicked_at');
            $table->timestamps();

            $table->index('ai_tool_id');
            $table->index('clicked_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clicks');
    }
};