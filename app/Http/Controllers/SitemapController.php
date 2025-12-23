<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Domains\AiTools\Models\AiTool;
use App\Domains\Blog\Models\News;
use App\Domains\Blog\Models\Post;
use App\Domains\Commerce\Models\Product;
use App\Domains\Core\Models\Career;
use App\Domains\Core\Models\Page;
use App\Domains\Courses\Models\Course;
use App\Domains\Courses\Models\Teacher;
use App\Settings\SeoSettings;
use Illuminate\Http\Response;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;

class SitemapController extends Controller
{
    public function __construct(
        private readonly SeoSettings $seoSettings
    ) {
    }

    /**
     * Generate sitemap index (main sitemap)
     */
    public function index(): Response
    {
        $sitemap = Sitemap::create();

        if ($this->seoSettings->include_ai_tools_in_sitemap) {
            $sitemap->add(Url::create(url('/ai_tool-sitemap.xml')));
        }
        if ($this->seoSettings->include_posts_in_sitemap) {
            $sitemap->add(Url::create(url('/post-sitemap.xml')));
        }
        if ($this->seoSettings->include_news_in_sitemap) {
            $sitemap->add(Url::create(url('/news-sitemap.xml')));
        }
        if ($this->seoSettings->include_courses_in_sitemap) {
            $sitemap->add(Url::create(url('/course-sitemap.xml')));
        }
        if ($this->seoSettings->include_products_in_sitemap) {
            $sitemap->add(Url::create(url('/product-sitemap.xml')));
        }
        if ($this->seoSettings->include_teachers_in_sitemap) {
            $sitemap->add(Url::create(url('/teacher-sitemap.xml')));
        }
        if ($this->seoSettings->include_pages_in_sitemap) {
            $sitemap->add(Url::create(url('/page-sitemap.xml')));
        }
        if ($this->seoSettings->include_careers_in_sitemap) {
            $sitemap->add(Url::create(url('/career-sitemap.xml')));
        }

        return response($sitemap->render(), 200, [
            'Content-Type' => 'application/xml',
        ]);
    }

    /**
     * Generate Posts sitemap
     */
    public function posts(): Response
    {
        if (! $this->seoSettings->include_posts_in_sitemap) {
            abort(404);
        }

        $sitemap = Sitemap::create();

        Post::query()
            ->where('status', \App\Enums\PostStatus::Published)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->get()
            ->each(function (Post $post) use ($sitemap) {
                $url = Url::create(url("/posts/{$post->slug}"))
                    ->setLastModificationDate($post->updated_at)
                    ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
                    ->setPriority(0.8);

                if ($post->thumbnail_path) {
                    $url->addImage(url($post->thumbnail_path), $post->title ?? '');
                }

                $sitemap->add($url);
            });

        return response($sitemap->render(), 200, [
            'Content-Type' => 'application/xml',
        ]);
    }

    /**
     * Generate AI Tools sitemap
     */
    public function aiTools(): Response
    {
        if (! $this->seoSettings->include_ai_tools_in_sitemap) {
            abort(404);
        }

        $sitemap = Sitemap::create();

        AiTool::query()
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->get()
            ->each(function (AiTool $tool) use ($sitemap) {
                $url = Url::create(url("/ai-tools/{$tool->slug}"))
                    ->setLastModificationDate($tool->updated_at)
                    ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
                    ->setPriority(0.8);

                if ($tool->logo_path) {
                    $url->addImage(url($tool->logo_path), $tool->name ?? '');
                }

                $sitemap->add($url);
            });

        return response($sitemap->render(), 200, [
            'Content-Type' => 'application/xml',
        ]);
    }

    /**
     * Generate Courses sitemap
     */
    public function courses(): Response
    {
        if (! $this->seoSettings->include_courses_in_sitemap) {
            abort(404);
        }

        $sitemap = Sitemap::create();

        Course::query()
            ->where('status', \App\Enums\CourseStatus::Published)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->get()
            ->each(function (Course $course) use ($sitemap) {
                $url = Url::create(url("/courses/{$course->slug}"))
                    ->setLastModificationDate($course->updated_at)
                    ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
                    ->setPriority(0.9);

                if ($course->thumbnail_path) {
                    $url->addImage(url($course->thumbnail_path), $course->title ?? '');
                }

                $sitemap->add($url);
            });

        return response($sitemap->render(), 200, [
            'Content-Type' => 'application/xml',
        ]);
    }

