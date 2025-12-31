<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Domains\Auth\Models\User;
use App\Domains\Courses\Models\Course;
use App\Domains\Courses\Models\Teacher;
use App\Enums\CourseStatus;
use App\Enums\CourseType;
use App\Enums\UserRole;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CourseManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $student;
    protected Teacher $teacher;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => UserRole::Admin]);
        $this->student = User::factory()->create(['role' => UserRole::Student]);
        $this->teacher = Teacher::factory()->create();
    }

    public function test_admin_can_view_courses_list(): void
    {
        Course::factory()->count(5)->create();

        $response = $this->actingAs($this->admin, 'admin')
            ->get('/admin/courses');

        $response->assertStatus(200);
    }

    public function test_admin_can_create_course_with_all_fields(): void
    {
        $courseData = [
            'title' => 'دوره آموزش Laravel پیشرفته',
            'slug' => 'laravel-advanced',
            'description' => 'یک دوره کامل و جامع برای یادگیری Laravel',
            'teacher_id' => $this->teacher->user_id,
            'price' => 2500000,
            'status' => CourseStatus::Published->value,
            'course_type' => CourseType::Online->value,
            'total_hours' => 40,
            'total_lessons' => 120,
            'has_lifetime_access' => true,
            'has_practice_files' => true,
            'what_you_learn' => [
                'ساخت برنامه‌های وب با Laravel',
                'کار با Database و Eloquent',
                'ایجاد API RESTful',
            ],
            'course_requirements' => [
                'آشنایی با PHP',
                'آشنایی با HTML و CSS',
            ],
            'course_includes' => [
                '120 ویدیو آموزشی',
                'فایل‌های تمرینی',
                'پشتیبانی مادام‌العمر',
            ],
            'support_type' => 'گروه تلگرام + تیکت',
            'is_featured' => true,
            'published_at' => now(),
        ];

        $course = Course::create($courseData);

        $this->assertDatabaseHas('courses', [
            'title' => 'دوره آموزش Laravel پیشرفته',
            'slug' => 'laravel-advanced',
            'teacher_id' => $this->teacher->user_id,
            'total_hours' => 40,
            'total_lessons' => 120,
            'has_lifetime_access' => true,
            'has_practice_files' => true,
        ]);

        $this->assertEquals(3, count($course->what_you_learn));
        $this->assertEquals(2, count($course->course_requirements));
        $this->assertEquals(3, count($course->course_includes));
    }

    public function test_admin_can_update_course(): void
    {
        $course = Course::factory()->create([
            'title' => 'دوره قدیمی',
            'total_hours' => 20,
        ]);

        $course->update([
            'title' => 'دوره جدید',
            'total_hours' => 30,
            'last_updated_at' => now(),
        ]);

        $this->assertDatabaseHas('courses', [
            'id' => $course->id,
            'title' => 'دوره جدید',
            'total_hours' => 30,
        ]);

        $this->assertNotNull($course->fresh()->last_updated_at);
    }

    public function test_admin_can_delete_course(): void
    {
        $course = Course::factory()->create();

        $course->delete();

        $this->assertSoftDeleted('courses', [
            'id' => $course->id,
        ]);
    }

    public function test_course_belongs_to_teacher(): void
    {
        $course = Course::factory()->create([
            'teacher_id' => $this->teacher->user_id,
        ]);

        $this->assertInstanceOf(User::class, $course->teacher);
        $this->assertEquals($this->teacher->user_id, $course->teacher->id);
    }

    public function test_published_courses_scope_works(): void
    {
        Course::factory()->create(['status' => CourseStatus::Published]);
        Course::factory()->create(['status' => CourseStatus::Published]);
        Course::factory()->create(['status' => CourseStatus::Draft]);

        $publishedCourses = Course::published()->get();

        $this->assertCount(2, $publishedCourses);
    }

    public function test_featured_courses_scope_works(): void
    {
        Course::factory()->create(['is_featured' => true]);
        Course::factory()->create(['is_featured' => true]);
        Course::factory()->create(['is_featured' => false]);

        $featuredCourses = Course::featured()->get();

        $this->assertCount(2, $featuredCourses);
    }

    public function test_course_price_is_formatted_correctly(): void
    {
        $course = Course::factory()->create(['price' => 1500000]);

        $this->assertEquals(1500000, $course->price);
    }

    public function test_course_casts_json_fields_correctly(): void
    {
        $whatYouLearn = ['مهارت 1', 'مهارت 2', 'مهارت 3'];
        $requirements = ['پیش‌نیاز 1', 'پیش‌نیاز 2'];
        $includes = ['شامل 1', 'شامل 2'];

        $course = Course::factory()->create([
            'what_you_learn' => $whatYouLearn,
            'course_requirements' => $requirements,
            'course_includes' => $includes,
        ]);

        $course = $course->fresh();

        $this->assertIsArray($course->what_you_learn);
        $this->assertIsArray($course->course_requirements);
        $this->assertIsArray($course->course_includes);
        $this->assertEquals($whatYouLearn, $course->what_you_learn);
    }

    public function test_course_has_lifetime_access_boolean(): void
    {
        $course = Course::factory()->create([
            'has_lifetime_access' => true,
            'has_practice_files' => false,
        ]);

        $this->assertTrue($course->has_lifetime_access);
        $this->assertFalse($course->has_practice_files);
    }

    public function test_guest_cannot_access_admin_courses(): void
    {
        $response = $this->get('/admin/courses');

        $response->assertRedirect('/admin/login');
    }

    public function test_student_cannot_access_admin_courses(): void
    {
        $response = $this->actingAs($this->student, 'admin')
            ->get('/admin/courses');

        $response->assertStatus(403);
    }

    public function test_course_total_duration_is_calculated_correctly(): void
    {
        $course = Course::factory()->create([
            'total_hours' => 40,
            'total_lessons' => 120,
        ]);

        $this->assertEquals(40, $course->total_hours);
        $this->assertEquals(120, $course->total_lessons);
    }

    public function test_course_status_enum_works(): void
    {
        $course = Course::factory()->create([
            'status' => CourseStatus::Draft,
        ]);

        $this->assertInstanceOf(CourseStatus::class, $course->status);
        $this->assertEquals('draft', $course->status->value);
    }

    public function test_course_type_enum_works(): void
    {
        $course = Course::factory()->create([
            'course_type' => CourseType::Online,
        ]);

        $this->assertInstanceOf(CourseType::class, $course->course_type);
    }

    public function test_course_can_be_archived(): void
    {
        $course = Course::factory()->create([
            'status' => CourseStatus::Published,
        ]);

        $course->update(['status' => CourseStatus::Archived]);

        $this->assertEquals(CourseStatus::Archived, $course->fresh()->status);
    }

    public function test_course_last_updated_timestamp_works(): void
    {
        $course = Course::factory()->create();

        $this->assertNull($course->last_updated_at);

        $course->update([
            'title' => 'عنوان جدید',
            'last_updated_at' => now(),
        ]);

        $this->assertNotNull($course->fresh()->last_updated_at);
    }
}
