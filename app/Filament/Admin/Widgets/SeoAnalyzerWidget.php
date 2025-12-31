<?php

declare(strict_types=1);

namespace App\Filament\Admin\Widgets;

use Filament\Widgets\Widget;

class SeoAnalyzerWidget extends Widget
{
    protected static string $view = 'filament.widgets.seo-analyzer';

    protected array $analysisResults = [];
    protected int $seoScore = 0;
    protected int $readabilityScore = 0;

    public function analyze(?string $title = null, ?string $content = null, ?string $focusKeyword = null, ?string $metaDescription = null, ?string $slug = null): array
    {
        $results = [];
        $score = 100;

        // Title Analysis
        if (empty($title)) {
            $results['title'][] = ['status' => 'error', 'message' => 'عنوان مقاله الزامی است'];
            $score -= 10;
        } else {
            $titleLength = mb_strlen($title);
            if ($titleLength < 30) {
                $results['title'][] = ['status' => 'warning', 'message' => 'عنوان کوتاه است (حداقل 30 کاراکتر توصیه می‌شود)'];
                $score -= 5;
            } elseif ($titleLength > 60) {
                $results['title'][] = ['status' => 'warning', 'message' => 'عنوان بلند است (حداکثر 60 کاراکتر توصیه می‌شود)'];
                $score -= 5;
            } else {
                $results['title'][] = ['status' => 'success', 'message' => 'طول عنوان مناسب است'];
            }

            if ($focusKeyword && stripos($title, $focusKeyword) !== false) {
                $results['title'][] = ['status' => 'success', 'message' => 'کلمه کلیدی اصلی در عنوان وجود دارد'];
                
                // Check if Focus Keyword is near the beginning of title
                $firstPart = mb_substr($title, 0, mb_strlen($title) / 2);
                if (stripos($firstPart, $focusKeyword) !== false) {
                    $results['title'][] = ['status' => 'success', 'message' => 'کلمه کلیدی در ابتدای عنوان قرار دارد'];
                } else {
                    $results['title'][] = ['status' => 'warning', 'message' => 'کلمه کلیدی را نزدیک ابتدای عنوان قرار دهید'];
                    $score -= 3;
                }
            } elseif ($focusKeyword) {
                $results['title'][] = ['status' => 'error', 'message' => 'کلمه کلیدی اصلی در عنوان یافت نشد'];
                $score -= 15;
            }
            
            // Check for number in title (improves CTR)
            if (preg_match('/\d+/', $title)) {
                $results['title'][] = ['status' => 'success', 'message' => 'عنوان شامل عدد است (بهبود نرخ کلیک)'];
            } else {
                $results['title'][] = ['status' => 'info', 'message' => 'افزودن عدد به عنوان (مثلاً "10 راه") می‌تواند CTR را بهبود بخشد'];
            }
        }

        // Content Analysis
        if (empty($content)) {
            $results['content'][] = ['status' => 'error', 'message' => 'محتوای مقاله الزامی است'];
            $score -= 20;
        } else {
            $wordCount = str_word_count(strip_tags($content));
            
            if ($wordCount < 300) {
                $results['content'][] = ['status' => 'error', 'message' => "محتوا بسیار کوتاه است ({$wordCount} کلمه) - حداقل 600 کلمه توصیه می‌شود"];
                $score -= 15;
            } elseif ($wordCount < 600) {
                $results['content'][] = ['status' => 'warning', 'message' => "محتوا کوتاه است ({$wordCount} کلمه) - محدوده ایده‌آل 600-2500 کلمه است"];
                $score -= 10;
            } elseif ($wordCount >= 600 && $wordCount <= 2500) {
                $results['content'][] = ['status' => 'success', 'message' => "تعداد کلمات در محدوده ایده‌آل است ({$wordCount} کلمه)"];
            } elseif ($wordCount > 2500) {
                $results['content'][] = ['status' => 'info', 'message' => "محتوا بسیار بلند است ({$wordCount} کلمه) - ممکن است نیاز به تقسیم به چند مقاله داشته باشد"];
            }

            // Focus Keyword Density (1.5% - 4.5% per Rank Math)
            if ($focusKeyword) {
                $keywordCount = substr_count(strtolower($content), strtolower($focusKeyword));
                $density = $wordCount > 0 ? ($keywordCount / $wordCount) * 100 : 0;

                if ($density === 0) {
                    $results['content'][] = ['status' => 'error', 'message' => 'کلمه کلیدی در محتوا یافت نشد'];
                    $score -= 20;
                } elseif ($density < 1.5) {
                    $results['content'][] = ['status' => 'warning', 'message' => sprintf('تراکم کلمه کلیدی کم است (%.2f%%) - بین 1.5%% تا 4.5%% توصیه می‌شود', $density)];
                    $score -= 10;
                } elseif ($density > 4.5) {
                    $results['content'][] = ['status' => 'warning', 'message' => sprintf('تراکم کلمه کلیدی زیاد است (%.2f%%) - ممکن است به عنوان spam شناخته شود', $density)];
                    $score -= 10;
                } else {
                    $results['content'][] = ['status' => 'success', 'message' => sprintf('تراکم کلمه کلیدی مناسب است (%.2f%%) - محدوده ایده‌آل 1.5%% تا 4.5%%', $density)];
                }
            }

            // Check for Focus Keyword at the beginning of content
            if ($focusKeyword) {
                $firstWords = mb_substr(strip_tags($content), 0, 100);
                if (stripos($firstWords, $focusKeyword) !== false) {
                    $results['content'][] = ['status' => 'success', 'message' => 'کلمه کلیدی در ابتدای محتوا استفاده شده است'];
                } else {
                    $results['content'][] = ['status' => 'warning', 'message' => 'کلمه کلیدی را در ابتدای محتوا قرار دهید'];
                    $score -= 5;
                }
            }

            // Check for headings
            if (preg_match('/<h[1-6]/', $content)) {
                $results['content'][] = ['status' => 'success', 'message' => 'از سرتیترها استفاده شده است'];
                
                // Check for Focus Keyword in subheadings (H2, H3, H4)
                if ($focusKeyword) {
                    if (preg_match('/<h[2-4][^>]*>.*?' . preg_quote($focusKeyword, '/') . '.*?<\/h[2-4]>/i', $content)) {
                        $results['content'][] = ['status' => 'success', 'message' => 'کلمه کلیدی در سرتیترها (H2, H3, H4) استفاده شده است'];
                    } else {
                        $results['content'][] = ['status' => 'warning', 'message' => 'کلمه کلیدی را در حداقل یک سرتیتر (H2, H3, H4) استفاده کنید'];
                        $score -= 5;
                    }
                }
            } else {
                $results['content'][] = ['status' => 'warning', 'message' => 'سرتیتر (H2, H3) اضافه کنید'];
                $score -= 5;
            }

            // Check for images and alt text with Focus Keyword
            if (preg_match('/<img/', $content)) {
                $results['content'][] = ['status' => 'success', 'message' => 'تصویر در محتوا وجود دارد'];
                
                if ($focusKeyword) {
                    if (preg_match('/<img[^>]+alt=["\'].*?' . preg_quote($focusKeyword, '/') . '.*?["\'][^>]*>/i', $content)) {
                        $results['content'][] = ['status' => 'success', 'message' => 'تصویری با alt text شامل کلمه کلیدی وجود دارد'];
                    } else {
                        $results['content'][] = ['status' => 'warning', 'message' => 'یک تصویر با alt text شامل کلمه کلیدی اضافه کنید'];
                        $score -= 5;
                    }
                }
            } else {
                $results['content'][] = ['status' => 'warning', 'message' => 'افزودن تصویر به محتوا توصیه می‌شود'];
                $score -= 3;
            }

            // Check for external links
            if (preg_match('/<a[^>]+href=["\']https?:\/\/(?!' . preg_quote(parse_url(config('app.url'), PHP_URL_HOST) ?? '', '/') . ')/i', $content)) {
                $results['content'][] = ['status' => 'success', 'message' => 'لینک خارجی به منابع مرجع اضافه شده است'];
            } else {
                $results['content'][] = ['status' => 'warning', 'message' => 'به منابع خارجی معتبر لینک دهید'];
                $score -= 5;
            }
            
            // Check for internal links
            $internalLinkCount = preg_match_all('/<a[^>]+href=["\'](?:\/|' . preg_quote(config('app.url'), '/') . ')/i', $content);
            if ($internalLinkCount > 0) {
                $results['content'][] = ['status' => 'success', 'message' => 'لینک‌های داخلی در محتوا وجود دارد'];
                
                if ($internalLinkCount >= 3) {
                    $results['content'][] = ['status' => 'success', 'message' => sprintf('تعداد کافی لینک داخلی وجود دارد (%d لینک)', $internalLinkCount)];
                }
            } else {
                $results['content'][] = ['status' => 'warning', 'message' => 'لینک داخلی به مقالات مرتبط اضافه کنید'];
                $score -= 5;
            }
            
            // Check for Table of Contents
            if (preg_match('/فهرست|table.*of.*content|جدول.*محتوا/iu', $content)) {
                $results['content'][] = ['status' => 'success', 'message' => 'فهرست مطالب (Table of Contents) وجود دارد'];
            } else {
                $results['content'][] = ['status' => 'info', 'message' => 'افزودن فهرست مطالب برای محتوای بلند توصیه می‌شود'];
            }
        }

        // Meta Description Analysis
        if (empty($metaDescription)) {
            $results['meta'][] = ['status' => 'warning', 'message' => 'توضیحات متا (Meta Description) وارد نشده است'];
            $score -= 10;
        } else {
            $metaLength = mb_strlen($metaDescription);
            if ($metaLength < 120) {
                $results['meta'][] = ['status' => 'warning', 'message' => 'توضیحات متا کوتاه است (حداقل 120 کاراکتر توصیه می‌شود)'];
                $score -= 5;
            } elseif ($metaLength > 160) {
                $results['meta'][] = ['status' => 'warning', 'message' => 'توضیحات متا بلند است (حداکثر 160 کاراکتر)'];
                $score -= 5;
            } else {
                $results['meta'][] = ['status' => 'success', 'message' => 'طول توضیحات متا مناسب است'];
            }

            if ($focusKeyword && stripos($metaDescription, $focusKeyword) !== false) {
                $results['meta'][] = ['status' => 'success', 'message' => 'کلمه کلیدی در توضیحات متا وجود دارد'];
            } elseif ($focusKeyword) {
                $results['meta'][] = ['status' => 'warning', 'message' => 'کلمه کلیدی در توضیحات متا یافت نشد'];
                $score -= 5;
            }
        }

        // Focus Keyword Analysis
        if (empty($focusKeyword)) {
            $results['keyword'][] = ['status' => 'warning', 'message' => 'کلمه کلیدی اصلی (Focus Keyword) انتخاب نشده است'];
            $score -= 10;
        } else {
            $results['keyword'][] = ['status' => 'success', 'message' => 'کلمه کلیدی اصلی: ' . $focusKeyword];
            
            // Check Focus Keyword in URL/Slug
            if ($slug) {
                $normalizedSlug = str_replace(['-', '_'], ' ', $slug);
                if (stripos($normalizedSlug, $focusKeyword) !== false) {
                    $results['keyword'][] = ['status' => 'success', 'message' => 'کلمه کلیدی در URL وجود دارد'];
                } else {
                    $results['keyword'][] = ['status' => 'warning', 'message' => 'کلمه کلیدی را در URL (آدرس مقاله) استفاده کنید'];
                    $score -= 5;
                }
            }
        }

        $this->seoScore = max(0, min(100, $score));
        $this->analysisResults = $results;

        return [
            'score' => $this->seoScore,
            'results' => $this->analysisResults,
        ];
    }

