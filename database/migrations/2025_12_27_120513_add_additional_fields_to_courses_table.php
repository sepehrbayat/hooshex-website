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
        if (Schema::hasTable('courses')) {
            Schema::table('courses', function (Blueprint $table) {
            $table->integer('total_hours')->nullable()->after('duration')->comment('Total course hours');
            $table->integer('total_lessons')->nullable()->after('total_hours')->comment('Total lessons count');
            $table->boolean('has_lifetime_access')->default(true)->after('is_certificate_available');
            $table->boolean('has_practice_files')->default(false)->after('has_lifetime_access');
            $table->string('course_type')->nullable()->after('level')->comment('نوع دوره: ضبط شده، زنده، ترکیبی');
            $table->text('what_you_learn')->nullable()->after('description')->comment('چیزهایی که یاد می‌گیرید - JSON array');
            $table->text('course_requirements')->nullable()->after('what_you_learn')->comment('الزامات دوره - JSON array');
            $table->text('course_includes')->nullable()->after('course_requirements')->comment('این دوره شامل چه چیزهایی است - JSON array');
            $table->dateTime('last_updated_at')->nullable()->after('published_at')->comment('آخرین به‌روزرسانی دوره');
            $table->string('support_type')->nullable()->after('guarantee_text')->comment('نوع پشتیبانی: تلگرام، ایمیل، و...');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('courses')) {
            Schema::table('courses', function (Blueprint $table) {
            $table->dropColumn([
                'total_hours',
                'total_lessons',
                'has_lifetime_access',
                'has_practice_files',
                'course_type',
                'what_you_learn',
                'course_requirements',
                'course_includes',
                'last_updated_at',
                'support_type',
            ]);
            });
        }
    }
};
