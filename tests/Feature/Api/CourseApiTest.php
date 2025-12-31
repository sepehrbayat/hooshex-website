<?php

namespace Tests\Feature\Api;

use App\Domains\Courses\Models\Course;
use App\Domains\Courses\Models\Teacher;
use App\Domains\Auth\Models\User;
use App\Enums\CourseStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CourseApiTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test courses index endpoint returns paginated results.
     */
    public function test_courses_index_returns_paginated_results(): void
    {
        $user = User::factory()->create();
        $teacher = Teacher::factory()->create(['user_id' => $user->id]);
        
        Course::factory()->count(5)->create([
            'teacher_id' => $teacher->id,
            'status' => CourseStatus::Published,
            'published_at' => now()->subDay(),
        ]);

        $response = $this->getJson('/api/v1/courses');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'title',
                        'slug',
                        'price',
                        'links',
                    ],
                ],
                'links',
                'meta',
            ]);
    }

    /**
     * Test courses show endpoint returns single course with chapters.
     */
    public function test_courses_show_returns_single_course(): void
    {
        $user = User::factory()->create();
        $teacher = Teacher::factory()->create(['user_id' => $user->id]);
        
        $course = Course::factory()->create([
            'teacher_id' => $teacher->id,
            'status' => CourseStatus::Published,
            'published_at' => now()->subDay(),
            'slug' => 'test-course',
        ]);

        $response = $this->getJson('/api/v1/courses/test-course');

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $course->id,
                    'title' => $course->title,
                    'slug' => 'test-course',
                ],
            ]);
    }

    /**
     * Test courses show endpoint returns 404 for non-existent course.
     */
    public function test_courses_show_returns_404_for_missing_course(): void
    {
        $response = $this->getJson('/api/v1/courses/non-existent');

        $response->assertStatus(404)
            ->assertJson([
                'message' => 'Course not found',
            ]);
    }

    /**
     * Test courses featured endpoint returns featured courses.
     */
    public function test_courses_featured_returns_featured_courses(): void
    {
        $user = User::factory()->create();
        $teacher = Teacher::factory()->create(['user_id' => $user->id]);
        
        Course::factory()->count(3)->create([
            'teacher_id' => $teacher->id,
            'status' => CourseStatus::Published,
            'published_at' => now()->subDay(),
            'is_featured' => true,
        ]);
        Course::factory()->count(2)->create([
            'teacher_id' => $teacher->id,
            'status' => CourseStatus::Published,
            'published_at' => now()->subDay(),
            'is_featured' => false,
        ]);

        $response = $this->getJson('/api/v1/courses/featured');

        $response->assertStatus(200);
        $this->assertCount(3, $response->json('data'));
    }

    /**
     * Test unpublished courses are not returned.
     */
    public function test_unpublished_courses_are_not_returned(): void
    {
        $user = User::factory()->create();
        $teacher = Teacher::factory()->create(['user_id' => $user->id]);
        
        Course::factory()->create([
            'teacher_id' => $teacher->id,
            'status' => CourseStatus::Draft,
            'published_at' => now()->subDay(),
        ]);
        Course::factory()->create([
            'teacher_id' => $teacher->id,
            'status' => CourseStatus::Published,
            'published_at' => now()->subDay(),
        ]);

        $response = $this->getJson('/api/v1/courses');

        $response->assertStatus(200);
        $this->assertCount(1, $response->json('data'));
    }

    /**
     * Test courses can be filtered by level.
     */
    public function test_courses_can_be_filtered_by_level(): void
    {
        $user = User::factory()->create();
        $teacher = Teacher::factory()->create(['user_id' => $user->id]);
        
        Course::factory()->create([
            'teacher_id' => $teacher->id,
            'status' => CourseStatus::Published,
            'published_at' => now()->subDay(),
            'level' => 'beginner',
        ]);
        Course::factory()->create([
            'teacher_id' => $teacher->id,
            'status' => CourseStatus::Published,
            'published_at' => now()->subDay(),
            'level' => 'advanced',
        ]);

        $response = $this->getJson('/api/v1/courses?level=beginner');

        $response->assertStatus(200);
        $this->assertCount(1, $response->json('data'));
    }
}
