<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Drop existing settings table if it has wrong structure
        Schema::dropIfExists('settings');
        
        // Create settings table with correct structure for Spatie Laravel Settings
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('group');
            $table->string('name');
            $table->text('payload')->nullable();
            $table->timestamps();
            
            $table->unique(['group', 'name']);
            $table->index('group');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
