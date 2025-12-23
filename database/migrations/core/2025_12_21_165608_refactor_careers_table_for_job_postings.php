<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enums\WorkType;
use App\Enums\ContractType;

return new class extends Migration
{
    public function up(): void
    {
        // Add new columns only if they don't exist
        if (!Schema::hasColumn('careers', 'department')) {
            Schema::table('careers', function (Blueprint $table) {
                $table->string('department')->nullable();
            });
        }
        if (!Schema::hasColumn('careers', 'work_type')) {
            Schema::table('careers', function (Blueprint $table) {
                $table->string('work_type', 20)->nullable();
            });
        }
        if (!Schema::hasColumn('careers', 'contract_type')) {
            Schema::table('careers', function (Blueprint $table) {
                $table->string('contract_type', 20)->nullable();
            });
        }
        if (!Schema::hasColumn('careers', 'salary_range')) {
            Schema::table('careers', function (Blueprint $table) {
                $table->string('salary_range')->nullable();
            });
        }
        if (!Schema::hasColumn('careers', 'experience_level')) {
            Schema::table('careers', function (Blueprint $table) {
                $table->string('experience_level')->nullable();
            });
        }
        if (!Schema::hasColumn('careers', 'responsibilities')) {
            Schema::table('careers', function (Blueprint $table) {
                $table->json('responsibilities')->nullable();
            });
        }
        if (!Schema::hasColumn('careers', 'requirements')) {
            Schema::table('careers', function (Blueprint $table) {
                $table->json('requirements')->nullable();
            });
        }
        if (!Schema::hasColumn('careers', 'benefits')) {
            Schema::table('careers', function (Blueprint $table) {
                $table->json('benefits')->nullable();
            });
        }

        // Add indexes (Laravel will handle duplicates gracefully)
        try {
            Schema::table('careers', function (Blueprint $table) {
                if (Schema::hasColumn('careers', 'work_type')) {
                    $table->index('work_type');
                }
                if (Schema::hasColumn('careers', 'contract_type')) {
                    $table->index('contract_type');
                }
                if (Schema::hasColumn('careers', 'department')) {
                    $table->index('department');
                }
            });
        } catch (\Exception $e) {
            // Indexes might already exist, ignore
        }

        // Migrate existing 'type' data to 'work_type' if needed
        // Note: This assumes existing data uses 'remote', 'hybrid', 'on_site' values
        if (Schema::hasColumn('careers', 'type') && Schema::hasColumn('careers', 'work_type')) {
            \DB::statement("UPDATE careers SET work_type = type WHERE work_type IS NULL");
        }

        // SQLite doesn't support dropping columns easily, so we'll leave 'type' column
        // It will be ignored by the model since it's not in fillable
        // For PostgreSQL/MySQL, we can drop it in a separate migration if needed
    }

    public function down(): void
    {
        Schema::table('careers', function (Blueprint $table) {
            // Restore 'type' column
            $table->string('type', 20)->default('remote')->after('location');
            $table->index('type');
        });

        // Migrate work_type back to type
        \DB::statement("UPDATE careers SET type = work_type WHERE type IS NULL");

        Schema::table('careers', function (Blueprint $table) {
            // Drop new columns
            $table->dropColumn([
                'department',
                'work_type',
                'contract_type',
                'salary_range',
                'experience_level',
                'responsibilities',
                'requirements',
                'benefits',
            ]);

            // Drop indexes
            $table->dropIndex(['work_type']);
            $table->dropIndex(['contract_type']);
            $table->dropIndex(['department']);
        });
    }
};
