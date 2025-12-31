<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('orders')) {
            Schema::create('orders', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->string('status', 20)->default('pending');
                $table->unsignedBigInteger('total_amount')->default(0);
                $table->string('gateway', 50)->default('zarinpal');
                $table->string('gateway_ref_id')->nullable();
                $table->string('transaction_id')->nullable();
                $table->json('billing_address')->nullable();
                $table->string('ip_address', 45)->nullable();
                $table->timestamps();

                $table->index('user_id');
                $table->index('status');
                $table->index('created_at');
            });
        }

        if (!Schema::hasTable('order_items')) {
            Schema::create('order_items', function (Blueprint $table) {
                $table->id();
                $table->foreignId('order_id')->constrained()->cascadeOnDelete();
                $table->morphs('orderable');
                $table->unsignedBigInteger('price')->default(0);
                $table->unsignedInteger('quantity')->default(1);
                $table->timestamps();

                $table->index('order_id');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
    }
};
