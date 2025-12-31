<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // AiTools: logo_id
        if (Schema::hasTable('ai_tools')) {
            Schema::table('ai_tools', function (Blueprint $table) {
                $table->foreignId('logo_id')
                    ->nullable()
                    ->after('logo_path')
                    ->constrained('media')
                    ->nullOnDelete();
            });
        }

        // Courses: thumbnail_id
        if (Schema::hasTable('courses')) {
            Schema::table('courses', function (Blueprint $table) {
                $table->foreignId('thumbnail_id')
                    ->nullable()
                    ->after('thumbnail_path')
                    ->constrained('media')
                    ->nullOnDelete();
            });
        }

        // Posts: thumbnail_id
        if (Schema::hasTable('posts')) {
            Schema::table('posts', function (Blueprint $table) {
                $table->foreignId('thumbnail_id')
                    ->nullable()
                    ->after('thumbnail_path')
                    ->constrained('media')
                    ->nullOnDelete();
            });
        }

        // News: thumbnail_id
        if (Schema::hasTable('news')) {
            Schema::table('news', function (Blueprint $table) {
                $table->foreignId('thumbnail_id')
                    ->nullable()
                    ->after('thumbnail_path')
                    ->constrained('media')
                    ->nullOnDelete();
            });
        }

        // Products: thumbnail_id
        if (Schema::hasTable('products')) {
            Schema::table('products', function (Blueprint $table) {
                $table->foreignId('thumbnail_id')
                    ->nullable()
                    ->after('thumbnail_path')
                    ->constrained('media')
                    ->nullOnDelete();
            });
        }

        // Teachers: avatar_id
        if (Schema::hasTable('teachers')) {
            Schema::table('teachers', function (Blueprint $table) {
                $table->foreignId('avatar_id')
                    ->nullable()
                    ->after('avatar_path')
                    ->constrained('media')
                    ->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('ai_tools')) {
            Schema::table('ai_tools', function (Blueprint $table) {
                $table->dropForeign(['logo_id']);
                $table->dropColumn('logo_id');
            });
        }

        if (Schema::hasTable('courses')) {
            Schema::table('courses', function (Blueprint $table) {
                $table->dropForeign(['thumbnail_id']);
                $table->dropColumn('thumbnail_id');
            });
        }

        if (Schema::hasTable('posts')) {
            Schema::table('posts', function (Blueprint $table) {
                $table->dropForeign(['thumbnail_id']);
                $table->dropColumn('thumbnail_id');
            });
        }

        if (Schema::hasTable('news')) {
            Schema::table('news', function (Blueprint $table) {
                $table->dropForeign(['thumbnail_id']);
                $table->dropColumn('thumbnail_id');
            });
        }

        if (Schema::hasTable('products')) {
            Schema::table('products', function (Blueprint $table) {
                $table->dropForeign(['thumbnail_id']);
                $table->dropColumn('thumbnail_id');
            });
        }

        if (Schema::hasTable('teachers')) {
            Schema::table('teachers', function (Blueprint $table) {
                $table->dropForeign(['avatar_id']);
                $table->dropColumn('avatar_id');
            });
        }
    }
};