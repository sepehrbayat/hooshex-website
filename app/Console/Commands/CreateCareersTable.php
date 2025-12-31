<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Schema\Blueprint;

class CreateCareersTable extends Command
{
    protected $signature = 'careers:create-table';
    
    protected $description = 'Create the careers table if it does not exist';

    public function handle(): int
    {
        $tableExists = Schema::hasTable('careers');
        $actions = [];
        
        if (!$tableExists) {
            $this->info('Creating careers table...');
            
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
            
            $actions[] = 'Created careers table';
            $this->info('✓ Created careers table with initial columns.');
        } else {
            $this->info('Table "careers" already exists. Checking for missing columns...');
        }
        
        // Add additional columns from refactor migration
        $columns = [
            'department' => fn($table) => $table->string('department')->nullable(),
            'work_type' => fn($table) => $table->string('work_type', 20)->nullable(),
            'contract_type' => fn($table) => $table->string('contract_type', 20)->nullable(),
            'salary_range' => fn($table) => $table->string('salary_range')->nullable(),
            'experience_level' => fn($table) => $table->string('experience_level')->nullable(),
            'responsibilities' => fn($table) => $table->json('responsibilities')->nullable(),
            'requirements' => fn($table) => $table->json('requirements')->nullable(),
            'benefits' => fn($table) => $table->json('benefits')->nullable(),
        ];
        
        $addedColumns = [];
        foreach ($columns as $column => $callback) {
            if (!Schema::hasColumn('careers', $column)) {
                try {
                    Schema::table('careers', $callback);
                    $addedColumns[] = $column;
                    $this->info("✓ Added '{$column}' column.");
                } catch (\Exception $e) {
                    $this->warn("⚠ Could not add '{$column}' column: " . $e->getMessage());
                }
            }
        }
        
        if (!empty($addedColumns)) {
            $actions[] = 'Added columns: ' . implode(', ', $addedColumns);
        }
        
        // Add indexes with better error handling
        $indexesAdded = [];
        try {
            Schema::table('careers', function (Blueprint $table) use (&$indexesAdded) {
                if (Schema::hasColumn('careers', 'work_type')) {
                    try {
                        $table->index('work_type');
                        $indexesAdded[] = 'work_type';
                    } catch (\Exception $e) {
                        // Index might already exist
                    }
                }
                if (Schema::hasColumn('careers', 'contract_type')) {
                    try {
                        $table->index('contract_type');
                        $indexesAdded[] = 'contract_type';
                    } catch (\Exception $e) {
                        // Index might already exist
                    }
                }
                if (Schema::hasColumn('careers', 'department')) {
                    try {
                        $table->index('department');
                        $indexesAdded[] = 'department';
                    } catch (\Exception $e) {
                        // Index might already exist
                    }
                }
            });
            
            if (!empty($indexesAdded)) {
                $this->info('✓ Added indexes for: ' . implode(', ', $indexesAdded));
                $actions[] = 'Added indexes';
            }
        } catch (\Exception $e) {
            $this->warn('Note: Some indexes may already exist.');
        }
        
        // Migrate existing 'type' data to 'work_type' if needed
        if (Schema::hasColumn('careers', 'type') && Schema::hasColumn('careers', 'work_type')) {
            try {
                $migrated = DB::statement("UPDATE careers SET work_type = type WHERE work_type IS NULL");
                if ($migrated) {
                    $count = DB::table('careers')->whereNotNull('type')->whereNull('work_type')->count();
                    if ($count > 0) {
                        $this->info("✓ Migrated type data to work_type for {$count} records.");
                        $actions[] = 'Migrated type data';
                    }
                }
            } catch (\Exception $e) {
                $this->warn('Note: Could not migrate type data: ' . $e->getMessage());
            }
        }
        
        // Mark migrations as run in migrations table
        $migrations = [
            '2025_12_21_123641_create_careers_table',
            '2025_12_21_165608_refactor_careers_table_for_job_postings',
        ];
        
        $maxBatch = DB::table('migrations')->max('batch') ?? 0;
        $newBatch = $maxBatch + 1;
        
        foreach ($migrations as $migration) {
            $exists = DB::table('migrations')->where('migration', $migration)->exists();
            if (!$exists) {
                DB::table('migrations')->insert([
                    'migration' => $migration,
                    'batch' => $newBatch,
                ]);
                $this->info("✓ Marked migration as run: {$migration}");
                $actions[] = "Marked migration: {$migration}";
            }
        }
        
        $this->newLine();
        $this->info('✅ Success! The careers table is ready.');
        if (!empty($actions)) {
            $this->info('Actions performed: ' . implode(', ', $actions));
        }
        $this->info('You can now access /admin/careers in Filament panel.');
        
        return self::SUCCESS;
    }
}

