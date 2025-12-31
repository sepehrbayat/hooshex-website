<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\OtpController;
use App\Http\Controllers\Payments\PaymentController;
use App\Http\Livewire\Home;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

Route::get('/', Home::class)->name('home');

Route::get('/login', fn () => redirect()->route('home'))->name('login');

Route::get('/dashboard', fn () => redirect()->route('home'))->name('dashboard');

Route::view('/ai-tools', 'ai-tools.index')->name('ai-tools.index');
Route::get('/ai-tools/{slug}', [App\Http\Controllers\AiTools\AiToolController::class, 'show'])->name('ai-tools.show');
Route::get('/posts/{slug}', [App\Http\Controllers\Blog\PostController::class, 'show'])->name('posts.show');
Route::get('/courses/{slug}', [App\Http\Controllers\Courses\CourseController::class, 'show'])->name('courses.show');
Route::get('/careers/{slug}', [App\Http\Controllers\Careers\CareerController::class, 'show'])->name('careers.show');

Route::prefix('auth/otp')->middleware('throttle:10,1')->group(function () {
    Route::post('request', [OtpController::class, 'requestOtp'])->name('otp.request');
    Route::post('verify', [OtpController::class, 'verifyOtp'])->name('otp.verify');
});

// Payment Routes
Route::get('/payment/callback', [PaymentController::class, 'callback'])->name('payment.callback');
Route::post('/payment/callback', [PaymentController::class, 'callback']); // Some gateways POST
Route::middleware('auth')->group(function () {
    Route::post('/payment/checkout', [PaymentController::class, 'checkout'])->name('payment.checkout');
    Route::get('/payment/status/{order}', [PaymentController::class, 'status'])->name('payment.status');
    Route::post('/payment/retry/{order}', [PaymentController::class, 'retry'])->name('payment.retry');
});

// Click Tracking
Route::get('/go/{slug}', [App\Http\Controllers\Core\ClickController::class, 'go'])->name('click.track');

// App Panel Routes (Invoice Download)
Route::middleware(['auth'])->group(function () {
    Route::get('/app/invoice/{order}', [App\Http\Controllers\App\InvoiceController::class, 'download'])
        ->name('app.invoice.download');
});

Route::post('/logout', function (Request $request) {
    Auth::logout();

    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect()->route('home');
})->name('logout');

