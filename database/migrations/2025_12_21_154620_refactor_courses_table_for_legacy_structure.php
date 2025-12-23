<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Rename content to description using raw SQL
        $driver = DB::getDriverName();
        if ($driver === 'sqlite') {
            // SQLite 3.25.0+ supports RENAME COLUMN
            DB::statement('ALTER TABLE courses RENAME COLUMN content TO description');
        } elseif ($driver === 'mysql') {
            DB::statement('ALTER TABLE courses CHANGE COLUMN content description LONGTEXT NULL');
        } elseif ($driver === 'pgsql') {
            DB::statement('ALTER TABLE courses RENAME COLUMN content TO description');
        } else {
            // Fallback: try SQLite syntax
            DB::statement('ALTER TABLE courses RENAME COLUMN content TO description');
        }

        Schema::table('courses', function (Blueprint $table) {
            // Add new columns
            $table->string('level', 20)->nullable()->after('short_description');
            $table->string('language', 10)->default('fa')->after('level');
            $table->unsignedInteger('students_count')->nullable()->after('language');
            $table->boolean('is_certificate_available')->default(true)->after('students_count');
            $table->string('guarantee_text')->nullable()->after('is_certificate_available');
            $table->string('intro_video_provider', 20)->nullable()->after('guarantee_text');
            $table->string('intro_video_id')->nullable()->after('intro_video_provider');
            $table->json('prerequisites')->nullable()->after('intro_video_id');
            $table->json('target_audience')->nullable()->after('prerequisites');

            // Ensure thumbnail_id exists and is proper foreignId
            if (!Schema::hasColumn('courses', 'thumbnail_id')) {
                $table->foreignId('thumbnail_id')->nullable()->constrained('media')->nullOnDelete()->after('target_audience');
            }
            // Note: If thumbnail_id already exists, we assume it's already set up correctly

            // Drop deprecated columns
            $table->dropColumn(['intro_video_url', 'thumbnail_path']);
        });
    }

    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            // Re-add dropped columns
            $table->string('intro_video_url', 500)->nullable();
            $table->string('thumbnail_path')->nullable();

            // Drop new columns
            $table->dropColumn([
                'level',
                'language',
                'students_count',
                'is_certificate_available',
                'guarantee_text',
                'intro_video_provider',
                'intro_video_id',
                'prerequisites',
                'target_audience',
            ]);
        });

        // Rename description back to content using raw SQL
        $driver = DB::getDriverName();
        if ($driver === 'sqlite') {
            DB::statement('ALTER TABLE courses RENAME COLUMN description TO content');
        } elseif ($driver === 'mysql') {
            DB::statement('ALTER TABLE courses CHANGE COLUMN description content LONGTEXT NULL');
        } elseif ($driver === 'pgsql') {
            DB::statement('ALTER TABLE courses RENAME COLUMN description TO content');
        } else {
            // Fallback: try SQLite syntax
            DB::statement('ALTER TABLE courses RENAME COLUMN description TO content');
        }
    }
};

