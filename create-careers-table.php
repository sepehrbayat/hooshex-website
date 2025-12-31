<?php

/**
 * Create Careers Table Script
 * 
 * This script creates the careers table directly using Laravel's database connection.
 * Run this with PHP 8.4: php create-careers-table.php
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;

try {
    echo "Creating careers table...\n";
    
    // Check if table already exists
    if (Schema::hasTable('careers')) {
        echo "Table 'careers' already exists. Skipping creation.\n";
        exit(0);
    }
    
    // Create the careers table
    Schema::create('careers', function (Blueprint $table) {
        $table->id();
        $table->string('title');
        $table->string('slug')->unique();
        $table->string('location')->nullable();
        $table->string('type', 20)->default('remote');
        $table->text('short_description')->nullable();
        $table->longText('description')->nullable();
        $table->string('application_link', 500)->nullable();
        $table->boolean('is_active')->default(true);
        $table->timestamp('published_at')->nullable();
        $table->timestamp('expires_at')->nullable();
        $table->timestamps();
        
        $table->index('published_at');
        $table->index('is_active');
        $table->index('type');
    });
    
    echo "✓ Created careers table with initial columns.\n";
    
    // Add additional columns from the refactor migration
    echo "Adding additional columns from refactor migration...\n";
    
    if (!Schema::hasColumn('careers', 'department')) {
        Schema::table('careers', function (Blueprint $table) {
            $table->string('department')->nullable();
        });
        echo "✓ Added 'department' column.\n";
    }
    
    if (!Schema::hasColumn('careers', 'work_type')) {
        Schema::table('careers', function (Blueprint $table) {
            $table->string('work_type', 20)->nullable();
        });
        echo "✓ Added 'work_type' column.\n";
    }
    
    if (!Schema::hasColumn('careers', 'contract_type')) {
        Schema::table('careers', function (Blueprint $table) {
            $table->string('contract_type', 20)->nullable();
        });
        echo "✓ Added 'contract_type' column.\n";
    }
    
    if (!Schema::hasColumn('careers', 'salary_range')) {
        Schema::table('careers', function (Blueprint $table) {
            $table->string('salary_range')->nullable();
        });
        echo "✓ Added 'salary_range' column.\n";
    }
    
    if (!Schema::hasColumn('careers', 'experience_level')) {
        Schema::table('careers', function (Blueprint $table) {
            $table->string('experience_level')->nullable();
        });
        echo "✓ Added 'experience_level' column.\n";
    }
    
    if (!Schema::hasColumn('careers', 'responsibilities')) {
        Schema::table('careers', function (Blueprint $table) {
            $table->json('responsibilities')->nullable();
        });
        echo "✓ Added 'responsibilities' column.\n";
    }
    
    if (!Schema::hasColumn('careers', 'requirements')) {
        Schema::table('careers', function (Blueprint $table) {
            $table->json('requirements')->nullable();
        });
        echo "✓ Added 'requirements' column.\n";
    }
    
    if (!Schema::hasColumn('careers', 'benefits')) {
        Schema::table('careers', function (Blueprint $table) {
            $table->json('benefits')->nullable();
        });
        echo "✓ Added 'benefits' column.\n";
    }
    
    // Add indexes
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
        echo "✓ Added indexes.\n";
    } catch (\Exception $e) {
        echo "Note: Some indexes may already exist (this is okay).\n";
    }
    
    // Mark migrations as run (optional - helps if you run migrations later)
    try {
        $migrations = [
            '2025_12_21_123641_create_careers_table',
            '2025_12_21_165608_refactor_careers_table_for_job_postings',
        ];
        
        $batch = DB::table('migrations')->max('batch') ?? 0;
        $batch++;
        
        foreach ($migrations as $migration) {
            $exists = DB::table('migrations')
                ->where('migration', $migration)
                ->exists();
            
            if (!$exists) {
                DB::table('migrations')->insert([
                    'migration' => $migration,
                    'batch' => $batch++,
                ]);
            }
        }
        
        echo "✓ Marked migrations as run.\n";
    } catch (\Exception $e) {
        echo "Note: Could not mark migrations (this is okay if migrations table doesn't exist yet).\n";
    }
    
    echo "\n✅ Success! The careers table has been created.\n";
    echo "You can now refresh http://localhost:6012/admin/careers\n";
    
} catch (\Exception $e) {
    echo "\n❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
}

