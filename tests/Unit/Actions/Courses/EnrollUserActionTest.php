<?php

namespace Tests\Unit\Actions\Courses;

use App\Domains\Auth\Models\User;
use App\Domains\Courses\Actions\EnrollUserAction;
use App\Domains\Courses\Exceptions\AlreadyEnrolledException;
use App\Domains\Courses\Models\Course;
use App\Domains\Courses\Models\Enrollment;
use App\Domains\Courses\Models\Teacher;
use App\Enums\CourseStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EnrollUserActionTest extends TestCase
{
    use RefreshDatabase;

    private EnrollUserAction $action;

    protected function setUp(): void
    {
        parent::setUp();
        $this->action = app(EnrollUserAction::class);
    }

    /**
     * Test user can be enrolled in a course.
     */
    public function test_user_can_be_enrolled_in_course(): void
    {
        $user = User::factory()->create();
        $teacher = Teacher::factory()->create(['user_id' => User::factory()->create()->id]);
        $course = Course::factory()->create([
            'teacher_id' => $teacher->id,
            'status' => CourseStatus::Published,
        ]);

        $enrollment = $this->action->handle($user, $course);

        $this->assertInstanceOf(Enrollment::class, $enrollment);
        $this->assertEquals($user->id, $enrollment->user_id);
        $this->assertEquals($course->id, $enrollment->course_id);
        $this->assertNotNull($enrollment->enrolled_at);
    }

    /**
     * Test duplicate enrollment throws exception.
     */
    public function test_duplicate_enrollment_throws_exception(): void
    {
        $user = User::factory()->create();
        $teacher = Teacher::factory()->create(['user_id' => User::factory()->create()->id]);
        $course = Course::factory()->create([
            'teacher_id' => $teacher->id,
            'status' => CourseStatus::Published,
        ]);

        // First enrollment
        $this->action->handle($user, $course);

        // Second enrollment should throw exception
        $this->expectException(AlreadyEnrolledException::class);
        $this->action->handle($user, $course);
    }

    /**
     * Test enrollment creates correct database record.
     */
    public function test_enrollment_creates_database_record(): void
    {
        $user = User::factory()->create();
        $teacher = Teacher::factory()->create(['user_id' => User::factory()->create()->id]);
        $course = Course::factory()->create([
            'teacher_id' => $teacher->id,
            'status' => CourseStatus::Published,
        ]);

        $this->action->handle($user, $course);

        $this->assertDatabaseHas('enrollments', [
            'user_id' => $user->id,
            'course_id' => $course->id,
        ]);
    }

    /**
     * Test user can enroll in multiple courses.
     */
    public function test_user_can_enroll_in_multiple_courses(): void
    {
        $user = User::factory()->create();
        $teacher = Teacher::factory()->create(['user_id' => User::factory()->create()->id]);
        
        $course1 = Course::factory()->create([
            'teacher_id' => $teacher->id,
            'status' => CourseStatus::Published,
        ]);
        $course2 = Course::factory()->create([
            'teacher_id' => $teacher->id,
            'status' => CourseStatus::Published,
        ]);

        $enrollment1 = $this->action->handle($user, $course1);
        $enrollment2 = $this->action->handle($user, $course2);

        $this->assertNotEquals($enrollment1->id, $enrollment2->id);
        $this->assertCount(2, $user->enrollments);
    }
}
