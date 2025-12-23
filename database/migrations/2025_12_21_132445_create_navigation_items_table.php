<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('navigation_items', function (Blueprint $table) {
            $table->id();
            $table->string('menu_location')->default('header'); // header, footer
            $table->string('label');
            $table->string('url')->nullable();
            $table->string('route')->nullable();
            $table->string('icon')->nullable();
            $table->integer('sort_order')->default(0);
            $table->foreignId('parent_id')->nullable()->constrained('navigation_items')->cascadeOnDelete();
            $table->boolean('is_active')->default(true);
            $table->boolean('open_in_new_tab')->default(false);
            $table->timestamps();

            $table->index('menu_location');
            $table->index('sort_order');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('navigation_items');
    }
};