<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Domains\Auth\Models\User;
use App\Domains\Commerce\Models\Order;
use App\Domains\Commerce\Models\OrderItem;
use App\Domains\Courses\Models\Chapter;
use App\Domains\Courses\Models\Course;
use App\Domains\Courses\Models\Enrollment;
use App\Domains\Courses\Models\Lesson;
use App\Domains\Courses\Models\Teacher;
use App\Enums\CourseLevel;
use App\Enums\CourseType;
use App\Enums\OrderStatus;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;

class CourseDemoSeeder extends Seeder
{
    public function run(): void
    {
        if (Course::query()->exists()) {
            return; // keep existing data intact
        }

        $supportTypes = [
            'گروه تلگرام + تیکت',
            'فروم پرسش و پاسخ',
            'جلسه لایو هفتگی',
        ];

        $teachers = Teacher::factory()->count(3)->create();

        foreach ($teachers as $teacher) {
            $courses = Course::factory()
                ->count(2)
                ->create([
                    'teacher_id' => $teacher->user_id,
                    'course_type' => CourseType::Online,
                    'level' => Arr::random([
                        CourseLevel::Beginner,
                        CourseLevel::Intermediate,
                        CourseLevel::Advanced,
                    ]),
                    'total_hours' => rand(8, 30),
                    'total_lessons' => 6,
                    'has_lifetime_access' => true,
                    'has_practice_files' => (bool) rand(0, 1),
                    'support_type' => Arr::random($supportTypes),
                    'what_you_learn' => ['پروژه عملی', 'مباحث پیشرفته', 'تمرین تعاملی'],
                    'course_requirements' => ['آشنایی مقدماتی با کامپیوتر'],
                    'course_includes' => ['دسترسی دائمی', 'فایل‌های تمرینی', 'گواهینامه پایان دوره'],
                ]);

            foreach ($courses as $course) {
                $chapters = [];
                for ($c = 1; $c <= 2; $c++) {
                    $chapters[] = Chapter::create([
                        'course_id' => $course->id,
                        'title' => "فصل {$c}",
                        'sort_order' => $c,
                    ]);
                }

                foreach ($chapters as $chapterIndex => $chapter) {
                    for ($l = 1; $l <= 3; $l++) {
                        Lesson::create([
                            'chapter_id' => $chapter->id,
                            'title' => "درس {$chapterIndex}-{$l}",
                            'duration' => rand(5, 20) . ' دقیقه',
                            'is_free_preview' => $l === 1,
                            'is_free' => $l === 1,
                            'sort_order' => $l,
                            'content' => 'نمونه محتوای درس برای تست در محیط ادمین.',
                        ]);
                    }
                }

                $students = User::factory()->count(2)->create();
                foreach ($students as $student) {
                    Enrollment::create([
                        'user_id' => $student->id,
                        'course_id' => $course->id,
                        'enrolled_at' => now()->subDays(rand(1, 30)),
                        'expires_at' => null,
                        'progress' => rand(0, 100),
                    ]);

                    $order = Order::factory()->state([
                        'user_id' => $student->id,
                        'status' => OrderStatus::Paid,
                        'total_amount' => (int) ($course->sale_price ?? $course->price),
                        'gateway' => 'zarinpal',
                    ])->create();

                    OrderItem::create([
                        'order_id' => $order->id,
                        'orderable_type' => Course::class,
                        'orderable_id' => $course->id,
                        'price' => (int) ($course->sale_price ?? $course->price),
                        'quantity' => 1,
                    ]);
                }
            }
        }
    }
}
