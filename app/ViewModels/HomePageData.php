<?php

declare(strict_types=1);

namespace App\ViewModels;

use App\Domains\Courses\Models\Course;
use App\Enums\CourseStatus;

/**
 * Home Page Data ViewModel
 * Contains all data structures for home page sections
 */
class HomePageData
{
    public static function features(): array
    {
        return [
            [
                'title' => 'دوره های متنوع',
                'description' => 'بیش از 1۰ دوره آموزشی کاربردی',
                'icon' => 'figma-images/images/various-courses-vector.png',
            ],
            [
                'title' => 'مسیر شغلی',
                'description' => 'طراحی مسیر شغلی',
                'icon' => 'figma-images/images/career-path-vector.png',
            ],
            [
                'title' => 'سوپر اپلیکیشن',
                'description' => 'بهترین هوش‌مصنوعی فارسی',
                'icon' => 'figma-images/images/super-app-vector.png',
            ],
            [
                'title' => 'توسعه هوش‌مصنوعی',
                'description' => 'اتوماسیون فرآیند با هوش‌مصنوعی',
                'icon' => 'figma-images/images/dev-with-ai-vector.png',
            ],
        ];
    }

    public static function testimonials(): array
    {
        return [
            [
                'name' => 'نسترن',
                'text' => 'دوره خیلی مفیدی بود و مطالب رو به شکل کاربردی توضیح می‌داد. پروژه‌های عملی کمک کرد تجربه واقعی پیدا کنم. در کل بنظرم ارزش وقت و هزینه‌اش رو داشت.',
            ],
            [
                'name' => 'محمد',
                'text' => 'قبل از این دوره هیچ تجربه‌ای از وردپرس نداشتم، ولی قدم‌به‌قدم یاد گرفتم سایت رو از صفر طراحی کنم. اما توضیحات و تمرین‌ها کمک کرد خوب وسریع پیش برم.',
            ],
            [
                'name' => 'سحر',
                'text' => 'دوره خیلی مفیدی بود و مطالب رو به شکل کاربردی توضیح می‌داد. پروژه‌های عملی کمک کرد تجربه واقعی پیدا کنم. در کل بنظرم ارزش وقت و هزینه‌اش رو داشت.',
            ],
            [
                'name' => 'علی',
                'text' => 'بهترین دوره‌ای بود که تا حالا شرکت کردم. محتوا خیلی کامل و جامع بود و استاد خیلی خوب توضیح می‌داد. حتماً دوره‌های دیگه رو هم می‌خرم.',
            ],
            [
                'name' => 'فاطمه',
                'text' => 'من تازه شروع به یادگیری کرده بودم و این دوره خیلی کمکم کرد. از صفر تا صد رو یاد گرفتم و الان می‌تونم پروژه‌های واقعی انجام بدم.',
            ],
            [
                'name' => 'رضا',
                'text' => 'کیفیت محتوا عالی بود و پروژه‌های عملی خیلی مفید بودن. پیشنهاد می‌کنم به همه کسایی که می‌خوان یاد بگیرن.',
            ],
        ];
    }

    public static function blogs(): array
    {
        return [
            ['title' => 'آموزش هوش مصنوعی برای کسب‌وکارها', 'tag' => 'جدید'],
            ['title' => 'چطور از هوش مصنوعی پول بسازیم؟', 'tag' => 'محبوب'],
            ['title' => 'راهنمای ابزارهای هوشکس', 'tag' => 'جدید'],
            ['title' => 'شروع یادگیری دیتاساینس', 'tag' => 'منتخب'],
            ['title' => 'بهترین روش‌های استفاده از ChatGPT', 'tag' => 'جدید'],
            ['title' => 'هوش مصنوعی در طراحی گرافیک', 'tag' => 'محبوب'],
            ['title' => 'راهنمای کامل Midjourney', 'tag' => 'جدید'],
            ['title' => 'هوش مصنوعی و آینده کار', 'tag' => 'منتخب'],
            ['title' => 'ابزارهای رایگان هوش مصنوعی', 'tag' => 'جدید'],
            ['title' => 'پرامپت‌نویسی حرفه‌ای', 'tag' => 'محبوب'],
        ];
    }

    /**
     * Get popular/published courses for home page
     */
    public static function courses()
    {
        try {
            $courses = Course::query()
                ->with('teacher')
                ->where('status', CourseStatus::Published)
                ->whereNotNull('published_at')
                ->orderByDesc('is_featured')
                ->orderByDesc('published_at')
                ->limit(10)
                ->get();
            
            // If no courses found, return mock data for development
            if ($courses->isEmpty()) {
                return static::mockCourses();
            }
            
            return $courses;
        } catch (\Exception $e) {
            // Return mock data if table doesn't exist yet
            return static::mockCourses();
        }
    }

    /**
     * Mock courses data for development/demo
     * Pixel Perfect from Figma Design
     */
    private static function mockCourses(): \Illuminate\Support\Collection
    {
        return collect([
            [
                'title' => 'درآمد دلاری با هوش مصنوعی',
                'slug' => 'dollar-income-ai',
                'price' => 3000000,
                'sale_price' => null,
                'thumbnail_path' => 'figma-images/images/dolloar-income-vector.png',
                'badge' => 'جدید',
                'instructor' => [
                    'name' => 'حامد قبادی',
                    'role' => 'برنامه نویس و توسعه دهنده Full Stack',
                ],
            ],
            [
                'title' => 'تولید محتوا با هوش مصنوعی',
                'slug' => 'content-creation-ai',
                'price' => 3000000,
                'sale_price' => 3750000,
                'thumbnail_path' => 'figma-images/images/content-ai-vector.png',
                'badge' => '20 درصد تخیف',
                'instructor' => [
                    'name' => 'امید توتونچی',
                    'role' => 'نویسنده و فیلمساز',
                ],
            ],
            [
                'title' => 'پرامیت نویسی مقدماتی',
                'slug' => 'prompt-writing-basics',
                'price' => 3800000,
                'sale_price' => null,
                'thumbnail_path' => 'figma-images/images/prompt-ai-vector.png',
                'badge' => 'جدید',
                'instructor' => [
                    'name' => 'ریا رحیمی',
                    'role' => 'متخصص هوش مصنوعی (AI)',
                ],
            ],
            [
                'title' => 'ساخت استارت آپ با هوش مصنوعی',
                'slug' => 'startup-with-ai',
                'price' => 5000000,
                'sale_price' => null,
                'thumbnail_path' => 'figma-images/images/startup-with-ai-vector.png',
                'badge' => null, // No badge for this course
                'instructor' => [
                    'name' => 'مجتبا یزدان پناه',
                    'role' => 'طراح محصول دیجیتال',
                ],
            ],
        ]);
    }
}

