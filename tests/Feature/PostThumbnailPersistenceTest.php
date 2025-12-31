<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Domains\Blog\Models\Post;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Tests\TestCase;

final class PostThumbnailPersistenceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Keep this test максимально سریع: don't run the whole migrations stack.
        // The app uses non-standard migration paths, so full migrate:fresh can fail in tests.
        Schema::dropIfExists('posts');
        Schema::dropIfExists('media');
        Schema::dropIfExists('seo');

        Schema::create('media', function (Blueprint $table) {
            $table->id();
            $table->string('disk');
            $table->string('directory');
            $table->string('visibility')->nullable();
            $table->string('name');
            $table->string('path');
            $table->unsignedInteger('width')->nullable();
            $table->unsignedInteger('height')->nullable();
            $table->unsignedBigInteger('size')->default(0);
            $table->string('type')->nullable();
            $table->string('ext')->nullable();
            $table->text('alt')->nullable();
            $table->text('title')->nullable();
            $table->text('description')->nullable();
            $table->text('caption')->nullable();
            $table->longText('exif')->nullable();
            $table->longText('curations')->nullable();
            $table->timestamps();
        });

        Schema::create('seo', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('model_id');
            $table->string('model_type');
            $table->longText('description')->nullable();
            $table->string('title')->nullable();
            $table->string('image')->nullable();
            $table->string('author')->nullable();
            $table->string('robots')->nullable();
            $table->string('canonical_url')->nullable();
            $table->timestamps();
        });

        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('author_id')->nullable();
            $table->string('type', 20)->default('article');
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('excerpt')->nullable();
            $table->longText('content')->nullable();
            $table->json('focus_keywords')->nullable();
            $table->string('thumbnail_path')->nullable();
            $table->unsignedBigInteger('thumbnail_id')->nullable();
            $table->string('status', 20)->default('draft');
            $table->string('seo_title')->nullable();
            $table->text('seo_description')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->integer('reading_time')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->unsignedBigInteger('primary_category_id')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function test_post_thumbnail_id_persists_in_database(): void
    {
        $mediaId = (int) DB::table('media')->insertGetId([
            'disk' => 'public',
            'directory' => 'media',
            'visibility' => 'public',
            'name' => 'test-image',
            'path' => 'tests/test-image.jpg',
            'width' => 100,
            'height' => 100,
            'size' => 12345,
            'type' => 'image',
            'ext' => 'jpg',
            'alt' => null,
            'title' => null,
            'description' => null,
            'caption' => null,
            'exif' => null,
            'curations' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $slug = 'test-post-' . Str::lower(Str::random(10));

        $post = Post::create([
            'author_id' => null,
            'title' => 'Test Post',
            'slug' => $slug,
            'content' => 'Hello',
        ]);

        $post->update(['thumbnail_id' => $mediaId]);

        $this->assertDatabaseHas('posts', [
            'id' => $post->id,
            'thumbnail_id' => $mediaId,
        ]);

        $fresh = $post->fresh();

        $this->assertSame($mediaId, (int) $fresh->thumbnail_id);
        $this->assertNotNull($fresh->thumbnail);
        $this->assertSame($mediaId, (int) $fresh->thumbnail->id);
    }
}
