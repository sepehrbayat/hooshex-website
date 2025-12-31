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
        if (Schema::hasTable('ai_tools')) {
            Schema::table('ai_tools', function (Blueprint $table) {
                $table->json('gallery_ids')->nullable()->after('logo_id');
                $table->string('deal_url', 500)->nullable()->after('affiliate_url');
                $table->json('pros')->nullable()->after('features');
                $table->json('cons')->nullable()->after('pros');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('ai_tools')) {
            Schema::table('ai_tools', function (Blueprint $table) {
                $table->dropColumn(['gallery_ids', 'deal_url', 'pros', 'cons']);
            });
        }
    }
};
