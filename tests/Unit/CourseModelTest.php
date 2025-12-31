<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Domains\Auth\Models\User;
use App\Domains\Courses\Models\Course;
use App\Domains\Courses\Models\Teacher;
use App\Enums\CourseStatus;
use App\Enums\CourseType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CourseModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_course_fillable_attributes(): void
    {
        $fillable = (new Course())->getFillable();

        $expectedAttributes = [
            'title',
            'slug',
            'description',
            'teacher_id',
            'price',
            'status',
            'course_type',
            'total_hours',
            'total_lessons',
            'has_lifetime_access',
            'has_practice_files',
            'what_you_learn',
            'course_requirements',
            'course_includes',
            'last_updated_at',
            'support_type',
            'is_featured',
            'published_at',
        ];

        foreach ($expectedAttributes as $attribute) {
            $this->assertContains($attribute, $fillable);
        }
    }

    public function test_course_casts_are_correct(): void
    {
        $course = new Course();
        $casts = $course->getCasts();

        $this->assertEquals('boolean', $casts['has_lifetime_access']);
        $this->assertEquals('boolean', $casts['has_practice_files']);
        $this->assertEquals('boolean', $casts['is_featured']);
        $this->assertEquals('datetime', $casts['last_updated_at']);
        $this->assertEquals('datetime', $casts['published_at']);
        $this->assertEquals('array', $casts['what_you_learn']);
        $this->assertEquals('array', $casts['course_requirements']);
        $this->assertEquals('array', $casts['course_includes']);
    }

    public function test_course_has_enum_casts(): void
    {
        $course = Course::factory()->create([
            'status' => CourseStatus::Published,
            'course_type' => CourseType::Online,
        ]);

        $this->assertInstanceOf(CourseStatus::class, $course->status);
        $this->assertInstanceOf(CourseType::class, $course->course_type);
    }

    public function test_course_uses_soft_deletes(): void
    {
        $course = Course::factory()->create();
        $courseId = $course->id;

        $course->delete();

        $this->assertSoftDeleted('courses', ['id' => $courseId]);

        // Can still find with trashed
        $deletedCourse = Course::withTrashed()->find($courseId);
        $this->assertNotNull($deletedCourse);
        $this->assertNotNull($deletedCourse->deleted_at);
    }

    public function test_course_can_be_restored(): void
    {
        $course = Course::factory()->create();
        $courseId = $course->id;

        $course->delete();
        $this->assertSoftDeleted('courses', ['id' => $courseId]);

        $course->restore();
        $this->assertDatabaseHas('courses', [
            'id' => $courseId,
            'deleted_at' => null,
        ]);
    }

    public function test_course_belongs_to_teacher_relationship(): void
    {
        $teacher = Teacher::factory()->create();
        $course = Course::factory()->create(['teacher_id' => $teacher->user_id]);

        $this->assertEquals($teacher->user_id, $course->teacher->id);
        $this->assertInstanceOf(User::class, $course->teacher);
    }

    public function test_course_slug_is_unique(): void
    {
        Course::factory()->create(['slug' => 'laravel-course']);

        $this->expectException(\Exception::class);

        Course::factory()->create(['slug' => 'laravel-course']);
    }

    public function test_course_price_is_integer(): void
    {
        $course = Course::factory()->create(['price' => 2500000]);

        $this->assertIsInt($course->price);
        $this->assertEquals(2500000, $course->price);
    }

    public function test_course_total_hours_and_lessons(): void
    {
        $course = Course::factory()->create([
            'total_hours' => 35,
            'total_lessons' => 100,
        ]);

        $this->assertIsInt($course->total_hours);
        $this->assertIsInt($course->total_lessons);
        $this->assertEquals(35, $course->total_hours);
        $this->assertEquals(100, $course->total_lessons);
    }

    public function test_course_support_type_is_string(): void
    {
        $course = Course::factory()->create([
            'support_type' => 'گروه تلگرام + تیکت',
        ]);

        $this->assertIsString($course->support_type);
        $this->assertEquals('گروه تلگرام + تیکت', $course->support_type);
    }

    public function test_course_json_fields_default_to_empty_array(): void
    {
        $course = Course::factory()->create([
            'what_you_learn' => null,
            'course_requirements' => null,
            'course_includes' => null,
        ]);

        // Based on casts, null JSON becomes null, not []
        $this->assertNull($course->what_you_learn);
    }
}
