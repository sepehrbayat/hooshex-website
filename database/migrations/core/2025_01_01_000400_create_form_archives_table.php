<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('form_archives', function (Blueprint $table) {
            $table->id();
            $table->string('type')->nullable();
            $table->string('source_table')->nullable();
            $table->unsignedBigInteger('original_id')->nullable();
            $table->json('payload');
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('form_archives');
    }
};

