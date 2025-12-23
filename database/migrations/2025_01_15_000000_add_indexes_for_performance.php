<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Add indexes for better query performance
 * Based on common WHERE, ORDER BY, and JOIN operations
 */
return new class extends Migration
{
    public function up(): void
    {
        // Users table indexes
        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                if (!$this->hasIndex('users', 'users_email_index')) {
                    $table->index('email');
                }
                if (!$this->hasIndex('users', 'users_mobile_index')) {
                    $table->index('mobile');
                }
            });
        }

        // Orders table indexes
        if (Schema::hasTable('orders')) {
            Schema::table('orders', function (Blueprint $table) {
                if (!$this->hasIndex('orders', 'orders_user_id_index')) {
                    $table->index('user_id');
                }
                if (!$this->hasIndex('orders', 'orders_status_index')) {
                    $table->index('status');
                }
                if (!$this->hasIndex('orders', 'orders_created_at_index')) {
                    $table->index('created_at');
                }
            });
        }

        // Enrollments table indexes
        if (Schema::hasTable('enrollments')) {
            Schema::table('enrollments', function (Blueprint $table) {
                if (!$this->hasIndex('enrollments', 'enrollments_user_id_index')) {
                    $table->index('user_id');
                }
                if (!$this->hasIndex('enrollments', 'enrollments_course_id_index')) {
                    $table->index('course_id');
                }
                // Composite index for common query: WHERE user_id = X AND course_id = Y
                if (!$this->hasIndex('enrollments', 'enrollments_user_course_index')) {
                    $table->index(['user_id', 'course_id']);
                }
            });
        }

        // Courses table indexes
        if (Schema::hasTable('courses')) {
            Schema::table('courses', function (Blueprint $table) {
                if (!$this->hasIndex('courses', 'courses_status_index')) {
                    $table->index('status');
                }
                if (!$this->hasIndex('courses', 'courses_published_at_index')) {
                    $table->index('published_at');
                }
                if (!$this->hasIndex('courses', 'courses_is_featured_index')) {
                    $table->index('is_featured');
                }
            });
        }

        // Order items table indexes
        if (Schema::hasTable('order_items')) {
            Schema::table('order_items', function (Blueprint $table) {
                if (!$this->hasIndex('order_items', 'order_items_order_id_index')) {
                    $table->index('order_id');
                }
                // Index for polymorphic relation queries
                if (!$this->hasIndex('order_items', 'order_items_orderable_index')) {
                    $table->index(['orderable_type', 'orderable_id']);
                }
            });
        }
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['email']);
            $table->dropIndex(['mobile']);
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->dropIndex(['status']);
            $table->dropIndex(['created_at']);
        });

        Schema::table('enrollments', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->dropIndex(['course_id']);
            $table->dropIndex(['user_id', 'course_id']);
        });

        Schema::table('courses', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropIndex(['published_at']);
            $table->dropIndex(['is_featured']);
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->dropIndex(['order_id']);
            $table->dropIndex(['orderable_type', 'orderable_id']);
        });
    }

    /**
     * Check if an index exists on a table
     * Database-agnostic implementation
     */
    private function hasIndex(string $table, string $indexName): bool
    {
        $connection = Schema::getConnection();
        $driver = $connection->getDriverName();

        try {
            if ($driver === 'sqlite') {
                // SQLite uses pragma_index_list
                $indexes = $connection->select("PRAGMA index_list({$table})");
                foreach ($indexes as $index) {
                    if ($index->name === $indexName) {
                        return true;
                    }
                }
                return false;
            } else {
                // MySQL/PostgreSQL use information_schema
                $database = $connection->getDatabaseName();
                $result = $connection->selectOne(
                    "SELECT COUNT(*) as count FROM information_schema.statistics 
                     WHERE table_schema = ? AND table_name = ? AND index_name = ?",
                    [$database, $table, $indexName]
                );
                return (int) $result->count > 0;
            }
        } catch (\Exception $e) {
            // If check fails, assume index doesn't exist
            return false;
        }
    }
};