    public function analyzeReadability(?string $content = null): array
    {
        if (empty($content)) {
            return [
                'score' => 0,
                'results' => [['status' => 'error', 'message' => 'محتوایی برای تحلیل وجود ندارد']],
            ];
        }

        $results = [];
        $score = 100;
        $text = strip_tags($content);
        $sentences = preg_split('/[.!?]+/', $text, -1, PREG_SPLIT_NO_EMPTY);
        $words = str_word_count($text);
        $sentenceCount = count($sentences);

        // Average sentence length
        $avgSentenceLength = $sentenceCount > 0 ? $words / $sentenceCount : 0;
        
        if ($avgSentenceLength > 25) {
            $results[] = ['status' => 'warning', 'message' => sprintf('جملات خیلی بلند هستند (میانگین %.1f کلمه) - کمتر از 20 کلمه توصیه می‌شود', $avgSentenceLength)];
            $score -= 15;
        } elseif ($avgSentenceLength > 20) {
            $results[] = ['status' => 'warning', 'message' => sprintf('طول جملات مناسب است اما می‌توان کوتاه‌تر کرد (میانگین %.1f کلمه)', $avgSentenceLength)];
            $score -= 5;
        } else {
            $results[] = ['status' => 'success', 'message' => sprintf('طول جملات مناسب است (میانگین %.1f کلمه)', $avgSentenceLength)];
        }

        // Paragraph check
        $paragraphs = explode("\n", trim($text));
        $paragraphs = array_filter($paragraphs, fn($p) => !empty(trim($p)));
        
        if (count($paragraphs) < 3) {
            $results[] = ['status' => 'warning', 'message' => 'متن را به پاراگراف‌های کوچک‌تر تقسیم کنید'];
            $score -= 10;
        } else {
            $results[] = ['status' => 'success', 'message' => 'تعداد پاراگراف‌ها مناسب است'];
        }

        // Transition words (basic check for Persian)
        $transitionWords = ['همچنین', 'علاوه بر این', 'بنابراین', 'در نتیجه', 'از طرفی', 'به علاوه', 'در مقابل'];
        $hasTransitions = false;
        foreach ($transitionWords as $word) {
            if (stripos($text, $word) !== false) {
                $hasTransitions = true;
                break;
            }
        }
        
        if ($hasTransitions) {
            $results[] = ['status' => 'success', 'message' => 'از کلمات انتقالی استفاده شده است'];
        } else {
            $results[] = ['status' => 'warning', 'message' => 'استفاده از کلمات انتقالی (همچنین، بنابراین، ...) توصیه می‌شود'];
            $score -= 10;
        }

        // Passive voice (simplified for Persian)
        $passiveCount = preg_match_all('/شده|گردیده|گشته/', $text);
        if ($passiveCount > $sentenceCount * 0.3) {
            $results[] = ['status' => 'warning', 'message' => 'استفاده زیاد از مجهول - سعی کنید از فعل معلوم استفاده کنید'];
            $score -= 10;
        } else {
            $results[] = ['status' => 'success', 'message' => 'تعادل خوبی بین مجهول و معلوم وجود دارد'];
        }

        $this->readabilityScore = max(0, min(100, $score));

        return [
            'score' => $this->readabilityScore,
            'results' => $results,
        ];
    }

    public function getScoreColor(int $score): string
    {
        if ($score >= 80) {
            return '#10b981'; // green
        } elseif ($score >= 50) {
            return '#f59e0b'; // orange
        } else {
            return '#ef4444'; // red
        }
    }

    public function getScoreLabel(int $score): string
    {
        if ($score >= 80) {
            return 'عالی';
        } elseif ($score >= 50) {
            return 'قابل قبول';
        } else {
            return 'نیاز به بهبود';
        }
    }
}
