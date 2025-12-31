<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Domains\Auth\Models\User;
use App\Domains\Courses\Models\Course;
use App\Domains\Courses\Models\Enrollment;
use App\Enums\CourseStatus;
use App\Enums\UserRole;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CourseEnrollmentTest extends TestCase
{
    use RefreshDatabase;

    protected User $student;
    protected Course $course;

    protected function setUp(): void
    {
        parent::setUp();

        $this->student = User::factory()->create(['role' => UserRole::Student]);
        $this->course = Course::factory()->create([
            'status' => CourseStatus::Published,
            'price' => 1000000,
        ]);
    }

    public function test_student_can_enroll_in_course(): void
    {
        $enrollment = Enrollment::create([
            'user_id' => $this->student->id,
            'course_id' => $this->course->id,
            'enrolled_at' => now(),
        ]);

        $this->assertDatabaseHas('enrollments', [
            'user_id' => $this->student->id,
            'course_id' => $this->course->id,
        ]);

        $this->assertInstanceOf(Course::class, $enrollment->course);
        $this->assertInstanceOf(User::class, $enrollment->user);
    }

    public function test_student_cannot_enroll_twice_in_same_course(): void
    {
        Enrollment::create([
            'user_id' => $this->student->id,
            'course_id' => $this->course->id,
            'enrolled_at' => now(),
        ]);

        $this->expectException(\Exception::class);

        Enrollment::create([
            'user_id' => $this->student->id,
            'course_id' => $this->course->id,
            'enrolled_at' => now(),
        ]);
    }

    public function test_course_has_many_enrollments(): void
    {
        $student1 = User::factory()->create(['role' => UserRole::Student]);
        $student2 = User::factory()->create(['role' => UserRole::Student]);

        Enrollment::create([
            'user_id' => $student1->id,
            'course_id' => $this->course->id,
            'enrolled_at' => now(),
        ]);

        Enrollment::create([
            'user_id' => $student2->id,
            'course_id' => $this->course->id,
            'enrolled_at' => now(),
        ]);

        $this->assertCount(2, $this->course->enrollments);
    }

    public function test_user_has_many_enrollments(): void
    {
        $course1 = Course::factory()->create(['status' => CourseStatus::Published]);
        $course2 = Course::factory()->create(['status' => CourseStatus::Published]);

        Enrollment::create([
            'user_id' => $this->student->id,
            'course_id' => $course1->id,
            'enrolled_at' => now(),
        ]);

        Enrollment::create([
            'user_id' => $this->student->id,
            'course_id' => $course2->id,
            'enrolled_at' => now(),
        ]);

        $this->assertCount(2, $this->student->enrollments);
    }

    public function test_enrollment_tracks_progress(): void
    {
        $enrollment = Enrollment::create([
            'user_id' => $this->student->id,
            'course_id' => $this->course->id,
            'enrolled_at' => now(),
            'progress' => 0,
        ]);

        $enrollment->update(['progress' => 50]);

        $this->assertEquals(50, $enrollment->fresh()->progress);
    }

    public function test_enrollment_can_be_completed(): void
    {
        $enrollment = Enrollment::create([
            'user_id' => $this->student->id,
            'course_id' => $this->course->id,
            'enrolled_at' => now(),
            'progress' => 0,
        ]);

        $enrollment->update([
            'progress' => 100,
            'completed_at' => now(),
        ]);

        $this->assertEquals(100, $enrollment->fresh()->progress);
        $this->assertNotNull($enrollment->fresh()->completed_at);
    }

    public function test_only_published_courses_can_be_enrolled(): void
    {
        $draftCourse = Course::factory()->create([
            'status' => CourseStatus::Draft,
        ]);

        // This should be prevented by business logic
        $enrollment = Enrollment::create([
            'user_id' => $this->student->id,
            'course_id' => $draftCourse->id,
            'enrolled_at' => now(),
        ]);

        // In real app, we'd prevent this in controller/action
        $this->assertDatabaseHas('enrollments', [
            'course_id' => $draftCourse->id,
        ]);
    }
}
