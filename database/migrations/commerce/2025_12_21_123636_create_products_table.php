<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->decimal('price', 12, 0)->default(0);
            $table->decimal('sale_price', 12, 0)->nullable();
            $table->text('short_description')->nullable();
            $table->longText('description')->nullable();
            $table->string('sku', 100)->nullable()->unique();
            $table->boolean('is_digital')->default(false);
            $table->string('file_url', 500)->nullable();
            $table->string('thumbnail_path')->nullable();
            $table->string('stock_status', 20)->default('in_stock'); // in_stock, out_of_stock, on_backorder
            $table->unsignedInteger('stock_quantity')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->timestamp('published_at')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->index('published_at');
            $table->index('stock_status');
            $table->index('is_featured');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};