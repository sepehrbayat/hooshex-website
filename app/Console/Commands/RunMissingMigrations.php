<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Schema\Blueprint;

class RunMissingMigrations extends Command
{
    protected $signature = 'migrate:missing';
    
    protected $description = 'Create missing tables (careers, news, etc.) directly without running full migrations';

    public function handle(): int
    {
        $this->info('Checking for missing tables...');
        
        $created = [];
        $errors = [];
        
        // 1. Create careers table
        if (!Schema::hasTable('careers')) {
            try {
                $this->info('Creating careers table...');
                
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
                
                // Add refactor columns
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
                
                foreach ($columns as $column => $callback) {
                    if (!Schema::hasColumn('careers', $column)) {
                        Schema::table('careers', $callback);
                    }
                }
                
                // Add indexes
                try {
                    Schema::table('careers', function (Blueprint $table) {
                        if (Schema::hasColumn('careers', 'work_type')) {
                            try { $table->index('work_type'); } catch (\Exception $e) {}
                        }
                        if (Schema::hasColumn('careers', 'contract_type')) {
                            try { $table->index('contract_type'); } catch (\Exception $e) {}
                        }
                        if (Schema::hasColumn('careers', 'department')) {
                            try { $table->index('department'); } catch (\Exception $e) {}
                        }
                    });
                } catch (\Exception $e) {}
                
                $created[] = 'careers';
                $this->info('✓ Created careers table');
            } catch (\Exception $e) {
                $errors[] = "careers: " . $e->getMessage();
                $this->error('✗ Failed to create careers: ' . $e->getMessage());
            }
        } else {
            $this->info('⊘ careers table already exists');
        }
        
        // 2. Create news table
        if (!Schema::hasTable('news')) {
            try {
                $this->info('Creating news table...');
                
                Schema::create('news', function (Blueprint $table) {
                    $table->id();
                    $table->foreignId('author_id')->constrained('users')->cascadeOnDelete();
                    $table->string('title');
                    $table->string('slug')->unique();
                    $table->text('excerpt')->nullable();
                    $table->longText('content')->nullable();
                    $table->string('thumbnail_path')->nullable();
                    $table->string('status', 20)->default('draft');
                    $table->timestamp('published_at')->nullable();
                    $table->softDeletes();
                    $table->timestamps();
                    
                    $table->index('published_at');
                    $table->index('status');
                });
                
                // Add thumbnail_id
                if (!Schema::hasColumn('news', 'thumbnail_id')) {
                    if (Schema::hasTable('media')) {
                        try {
                            Schema::table('news', function (Blueprint $table) {
                                $table->foreignId('thumbnail_id')->nullable()->after('thumbnail_path')->constrained('media')->nullOnDelete();
                            });
                        } catch (\Exception $e) {
                            Schema::table('news', function (Blueprint $table) {
                                $table->unsignedBigInteger('thumbnail_id')->nullable()->after('thumbnail_path');
                            });
                        }
                    } else {
                        Schema::table('news', function (Blueprint $table) {
                            $table->unsignedBigInteger('thumbnail_id')->nullable()->after('thumbnail_path');
                        });
                    }
                }
                
                $created[] = 'news';
                $this->info('✓ Created news table');
            } catch (\Exception $e) {
                $errors[] = "news: " . $e->getMessage();
                $this->error('✗ Failed to create news: ' . $e->getMessage());
            }
        } else {
            $this->info('⊘ news table already exists');
        }
        
        // 3. Create pages table
        if (!Schema::hasTable('pages')) {
            try {
                $this->info('Creating pages table...');
                
                Schema::create('pages', function (Blueprint $table) {
                    $table->id();
                    $table->string('title');
                    $table->string('slug')->unique();
                    $table->text('excerpt')->nullable();
                    $table->json('content_blocks')->nullable(); // Structured landing page data
                    $table->string('template', 50)->default('default'); // Template identifier
                    $table->boolean('is_published')->default(false);
                    $table->timestamp('published_at')->nullable();
                    $table->timestamps();
                    
                    $table->index('published_at');
                    $table->index('is_published');
                    $table->index('slug');
                });
                
                $created[] = 'pages';
                $this->info('✓ Created pages table');
            } catch (\Exception $e) {
                $errors[] = "pages: " . $e->getMessage();
                $this->error('✗ Failed to create pages: ' . $e->getMessage());
            }
        } else {
            $this->info('⊘ pages table already exists');
        }
        
        // 4. Create products table
        if (!Schema::hasTable('products')) {
            try {
                $this->info('Creating products table...');
                
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
                    $table->string('stock_status', 20)->default('in_stock');
                    $table->unsignedInteger('stock_quantity')->nullable();
                    $table->boolean('is_featured')->default(false);
                    $table->timestamp('published_at')->nullable();
                    $table->softDeletes();
                    $table->timestamps();
                    
                    $table->index('published_at');
                    $table->index('stock_status');
                    $table->index('is_featured');
                });
                
                // Add thumbnail_id
                if (!Schema::hasColumn('products', 'thumbnail_id')) {
                    if (Schema::hasTable('media')) {
                        try {
                            Schema::table('products', function (Blueprint $table) {
                                $table->foreignId('thumbnail_id')->nullable()->after('thumbnail_path')->constrained('media')->nullOnDelete();
                            });
                        } catch (\Exception $e) {
                            Schema::table('products', function (Blueprint $table) {
                                $table->unsignedBigInteger('thumbnail_id')->nullable()->after('thumbnail_path');
                            });
                        }
                    } else {
                        Schema::table('products', function (Blueprint $table) {
                            $table->unsignedBigInteger('thumbnail_id')->nullable()->after('thumbnail_path');
                        });
                    }
                }
                
                $created[] = 'products';
                $this->info('✓ Created products table');
            } catch (\Exception $e) {
                $errors[] = "products: " . $e->getMessage();
                $this->error('✗ Failed to create products: ' . $e->getMessage());
            }
        } else {
            $this->info('⊘ products table already exists');
        }
        
        // 5. Create teachers table
        if (!Schema::hasTable('teachers')) {
            try {
                $this->info('Creating teachers table...');
                
                Schema::create('teachers', function (Blueprint $table) {
                    $table->id();
                    $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
                    $table->string('slug')->unique();
                    $table->text('bio')->nullable();
                    $table->string('specialty')->nullable();
                    $table->json('social_links')->nullable();
                    $table->string('avatar_path')->nullable();
                    $table->boolean('is_featured')->default(false);
                    $table->timestamp('published_at')->nullable();
                    $table->timestamps();
                    
                    $table->index('published_at');
                    $table->index('is_featured');
                });
                
                // Add avatar_id
                if (!Schema::hasColumn('teachers', 'avatar_id')) {
                    if (Schema::hasTable('media')) {
                        try {
                            Schema::table('teachers', function (Blueprint $table) {
                                $table->foreignId('avatar_id')->nullable()->after('avatar_path')->constrained('media')->nullOnDelete();
                            });
                        } catch (\Exception $e) {
                            Schema::table('teachers', function (Blueprint $table) {
                                $table->unsignedBigInteger('avatar_id')->nullable()->after('avatar_path');
                            });
                        }
                    } else {
                        Schema::table('teachers', function (Blueprint $table) {
                            $table->unsignedBigInteger('avatar_id')->nullable()->after('avatar_path');
                        });
                    }
                }
                
                $created[] = 'teachers';
                $this->info('✓ Created teachers table');
            } catch (\Exception $e) {
                $errors[] = "teachers: " . $e->getMessage();
                $this->error('✗ Failed to create teachers: ' . $e->getMessage());
            }
        } else {
            $this->info('⊘ teachers table already exists');
        }
        
        // Mark migrations as run
        $migrations = [
            '2025_12_21_123641_create_careers_table',
            '2025_12_21_165608_refactor_careers_table_for_job_postings',
            '2025_12_21_123637_create_news_table',
            '2025_12_21_123636_create_pages_table',
            '2025_12_21_123636_create_products_table',
            '2025_12_21_123640_create_teachers_table',
        ];
        
        // Add curator migration if any table has media fields
        if ((Schema::hasTable('news') && Schema::hasColumn('news', 'thumbnail_id')) ||
            (Schema::hasTable('products') && Schema::hasColumn('products', 'thumbnail_id')) ||
            (Schema::hasTable('teachers') && Schema::hasColumn('teachers', 'avatar_id'))) {
            $migrations[] = '2025_12_21_140740_add_curator_media_fields_to_models';
        }
        
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
            }
        }
        
        $this->newLine();
        if (!empty($created)) {
            $this->info('✅ Successfully created: ' . implode(', ', $created));
        }
        
        if (!empty($errors)) {
            $this->error('Errors occurred:');
            foreach ($errors as $error) {
                $this->error('  - ' . $error);
            }
            return self::FAILURE;
        }
        
        $this->info('✅ All migrations completed successfully!');
        return self::SUCCESS;
    }
}

