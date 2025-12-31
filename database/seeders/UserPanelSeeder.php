<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Domains\Auth\Models\User;
use App\Domains\AiTools\Models\AiTool;
use App\Domains\Commerce\Models\Order;
use App\Domains\Commerce\Models\OrderItem;
use App\Domains\Core\Models\Career;
use App\Domains\Courses\Models\Chapter;
use App\Domains\Courses\Models\Course;
use App\Domains\Courses\Models\CourseLicense;
use App\Domains\Courses\Models\Enrollment;
use App\Domains\Courses\Models\Lesson;
use App\Domains\Courses\Models\Teacher;
use App\Enums\CourseLevel;
use App\Enums\CourseStatus;
use App\Enums\CourseType;
use App\Enums\OrderStatus;
use App\Enums\UserRole;
use App\Enums\WorkType;
use App\Enums\ContractType;
use App\Enums\PricingType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class UserPanelSeeder extends Seeder
{
    public function run(): void
    {
        // Create or get test user
        $testUser = User::firstOrCreate(
            ['email' => 'user@test.com'],
            [
                'username' => 'testuser',
                'name' => 'کاربر تست',
                'password' => 'password',
                'email_verified_at' => now(),
                'role' => UserRole::Student,
            ]
        );
        
        // Always ensure password is set correctly (in case user already existed)
        if (!$testUser->wasRecentlyCreated) {
            $testUser->password = 'password';
            $testUser->save();
        }

        $this->command->info('✅ Test user created: user@test.com / password');

        // Create Careers
        $careers = $this->createCareers();
        $this->command->info('✅ ' . count($careers) . ' career paths created');

        // Assign a career to test user
        if (!empty($careers)) {
            $testUser->update(['selected_career_id' => $careers[0]->id]);
            $this->command->info('✅ Career assigned to test user');
        }

        // Create Courses, Enrollments, Orders, and Licenses
        $courses = $this->createCoursesAndEnrollments($testUser);
        $this->command->info('✅ ' . count($courses) . ' courses created with enrollments');

        // Create Bookmarks
        $bookmarksCount = $this->createBookmarks($testUser);
        $this->command->info("✅ {$bookmarksCount} bookmarks created");

        $this->command->info('');
        $this->command->info('🎉 User panel seed data created successfully!');
        $this->command->info('You can now test all user panel pages.');
    }

    protected function createCareers(): array
    {
        $careers = [];

        $careerData = [
            [
                'title' => 'برنامه‌نویس Frontend',
                'department' => 'توسعه نرم‌افزار',
                'location' => 'تهران',
                'work_type' => WorkType::Remote,
                'contract_type' => ContractType::FullTime,
                'salary_range' => '۱۵ تا ۲۵ میلیون تومان',
                'experience_level' => 'متوسط',
                'short_description' => 'مسیر شغلی برای تبدیل شدن به یک برنامه‌نویس Frontend حرفه‌ای',
                'description' => 'این مسیر شغلی شما را با آخرین تکنولوژی‌های Frontend مانند React, Vue.js, و TypeScript آشنا می‌کند.',
                'responsibilities' => [
                    'توسعه رابط کاربری با React/Vue',
                    'بهینه‌سازی عملکرد',
                    'همکاری با تیم Backend',
                ],
                'requirements' => [
                    'آشنایی با HTML/CSS/JavaScript',
                    'تجربه کار با فریمورک‌های Frontend',
                    'توانایی کار تیمی',
                ],
                'benefits' => [
                    'کار از راه دور',
                    'پاداش عملکرد',
                    'بیمه تکمیلی',
                ],
            ],
            [
                'title' => 'طراح UI/UX',
                'department' => 'طراحی',
                'location' => 'اصفهان',
                'work_type' => WorkType::Hybrid,
                'contract_type' => ContractType::FullTime,
                'salary_range' => '۱۲ تا ۲۰ میلیون تومان',
                'experience_level' => 'مبتدی',
                'short_description' => 'مسیر شغلی برای تبدیل شدن به یک طراح UI/UX موفق',
                'description' => 'یادگیری اصول طراحی رابط کاربری و تجربه کاربری با ابزارهای مدرن مانند Figma و Adobe XD.',
                'responsibilities' => [
                    'طراحی رابط کاربری',
                    'تحقیق کاربر',
                    'ایجاد پروتوتایپ',
                ],
                'requirements' => [
                    'سلیقه بصری خوب',
                    'آشنایی با Figma',
                    'درک اصول UX',
                ],
                'benefits' => [
                    'محیط کاری خلاق',
                    'آموزش مداوم',
                    'امکانات رفاهی',
                ],
            ],
            [
                'title' => 'متخصص هوش مصنوعی',
                'department' => 'هوش مصنوعی',
                'location' => 'مشهد',
                'work_type' => WorkType::OnSite,
                'contract_type' => ContractType::FullTime,
                'salary_range' => '۲۰ تا ۳۵ میلیون تومان',
                'experience_level' => 'پیشرفته',
                'short_description' => 'مسیر شغلی برای تبدیل شدن به یک متخصص AI',
                'description' => 'یادگیری مباحث پیشرفته هوش مصنوعی، یادگیری ماشین، و پردازش زبان طبیعی.',
                'responsibilities' => [
                    'توسعه مدل‌های ML',
                    'تحلیل داده',
                    'پیاده‌سازی الگوریتم‌های AI',
                ],
                'requirements' => [
                    'دانش ریاضی قوی',
                    'تجربه با Python',
                    'آشنایی با TensorFlow/PyTorch',
                ],
                'benefits' => [
                    'پروژه‌های چالش‌برانگیز',
                    'حقوق رقابتی',
                    'فرصت‌های رشد',
                ],
            ],
        ];

        foreach ($careerData as $data) {
            $careers[] = Career::firstOrCreate(
                ['slug' => Str::slug($data['title'])],
                array_merge($data, [
                    'is_active' => true,
                    'published_at' => now()->subDays(rand(1, 30)),
                ])
            );
        }

        return $careers;
    }

    protected function createCoursesAndEnrollments(User $user): array
    {
        // Get or create a teacher
        $teacherUser = User::firstOrCreate(
            ['email' => 'teacher@test.com'],
            [
                'username' => 'teacher',
                'name' => 'استاد نمونه',
                'password' => 'password',
                'email_verified_at' => now(),
                'role' => UserRole::Teacher,
            ]
        );

        $teacher = Teacher::firstOrCreate(
            ['user_id' => $teacherUser->id],
            [
                'slug' => Str::slug($teacherUser->name),
                'bio' => 'استاد با تجربه در زمینه برنامه‌نویسی و طراحی',
                'specialty' => 'توسعه نرم‌افزار',
                'is_featured' => true,
                'published_at' => now(),
            ]
        );

        $courses = [];

        $courseData = [
            [
                'title' => 'دوره کامل React.js',
                'short_description' => 'یادگیری React از صفر تا صد',
                'description' => 'در این دوره جامع React.js را از پایه تا پیشرفته یاد خواهید گرفت.',
                'price' => 2500000,
                'sale_price' => 2000000,
                'level' => CourseLevel::Beginner,
                'course_type' => CourseType::Online,
                'total_hours' => 40,
                'total_lessons' => 50,
                'has_lifetime_access' => true,
                'has_practice_files' => true,
                'support_type' => 'گروه تلگرام + تیکت',
            ],
            [
                'title' => 'دوره پیشرفته Node.js',
                'short_description' => 'برنامه‌نویسی Backend با Node.js',
                'description' => 'یادگیری Node.js و Express.js برای ساخت API های قدرتمند.',
                'price' => 3000000,
                'sale_price' => 2500000,
                'level' => CourseLevel::Intermediate,
                'course_type' => CourseType::Online,
                'total_hours' => 35,
                'total_lessons' => 45,
                'has_lifetime_access' => true,
                'has_practice_files' => true,
                'support_type' => 'فروم پرسش و پاسخ',
            ],
            [
                'title' => 'دوره طراحی UI/UX با Figma',
                'short_description' => 'طراحی رابط کاربری حرفه‌ای',
                'description' => 'یادگیری طراحی UI/UX و استفاده از Figma برای طراحی رابط‌های کاربری مدرن.',
                'price' => 1800000,
                'sale_price' => null,
                'level' => CourseLevel::Beginner,
                'course_type' => CourseType::Online,
                'total_hours' => 25,
                'total_lessons' => 30,
                'has_lifetime_access' => true,
                'has_practice_files' => true,
                'support_type' => 'جلسه لایو هفتگی',
            ],
        ];

        foreach ($courseData as $index => $data) {
            $course = Course::firstOrCreate(
                ['slug' => Str::slug($data['title'])],
                array_merge($data, [
                    'teacher_id' => $teacher->user_id,
                    'status' => CourseStatus::Published,
                    'is_featured' => $index === 0,
                    'published_at' => now()->subDays(rand(1, 60)),
                    'what_you_learn' => ['پروژه عملی', 'مباحث پیشرفته', 'تمرین تعاملی'],
                    'course_requirements' => ['آشنایی مقدماتی با کامپیوتر'],
                    'course_includes' => ['دسترسی دائمی', 'فایل‌های تمرینی', 'گواهینامه پایان دوره'],
                ])
            );

            $courses[] = $course;

            // Create chapters and lessons
            $this->createChaptersAndLessons($course);

            // Create enrollment for test user
            $enrollment = Enrollment::firstOrCreate(
                [
                    'user_id' => $user->id,
                    'course_id' => $course->id,
                ],
                [
                    'enrolled_at' => now()->subDays(rand(1, 30)),
                    'expires_at' => $index === 2 ? now()->addDays(30) : null, // Third course has expiration
                    'progress' => rand(0, 100),
                ]
            );

            // Create order for the enrollment
            $order = Order::firstOrCreate(
                [
                    'user_id' => $user->id,
                    'total_amount' => $data['sale_price'] ?? $data['price'],
                ],
                [
                    'status' => OrderStatus::Paid,
                    'gateway' => 'zarinpal',
                    'transaction_id' => 'TXN-' . Str::random(10),
                    'created_at' => $enrollment->enrolled_at,
                ]
            );

            // Create order item
            OrderItem::firstOrCreate(
                [
                    'order_id' => $order->id,
                    'orderable_type' => Course::class,
                    'orderable_id' => $course->id,
                ],
                [
                    'price' => $data['sale_price'] ?? $data['price'],
                    'quantity' => 1,
                ]
            );

            // Create license for the first two courses
            if ($index < 2) {
                CourseLicense::firstOrCreate(
                    [
                        'user_id' => $user->id,
                        'course_id' => $course->id,
                    ],
                    [
                        'order_id' => $order->id,
                        'license_key' => 'LIC-' . strtoupper(Str::random(12)),
                        'is_active' => true,
                        'expires_at' => $index === 0 ? now()->addYear() : null, // First course expires in 1 year
                        'assigned_by' => $teacherUser->id,
                        'notes' => 'لایسنس دوره ' . $course->title,
                    ]
                );
            }
        }

        return $courses;
    }

    protected function createChaptersAndLessons(Course $course): void
    {
        // Only create if course doesn't have chapters
        if ($course->chapters()->exists()) {
            return;
        }

        for ($c = 1; $c <= 3; $c++) {
            $chapter = Chapter::create([
                'course_id' => $course->id,
                'title' => "فصل {$c}: مباحث اصلی",
                'sort_order' => $c,
            ]);

            for ($l = 1; $l <= 5; $l++) {
                Lesson::create([
                    'chapter_id' => $chapter->id,
                    'title' => "درس {$c}-{$l}: " . ['مقدمه', 'آموزش عملی', 'تمرین', 'پروژه', 'نتیجه‌گیری'][$l - 1],
                    'duration' => rand(10, 45) . ' دقیقه',
                    'is_free_preview' => $l === 1,
                    'sort_order' => $l,
                    'content' => 'محتوای کامل این درس برای تست و بررسی در پنل کاربری.',
                ]);
            }
        }
    }

    protected function createBookmarks(User $user): int
    {
        // Create some AI tools if they don't exist
        $aiTools = [];
        
        $aiToolData = [
            [
                'name' => 'ChatGPT',
                'slug' => 'chatgpt',
                'short_description' => 'هوش مصنوعی گفتگو',
                'content' => 'ابزار قدرتمند هوش مصنوعی برای گفتگو و پاسخ به سوالات. ChatGPT یک مدل زبان بزرگ است که می‌تواند به سوالات شما پاسخ دهد و در کارهای مختلف به شما کمک کند.',
                'pricing_type' => PricingType::Freemium,
                'price' => 0,
            ],
            [
                'name' => 'Midjourney',
                'slug' => 'midjourney',
                'short_description' => 'تولید تصویر با AI',
                'content' => 'ایجاد تصاویر زیبا با استفاده از هوش مصنوعی. Midjourney یکی از بهترین ابزارهای تولید تصویر با AI است که می‌تواند تصاویر هنری و خلاقانه ایجاد کند.',
                'pricing_type' => PricingType::Paid,
                'price' => 100000,
            ],
            [
                'name' => 'GitHub Copilot',
                'slug' => 'github-copilot',
                'short_description' => 'دستیار برنامه‌نویسی',
                'content' => 'کد نویسی سریع‌تر با کمک هوش مصنوعی. GitHub Copilot یک دستیار هوش مصنوعی است که به شما در نوشتن کد کمک می‌کند و پیشنهادات هوشمندانه ارائه می‌دهد.',
                'pricing_type' => PricingType::Paid,
                'price' => 200000,
            ],
        ];

        foreach ($aiToolData as $data) {
            $aiTool = AiTool::firstOrCreate(
                ['slug' => $data['slug']],
                array_merge($data, [
                    'published_at' => now()->subDays(rand(1, 60)),
                ])
            );
            $aiTools[] = $aiTool;
        }

        // Create bookmarks for test user
        $bookmarksCount = 0;
        foreach ($aiTools as $aiTool) {
            $exists = DB::table('bookmarks')
                ->where('user_id', $user->id)
                ->where('ai_tool_id', $aiTool->id)
                ->exists();

            if (!$exists) {
                DB::table('bookmarks')->insert([
                    'user_id' => $user->id,
                    'ai_tool_id' => $aiTool->id,
                    'created_at' => now()->subDays(rand(1, 20)),
                    'updated_at' => now(),
                ]);
                $bookmarksCount++;
            }
        }

        return $bookmarksCount;
    }
}

