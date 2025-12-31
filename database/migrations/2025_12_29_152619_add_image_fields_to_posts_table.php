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
        Schema::table('posts', function (Blueprint $table) {
            $table->text('image_alt_text')->nullable()->after('thumbnail_id');
            $table->string('image_title')->nullable()->after('image_alt_text');
            $table->text('image_caption')->nullable()->after('image_title');
            $table->text('image_description')->nullable()->after('image_caption');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropColumn([
                'image_alt_text',
                'image_title', 
                'image_caption',
                'image_description'
            ]);
        });
    }
};
