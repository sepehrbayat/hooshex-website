<?php

declare(strict_types=1);

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
        Schema::table('posts', function (Blueprint $table) {
            $table->integer('reading_time')->nullable()->after('content');
            $table->boolean('is_featured')->default(false)->after('status');
            $table->foreignId('primary_category_id')->nullable()->after('author_id')
                ->constrained('categories')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropForeign(['primary_category_id']);
            $table->dropColumn(['reading_time', 'is_featured', 'primary_category_id']);
        });
    }
};