// Temporary route to run missing migrations (admin only, one-time use)
Route::get('/admin/run-missing-migrations', function () {
    if (!Auth::check() || !Auth::user()->isAdmin()) {
        abort(403, 'Unauthorized');
    }
    
    $actions = [];
    $errors = [];
    $created = [];
    $skipped = [];
    
    try {
        // 1. Create careers table
        if (!\Illuminate\Support\Facades\Schema::hasTable('careers')) {
            \Illuminate\Support\Facades\Schema::create('careers', function (\Illuminate\Database\Schema\Blueprint $table) {
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
                if (!\Illuminate\Support\Facades\Schema::hasColumn('careers', $column)) {
                    \Illuminate\Support\Facades\Schema::table('careers', $callback);
                }
            }
            
            // Add indexes
            try {
                \Illuminate\Support\Facades\Schema::table('careers', function (\Illuminate\Database\Schema\Blueprint $table) {
                    if (\Illuminate\Support\Facades\Schema::hasColumn('careers', 'work_type')) {
                        try { $table->index('work_type'); } catch (\Exception $e) {}
                    }
                    if (\Illuminate\Support\Facades\Schema::hasColumn('careers', 'contract_type')) {
                        try { $table->index('contract_type'); } catch (\Exception $e) {}
                    }
                    if (\Illuminate\Support\Facades\Schema::hasColumn('careers', 'department')) {
                        try { $table->index('department'); } catch (\Exception $e) {}
                    }
                });
            } catch (\Exception $e) {}
            
            $created[] = 'careers';
            $actions[] = 'Created careers table';
        } else {
            $skipped[] = 'careers';
        }
        
        // 2. Create news table
        if (!\Illuminate\Support\Facades\Schema::hasTable('news')) {
            \Illuminate\Support\Facades\Schema::create('news', function (\Illuminate\Database\Schema\Blueprint $table) {
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
            if (!\Illuminate\Support\Facades\Schema::hasColumn('news', 'thumbnail_id')) {
                if (\Illuminate\Support\Facades\Schema::hasTable('media')) {
                    try {
                        \Illuminate\Support\Facades\Schema::table('news', function (\Illuminate\Database\Schema\Blueprint $table) {
                            $table->foreignId('thumbnail_id')->nullable()->after('thumbnail_path')->constrained('media')->nullOnDelete();
                        });
                    } catch (\Exception $e) {
                        \Illuminate\Support\Facades\Schema::table('news', function (\Illuminate\Database\Schema\Blueprint $table) {
                            $table->unsignedBigInteger('thumbnail_id')->nullable()->after('thumbnail_path');
                        });
                    }
                } else {
                    \Illuminate\Support\Facades\Schema::table('news', function (\Illuminate\Database\Schema\Blueprint $table) {
                        $table->unsignedBigInteger('thumbnail_id')->nullable()->after('thumbnail_path');
                    });
                }
            }
            
            $created[] = 'news';
            $actions[] = 'Created news table';
        } else {
            $skipped[] = 'news';
        }
        
        // 3. Create pages table
        if (!\Illuminate\Support\Facades\Schema::hasTable('pages')) {
            \Illuminate\Support\Facades\Schema::create('pages', function (\Illuminate\Database\Schema\Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->string('slug')->unique();
                $table->text('excerpt')->nullable();
                $table->json('content_blocks')->nullable();
                $table->string('template', 50)->default('default');
                $table->boolean('is_published')->default(false);
                $table->timestamp('published_at')->nullable();
                $table->timestamps();
                
                $table->index('published_at');
                $table->index('is_published');
                $table->index('slug');
            });
            
            $created[] = 'pages';
            $actions[] = 'Created pages table';
        } else {
            $skipped[] = 'pages';
        }
        
        // 4. Create products table
        if (!\Illuminate\Support\Facades\Schema::hasTable('products')) {
            \Illuminate\Support\Facades\Schema::create('products', function (\Illuminate\Database\Schema\Blueprint $table) {
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
            if (!\Illuminate\Support\Facades\Schema::hasColumn('products', 'thumbnail_id')) {
                if (\Illuminate\Support\Facades\Schema::hasTable('media')) {
                    try {
                        \Illuminate\Support\Facades\Schema::table('products', function (\Illuminate\Database\Schema\Blueprint $table) {
                            $table->foreignId('thumbnail_id')->nullable()->after('thumbnail_path')->constrained('media')->nullOnDelete();
                        });
                    } catch (\Exception $e) {
                        \Illuminate\Support\Facades\Schema::table('products', function (\Illuminate\Database\Schema\Blueprint $table) {
                            $table->unsignedBigInteger('thumbnail_id')->nullable()->after('thumbnail_path');
                        });
                    }
                } else {
                    \Illuminate\Support\Facades\Schema::table('products', function (\Illuminate\Database\Schema\Blueprint $table) {
                        $table->unsignedBigInteger('thumbnail_id')->nullable()->after('thumbnail_path');
                    });
                }
            }
            
            $created[] = 'products';
            $actions[] = 'Created products table';
        } else {
            $skipped[] = 'products';
        }
        
        // 5. Create teachers table
        if (!\Illuminate\Support\Facades\Schema::hasTable('teachers')) {
            \Illuminate\Support\Facades\Schema::create('teachers', function (\Illuminate\Database\Schema\Blueprint $table) {
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
            if (!\Illuminate\Support\Facades\Schema::hasColumn('teachers', 'avatar_id')) {
                if (\Illuminate\Support\Facades\Schema::hasTable('media')) {
                    try {
                        \Illuminate\Support\Facades\Schema::table('teachers', function (\Illuminate\Database\Schema\Blueprint $table) {
                            $table->foreignId('avatar_id')->nullable()->after('avatar_path')->constrained('media')->nullOnDelete();
                        });
                    } catch (\Exception $e) {
                        \Illuminate\Support\Facades\Schema::table('teachers', function (\Illuminate\Database\Schema\Blueprint $table) {
                            $table->unsignedBigInteger('avatar_id')->nullable()->after('avatar_path');
                        });
                    }
                } else {
                    \Illuminate\Support\Facades\Schema::table('teachers', function (\Illuminate\Database\Schema\Blueprint $table) {
                        $table->unsignedBigInteger('avatar_id')->nullable()->after('avatar_path');
                    });
                }
            }
            
            $created[] = 'teachers';
            $actions[] = 'Created teachers table';
        } else {
            $skipped[] = 'teachers';
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
        if ((\Illuminate\Support\Facades\Schema::hasTable('news') && \Illuminate\Support\Facades\Schema::hasColumn('news', 'thumbnail_id')) ||
            (\Illuminate\Support\Facades\Schema::hasTable('products') && \Illuminate\Support\Facades\Schema::hasColumn('products', 'thumbnail_id')) ||
            (\Illuminate\Support\Facades\Schema::hasTable('teachers') && \Illuminate\Support\Facades\Schema::hasColumn('teachers', 'avatar_id'))) {
            $migrations[] = '2025_12_21_140740_add_curator_media_fields_to_models';
        }
        
        $maxBatch = \DB::table('migrations')->max('batch') ?? 0;
        $newBatch = $maxBatch + 1;
        
        foreach ($migrations as $migration) {
            $exists = \DB::table('migrations')->where('migration', $migration)->exists();
            if (!$exists) {
                \DB::table('migrations')->insert([
                    'migration' => $migration,
                    'batch' => $newBatch,
                ]);
                $actions[] = "Marked migration as run: {$migration}";
            }
        }
        
        // Return HTML response
        $html = '<!DOCTYPE html><html dir="rtl" lang="fa"><head><meta charset="UTF-8"><title>Migration Results</title>';
        $html .= '<style>body{font-family:Vazirmatn,Arial;max-width:800px;margin:50px auto;padding:20px;background:#f5f5f5}';
        $html .= '.container{background:white;padding:30px;border-radius:8px;box-shadow:0 2px 10px rgba(0,0,0,0.1)}';
        $html .= '.success{color:#10b981;background:#d1fae5;padding:10px;border-radius:4px;margin:10px 0}';
        $html .= '.info{color:#3b82f6;background:#dbeafe;padding:10px;border-radius:4px;margin:10px 0}';
        $html .= 'ul{list-style:none;padding:0}li{padding:5px 0}</style></head><body><div class="container">';
        $html .= '<h1>نتیجه اجرای Migration ها</h1>';
        
        if (!empty($created)) {
            $html .= '<div class="success"><strong>✅ جداول ایجاد شده:</strong><ul>';
            foreach ($created as $table) {
                $html .= '<li>✓ ' . $table . '</li>';
            }
            $html .= '</ul></div>';
        }
        
        if (!empty($skipped)) {
            $html .= '<div class="info"><strong>⊘ جداول موجود (رد شده):</strong><ul>';
            foreach ($skipped as $table) {
                $html .= '<li>⊘ ' . $table . '</li>';
            }
            $html .= '</ul></div>';
        }
        
        if (!empty($actions)) {
            $html .= '<div class="info"><strong>عملیات انجام شده:</strong><ul>';
            foreach ($actions as $action) {
                $html .= '<li>' . htmlspecialchars($action) . '</li>';
            }
            $html .= '</ul></div>';
        }
        
        $html .= '<div class="success"><strong>✅ موفق!</strong><br>';
        $html .= 'اکنون می‌توانید به صفحات admin دسترسی داشته باشید.</div>';
        $html .= '</div></body></html>';
        
        return response($html)->header('Content-Type', 'text/html; charset=utf-8');
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error: ' . $e->getMessage(),
            'trace' => config('app.debug') ? $e->getTraceAsString() : null
        ], 500);
    }
})->middleware(['auth', 'admin'])->name('admin.run-missing-migrations');

// Sitemaps (Legacy WordPress URL structure)
Route::get('/sitemap_index.xml', [App\Http\Controllers\SitemapController::class, 'index'])->name('sitemap.index');
Route::get('/post-sitemap.xml', [App\Http\Controllers\SitemapController::class, 'posts'])->name('sitemap.posts');
Route::get('/ai_tool-sitemap.xml', [App\Http\Controllers\SitemapController::class, 'aiTools'])->name('sitemap.ai-tools');
Route::get('/course-sitemap.xml', [App\Http\Controllers\SitemapController::class, 'courses'])->name('sitemap.courses');
Route::get('/teacher-sitemap.xml', [App\Http\Controllers\SitemapController::class, 'teachers'])->name('sitemap.teachers');
Route::get('/product-sitemap.xml', [App\Http\Controllers\SitemapController::class, 'products'])->name('sitemap.products');
Route::get('/news-sitemap.xml', [App\Http\Controllers\SitemapController::class, 'news'])->name('sitemap.news');
Route::get('/page-sitemap.xml', [App\Http\Controllers\SitemapController::class, 'pages'])->name('sitemap.pages');
Route::get('/career-sitemap.xml', [App\Http\Controllers\SitemapController::class, 'careers'])->name('sitemap.careers');

// Dynamic Pages (must be last to avoid route conflicts)
Route::get('/{slug}', [App\Http\Controllers\PageController::class, 'show'])
    ->where('slug', '^(?!admin|api|auth|payment|ai-tools|posts|courses|careers|sitemap|go|app).*$')
    ->name('page.show');