    /**
     * Generate Teachers sitemap
     */
    public function teachers(): Response
    {
        if (! $this->seoSettings->include_teachers_in_sitemap) {
            abort(404);
        }

        $sitemap = Sitemap::create();

        Teacher::query()
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->get()
            ->each(function (Teacher $teacher) use ($sitemap) {
                $url = Url::create(url("/teachers/{$teacher->slug}"))
                    ->setLastModificationDate($teacher->updated_at)
                    ->setChangeFrequency(Url::CHANGE_FREQUENCY_MONTHLY)
                    ->setPriority(0.7);

                if ($teacher->avatar_path) {
                    $url->addImage(url($teacher->avatar_path), $teacher->user->name ?? '');
                }

                $sitemap->add($url);
            });

        return response($sitemap->render(), 200, [
            'Content-Type' => 'application/xml',
        ]);
    }

    /**
     * Generate Products sitemap
     */
    public function products(): Response
    {
        if (! $this->seoSettings->include_products_in_sitemap) {
            abort(404);
        }

        $sitemap = Sitemap::create();

        Product::query()
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->get()
            ->each(function (Product $product) use ($sitemap) {
                $url = Url::create(url("/products/{$product->slug}"))
                    ->setLastModificationDate($product->updated_at)
                    ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
                    ->setPriority(0.8);

                if ($product->thumbnail_path) {
                    $url->addImage(url($product->thumbnail_path), $product->title ?? '');
                }

                $sitemap->add($url);
            });

        return response($sitemap->render(), 200, [
            'Content-Type' => 'application/xml',
        ]);
    }

    /**
     * Generate News sitemap
     */
    public function news(): Response
    {
        if (! $this->seoSettings->include_news_in_sitemap) {
            abort(404);
        }

        $sitemap = Sitemap::create();

        News::query()
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->get()
            ->each(function (News $news) use ($sitemap) {
                $url = Url::create(url("/news/{$news->slug}"))
                    ->setLastModificationDate($news->updated_at)
                    ->setChangeFrequency(Url::CHANGE_FREQUENCY_DAILY)
                    ->setPriority(0.9);

                if ($news->thumbnail_path) {
                    $url->addImage(url($news->thumbnail_path), $news->title ?? '');
                }

                $sitemap->add($url);
            });

        return response($sitemap->render(), 200, [
            'Content-Type' => 'application/xml',
        ]);
    }

    /**
     * Generate Pages sitemap
     */
    public function pages(): Response
    {
        if (! $this->seoSettings->include_pages_in_sitemap) {
            abort(404);
        }

        $sitemap = Sitemap::create();

        Page::query()
            ->where('is_published', true)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->get()
            ->each(function (Page $page) use ($sitemap) {
                $sitemap->add(
                    Url::create(url("/{$page->slug}"))
                        ->setLastModificationDate($page->updated_at)
                        ->setChangeFrequency(Url::CHANGE_FREQUENCY_MONTHLY)
                        ->setPriority(0.6)
                );
            });

        return response($sitemap->render(), 200, [
            'Content-Type' => 'application/xml',
        ]);
    }

    /**
     * Generate Careers sitemap
     */
    public function careers(): Response
    {
        if (! $this->seoSettings->include_careers_in_sitemap) {
            abort(404);
        }

        $sitemap = Sitemap::create();

        Career::query()
            ->where('is_active', true)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->where(function ($query) {
                $query->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            })
            ->get()
            ->each(function (Career $career) use ($sitemap) {
                $sitemap->add(
                    Url::create(url("/careers/{$career->slug}"))
                        ->setLastModificationDate($career->updated_at)
                        ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
                        ->setPriority(0.7)
                );
            });

        return response($sitemap->render(), 200, [
            'Content-Type' => 'application/xml',
        ]);
    }
}