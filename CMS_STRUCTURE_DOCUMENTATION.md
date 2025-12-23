# مستندات کامل ساختار CMS/Panel مدیریت - هوشکس

## 📋 فهرست مطالب
1. [معماری کلی](#معماری-کلی)
2. [دامین‌ها و مدل‌ها](#دامین‌ها-و-مدل‌ها)
3. [پنل مدیریت Filament](#پنل-مدیریت-filament)
4. [پنل کاربری (Student Dashboard)](#پنل-کاربری-student-dashboard)
5. [سیستم تنظیمات](#سیستم-تنظیمات)
6. [سیستم منوی ناوبری](#سیستم-منوی-ناوبری)
7. [سیستم تعاملات (Comments & Reviews)](#سیستم-تعاملات-comments--reviews)
8. [سیستم Analytics و Click Tracking](#سیستم-analytics-و-click-tracking)
9. [SEO و Sitemap](#seo-و-sitemap)
10. [سیستم تغییر مسیر و 404](#سیستم-تغییر-مسیر-و-404)
11. [Commerce و پرداخت](#commerce-و-پرداخت)
12. [Authentication و Authorization](#authentication-و-authorization)

---

## معماری کلی

پروژه بر اساس **Modular Monolith** با Laravel 12 و Filament v3 طراحی شده است.

### ساختار دامین‌ها (Domain-Driven Design):
```
app/Domains/
├── AiTools/          # ابزارهای هوش مصنوعی
├── Auth/             # احراز هویت
├── Blog/             # بلاگ و اخبار
├── Commerce/         # تجارت و فروش
├── Core/             # هسته اصلی (صفحات، دسته‌بندی‌ها، تغییر مسیرها، منوها، کلیک‌ها)
└── Courses/          # دوره‌های آموزشی

app/Interactions/     # سیستم تعاملات (نظرات و نقد و بررسی)
```

---

## دامین‌ها و مدل‌ها

### 1. دامین AiTools

**مدل:** `App\Domains\AiTools\Models\AiTool`

**فیلدها:**
- `name` (string)
- `slug` (string, unique)
- `short_description` (text)
- `content` (longtext)
- `website_url`, `affiliate_url`, `demo_url`
- `logo_path`
- `pricing_type` (Enum: Free, Freemium, Paid)
- `price` (integer)
- `rating` (float)
- `users_count`, `success_rate`, `response_time`
- `languages` (JSON array)
- `features` (JSON array)
- `company`
- `is_verified`, `is_featured`, `has_course`
- `related_course_id`
- `published_at`

**Relationships:**
- `categories()` - MorphToMany Category
- `relatedCourse()` - BelongsTo Course
- `clicks()` - HasMany Click (Click Tracking)
- `bookmarkers()` - BelongsToMany User (Bookmarks)
- `reviews()` - MorphMany Review (می‌توان اضافه کرد)

**Traits:**
- `HasSEO` - SEO support
- `Searchable` - Meilisearch integration
- `SoftDeletes`

---

### 2. دامین Courses

#### مدل Course
**مسیر:** `App\Domains\Courses\Models\Course`

**فیلدها:**
- `teacher_id`
- `title`, `slug`
- `short_description`, `content`
- `price`, `sale_price`, `sku`
- `intro_video_url`, `thumbnail_path`
- `duration`
- `status` (Enum: Draft, Published, Archived)
- `is_featured`
- `published_at`

**Relationships:**
- `teacher()` - BelongsTo User
- `chapters()` - HasMany Chapter
- `enrollments()` - HasMany Enrollment

#### مدل Chapter
**فیلدها:** `course_id`, `title`, `sort_order`
**Relationships:** `course()`, `lessons()`

#### مدل Lesson
**فیلدها:** `chapter_id`, `title`, `video_url`, `duration`, `is_free_preview`, `sort_order`, `content`
**Relationships:** `chapter()`

#### مدل Enrollment
**فیلدها:** `user_id`, `course_id`, `enrolled_at`, `expires_at`
**Relationships:** `user()`, `course()`

#### مدل Teacher
**فیلدها:** `user_id`, `slug`, `bio`, `specialty`, `social_links` (JSON), `avatar_path`, `is_featured`, `published_at`
**Relationships:** `user()`, `courses()`

**Traits:** `HasSEO`

---

### 3. دامین Blog

#### مدل Post
**مسیر:** `App\Domains\Blog\Models\Post`

**فیلدها:**
- `author_id`
- `type` (Enum: Article, News)
- `title`, `slug`
- `excerpt`, `content`
- `thumbnail_path`
- `status` (Enum: Draft, Published, Scheduled)
- `published_at`

**Relationships:**
- `author()` - BelongsTo User
- `categories()` - MorphToMany Category

**Traits:** `HasSEO`, `Searchable`, `SoftDeletes`

#### مدل News
**مسیر:** `App\Domains\Blog\Models\News`

**فیلدها:** مشابه Post با `status` (string)
**Relationships:** `author()`, `categories()`
**Traits:** `HasSEO`, `Searchable`, `SoftDeletes`, `HasTags`

---

### 4. دامین Commerce

#### مدل Product
**مسیر:** `App\Domains\Commerce\Models\Product`

**فیلدها:**
- `title`, `slug`
- `price`, `sale_price`, `sku`
- `short_description`, `description`
- `is_digital`, `file_url`
- `thumbnail_path`
- `stock_status`, `stock_quantity`
- `is_featured`
- `published_at`

**Relationships:** `categories()` - MorphToMany Category
**Traits:** `HasSEO`, `Searchable`, `SoftDeletes`, `HasTags`

#### مدل Order
**مسیر:** `App\Domains\Commerce\Models\Order`

**فیلدها:**
- `user_id`
- `status` (Enum: Pending, Paid, Failed, Cancelled)
- `total_amount`
- `gateway`, `gateway_ref_id`, `transaction_id`
- `billing_address` (JSON)
- `ip_address`

**Relationships:**
- `user()` - BelongsTo User
- `items()` - HasMany OrderItem

#### مدل OrderItem
**فیلدها:** `order_id`, `orderable_type`, `orderable_id`, `price`, `quantity`
**Relationships:** `order()`, `orderable()` - MorphTo (Course/Product)

---

### 5. دامین Core

#### مدل Page
**مسیر:** `App\Domains\Core\Models\Page`

**فیلدها:**
- `title`, `slug`
- `excerpt`
- `content_blocks` (JSON)
- `template`
- `is_published`
- `published_at`

**Traits:** `HasSEO`

#### مدل Category
**مسیر:** `App\Domains\Core\Models\Category`

**فیلدها:** `name`, `slug`, `type` (default: 'ai_tool')
**Relationships:** Polymorphic many-to-many با AiTool, Post, Product

#### مدل Redirect
**مسیر:** `App\Domains\Core\Models\Redirect`

**فیلدها:**
- `source_url` (unique, indexed)
- `target_url`
- `status_code` (301, 302, 307)
- `hit_count`
- `last_accessed_at`

**Methods:** `recordHit()` - ثبت بازدید

#### مدل NotFoundLog
**مسیر:** `App\Domains\Core\Models\NotFoundLog`

**فیلدها:**
- `url`
- `referer`, `ip_address`, `user_agent`
- `hit_count`
- `first_seen_at`, `last_seen_at`

#### مدل Career
**مسیر:** `App\Domains\Core\Models\Career`

**فیلدها:**
- `title`, `slug`
- `location`, `type`
- `short_description`, `description`
- `application_link`
- `is_active`
- `published_at`, `expires_at`

**Traits:** `HasSEO`, `HasTags`

#### مدل NavigationItem
**مسیر:** `App\Domains\Core\Models\NavigationItem`

**فیلدها:**
- `menu_location` (header, footer)
- `label`
- `url`, `route` (یکی از دو مورد)
- `icon`
- `sort_order`
- `parent_id` (برای منوهای تودرتو)
- `is_active`
- `open_in_new_tab`

**Relationships:**
- `parent()` - BelongsTo NavigationItem
- `children()` - HasMany NavigationItem

**Methods:**
- `getHrefAttribute()` - دریافت URL نهایی
- `getMenu(string $location)` - استاتیک: دریافت منوی یک موقعیت

#### مدل Click
**مسیر:** `App\Domains\Core\Models\Click`

**فیلدها:**
- `ai_tool_id`
- `ip_address`, `user_agent`, `referer`
- `user_id` (nullable)
- `clicked_at`

**Relationships:**
- `aiTool()` - BelongsTo AiTool
- `user()` - BelongsTo User

---

### 7. دامین Interactions

#### مدل Comment
**مسیر:** `App\Interactions\Comment`

**فیلدها:**
- `user_id`
- `commentable_type`, `commentable_id` (Polymorphic: Post, Lesson)
- `parent_id` (برای پاسخ به نظرات)
- `body`
- `status` (pending, approved, spam, trash)
- `ip_address`, `user_agent`

**Relationships:**
- `user()` - BelongsTo User
- `commentable()` - MorphTo
- `parent()` - BelongsTo Comment
- `replies()` - HasMany Comment

**Methods:**
- `isApproved()` - بررسی وضعیت تایید

**Traits:** `SoftDeletes`

#### مدل Review
**مسیر:** `App\Interactions\Review`

**فیلدها:**
- `user_id`
- `reviewable_type`, `reviewable_id` (Polymorphic: AiTool, Course)
- `rating` (1-5)
- `title`, `body`
- `status` (pending, approved, spam)

**Relationships:**
- `user()` - BelongsTo User
- `reviewable()` - MorphTo

**Methods:**
- `isApproved()` - بررسی وضعیت تایید

**Traits:** `SoftDeletes`

---

### 6. دامین Auth

#### مدل User
**مسیر:** `App\Domains\Auth\Models\User`

**فیلدها:**
- `username`, `email`, `mobile`
- `name`
- `legacy_password` (برای migration از WordPress)
- `bio`, `avatar_path`
- `social_links` (JSON)
- `role` (Enum: Admin, Student, Teacher)

---

## Enums

**مسیر:** `app/Enums/`

1. **CourseStatus:** Draft, Published, Archived
2. **OrderStatus:** Pending, Paid, Failed, Cancelled
3. **PostStatus:** Draft, Published, Scheduled
4. **PostType:** Article, News
5. **PricingType:** Free, Freemium, Paid
6. **UserRole:** Admin, Student, Teacher

---

## پنل مدیریت Filament

**مسیر پنل:** `/admin`

### Resources (منابع):

#### 1. AiToolResource
- **Create/Edit/List:** ✅
- **تب‌ها:** General, Content, Taxonomy, SEO
- **ویژگی‌ها:** قیمت‌گذاری، دسته‌بندی، لوگو، تگ‌ها

#### 2. CourseResource
- **Create/Edit/List:** ✅
- **تب‌ها:** General, Content, Pricing, SEO
- **Relations:** Teacher, Chapters, Lessons

#### 3. PostResource
- **Create/Edit/List:** ✅
- **تب‌ها:** General, Content, Taxonomy, SEO
- **فیلترها:** Type, Status, Author

#### 4. NewsResource
- **Create/Edit/List/View:** ✅
- مشابه PostResource

#### 5. ProductResource
- **Create/Edit/List/View:** ✅
- **ویژگی‌ها:** قیمت، موجودی، فایل دیجیتال

#### 6. TeacherResource
- **Create/Edit/List/View:** ✅
- **Relations:** User, Courses

#### 7. CareerResource
- **Create/Edit/List/View:** ✅
- **فیلدها:** موقعیت شغلی، تاریخ انقضا

#### 8. PageResource
- **Create/Edit/List/View:** ✅
- **ویژگی:** Content Blocks (JSON Builder)

#### 9. RedirectResource
- **Create/Edit/List:** ✅
- **Actions:** Import CSV
- **Navigation Group:** SEO

#### 10. NotFoundLogResource
- **Manage:** ✅ (فقط مشاهده، بدون ایجاد دستی)
- **Actions:** Create Redirect from 404 log
- **Navigation Group:** SEO

#### 11. NavigationItemResource
- **Create/Edit/List:** ✅
- **ویژگی‌ها:** مدیریت منوهای Header و Footer
- **Navigation Group:** تنظیمات

#### 12. CommentResource
- **List/Edit:** ✅
- **Actions:** Approve, Spam, Delete
- **Bulk Actions:** Approve Selected
- **Navigation Group:** تعاملات

#### 13. ReviewResource
- **List/Edit:** ✅
- **Actions:** Approve, Delete
- **Bulk Actions:** Approve Selected
- **Navigation Group:** تعاملات

---

### Settings Pages (صفحات تنظیمات):

#### 1. GeneralSettings (`/admin/general-settings`)
**بخش‌ها:**
- **هویت سایت:** نام سایت، شعار، لوگو، فاوآیکن
- **اطلاعات تماس:** تلفن، ایمیل، آدرس
- **اسکریپت‌ها:** Header Scripts (Analytics, GTM), Footer Scripts (Chat widgets)
- **شبکه‌های اجتماعی:** Repeater برای پروفایل‌های اجتماعی

#### 2. SeoSettings (`/admin/seo-settings`)
**بخش‌ها:**
- **پیش‌فرض‌های عنوان:** جداکننده، پسوند
- **کنترل ایندکس‌گذاری:** noindex برای tags, categories, search
- **پیش‌فرض Schema Type:** برای هر نوع محتوا (AiTools, Posts, Courses, Products)
- **کنترل Sitemap:** Toggle برای شامل/حذف هر نوع محتوا از sitemap

---

## پنل کاربری (Student Dashboard)

**مسیر پنل:** `/app`
**Provider:** `App\Providers\Filament\AppPanelProvider`

### صفحات:

#### 1. MyCourses
- **لیست دوره‌های ثبت‌نام شده**
- نمایش با Table: عنوان، تصویر، تاریخ ثبت‌نام، تاریخ انقضا
- Action: مشاهده دوره

#### 2. OrderHistory
- **تاریخچه سفارش‌ها**
- نمایش: شماره سفارش، وضعیت، مبلغ، تاریخ
- Action: دانلود فاکتور (TODO: PDF Generation)

#### 3. Bookmarks
- **نشان‌گذاری‌های AI Tools**
- نمایش: لوگو، نام، نوع قیمت
- Action: حذف از نشان‌گذاری‌ها

#### 4. Profile
- **ویرایش پروفایل**
- بخش‌ها:
  - اطلاعات شخصی: نام، ایمیل، موبایل، بیوگرافی، آواتار
  - تغییر رمز عبور: رمز فعلی، رمز جدید

---

## سیستم تنظیمات

### GeneralSettings
**مسیر:** `App\Settings\GeneralSettings`

**Properties:**
```php
public string $site_name;
public ?string $tagline;
public ?string $logo_path;
public ?string $favicon_path;
public ?string $phone;
public ?string $email;
public ?string $address;
public ?string $header_scripts;
public ?string $footer_scripts;
public ?array $social_profiles;
```

**استفاده در View:**
- `$settings->site_name` - نام سایت
- `$settings->favicon_path` - فاوآیکن
- `$settings->header_scripts` - اسکریپت‌های هدر
- `$settings->footer_scripts` - اسکریپت‌های فوتر

---

### SeoSettings
**مسیر:** `App\Settings\SeoSettings`

**Properties:**
```php
// Title defaults
public string $title_separator;
public ?string $title_suffix;

// Indexing control
public bool $noindex_tags;
public bool $noindex_categories;
public bool $noindex_search;

// Schema defaults
public ?string $default_schema_ai_tools;
public ?string $default_schema_posts;
public ?string $default_schema_courses;
public ?string $default_schema_products;

// Sitemap control
public bool $include_ai_tools_in_sitemap;
public bool $include_posts_in_sitemap;
public bool $include_news_in_sitemap;
public bool $include_courses_in_sitemap;
public bool $include_products_in_sitemap;
public bool $include_teachers_in_sitemap;
public bool $include_pages_in_sitemap;
public bool $include_careers_in_sitemap;
```

---

## سیستم منوی ناوبری

**وضعیت فعلی:** ✅ **پیاده‌سازی کامل شده**

### NavigationItem Model
**مسیر:** `App\Domains\Core\Models\NavigationItem`

**فیلدها:**
- `menu_location` (header, footer)
- `label`, `url`, `route`, `icon`
- `sort_order`, `parent_id`
- `is_active`, `open_in_new_tab`

**Relationships:**
- `parent()` - BelongsTo NavigationItem
- `children()` - HasMany NavigationItem (ordered by sort_order)

**Methods:**
- `getHrefAttribute()` - دریافت URL نهایی (route یا url)
- `getMenu(string $location)` - استاتیک: دریافت منوی فعال یک موقعیت

### Filament Resource
**NavigationItemResource** - کامل با Create/Edit/List
- Navigation Group: تنظیمات
- فیلتر بر اساس `menu_location` (header/footer)
- Actions: Edit, Delete
- Sortable by `sort_order`

### Frontend Integration

**ViewComposer:** `App\Http\ViewComposers\LayoutComposer`
- ✅ `$headerMenu` - منوی Header (injected)
- ✅ `$footerMenu` - منوی Footer (injected)

**Layout Blade:** `resources/views/components/layouts/app.blade.php`
- ✅ Header navigation از `$headerMenu` استفاده می‌کند
- ✅ Fallback به منوی پیش‌فرض در صورت خالی بودن
- ⚠️ Footer navigation هنوز static است (نیاز به به‌روزرسانی)

**استفاده در Header (پیاده‌سازی شده):**
```blade
@if(isset($headerMenu) && $headerMenu->isNotEmpty())
    @foreach($headerMenu as $item)
        <a href="{{ $item->href }}" 
           class="text-sm font-medium text-text-primary hover:text-primary-600"
           {{ $item->open_in_new_tab ? 'target="_blank" rel="noopener noreferrer"' : '' }}>
            {{ $item->label }}
        </a>
    @endforeach
@endif
```

**برای Footer (نیاز به پیاده‌سازی):**
```blade
@if(isset($footerMenu) && $footerMenu->isNotEmpty())
    @foreach($footerMenu as $item)
        <a href="{{ $item->href }}" 
           {{ $item->open_in_new_tab ? 'target="_blank"' : '' }}>
            {{ $item->label }}
        </a>
    @endforeach
@endif
```

**نکته:** منوهای تودرتو (Nested) در backend پشتیبانی می‌شوند اما frontend فعلاً فقط flat نمایش می‌دهد. در صورت نیاز می‌توان کامپوننت Blade برای dropdown menus اضافه کرد.

---

## سیستم تعاملات (Comments & Reviews)

### Comment System

**مدل:** `App\Interactions\Comment`
- Polymorphic: قابل اتصال به Post, Lesson, و سایر مدل‌ها
- پشتیبانی از پاسخ‌های تودرتو (Nested Replies)
- وضعیت‌ها: pending, approved, spam, trash

**Filament Resource:** `CommentResource`
- Actions: Approve, Spam, Delete
- Bulk Actions: Approve Selected

**Relationships (برای اضافه کردن به مدل‌ها):**
```php
// در Post Model:
public function comments(): MorphMany
{
    return $this->morphMany(Comment::class, 'commentable');
}
```

### Review System

**مدل:** `App\Interactions\Review`
- Polymorphic: قابل اتصال به AiTool, Course
- امتیاز: 1 تا 5 ستاره
- وضعیت‌ها: pending, approved, spam

**Filament Resource:** `ReviewResource`
- Actions: Approve, Delete
- Bulk Actions: Approve Selected

**Relationships (برای اضافه کردن به مدل‌ها):**
```php
// در AiTool Model:
public function reviews(): MorphMany
{
    return $this->morphMany(Review::class, 'reviewable');
}

public function averageRating(): float
{
    return $this->reviews()
        ->where('status', 'approved')
        ->avg('rating') ?? 0;
}
```

---

## سیستم Analytics و Click Tracking

### ClickTracker Service
**مسیر:** `app/Domains/Core/Services/ClickTracker.php`

**Methods:**
- `track(AiTool $aiTool, Request $request)` - ثبت کلیک
- `getClickCount(int $aiToolId)` - دریافت تعداد کلیک

### ClickController
**مسیر:** `app/Http/Controllers/Core/ClickController.php`
**Route:** `/go/{slug}` - `click.track`

**عملکرد:**
1. دریافت slug ابزار AI
2. ثبت کلیک در دیتابیس
3. Redirect به `affiliate_url` یا `website_url`

### Click Model
**مسیر:** `App\Domains\Core\Models\Click`

**ذخیره اطلاعات:**
- ai_tool_id
- ip_address, user_agent, referer
- user_id (اگر کاربر لاگین باشد)
- clicked_at

### Integration در AiToolResource
- ستون `click_count` در جدول (counts relationship)
- نمایش تعداد کلیک‌ها در Admin Panel

**استفاده در Frontend:**
```blade
<a href="{{ route('click.track', $aiTool->slug) }}">
    بازدید از سایت
</a>
```

---

## SEO و Sitemap

### SEO Integration

همه مدل‌های محتوا از Trait `HasSEO` استفاده می‌کنند:
- AiTool, Course, Post, News, Product, Teacher, Career, Page

**استفاده در Blade:**
```blade
{!! seo()->for($page ?? null) !!}
```

### Sitemap Controller

**مسیر:** `App\Http\Controllers\SitemapController`

**Routes:**
- `/sitemap_index.xml` - Sitemap Index
- `/post-sitemap.xml` - Posts
- `/ai_tool-sitemap.xml` - AI Tools
- `/course-sitemap.xml` - Courses
- `/teacher-sitemap.xml` - Teachers
- `/product-sitemap.xml` - Products
- `/news-sitemap.xml` - News
- `/page-sitemap.xml` - Pages
- `/career-sitemap.xml` - Careers

**ویژگی‌ها:**
- بررسی تنظیمات SeoSettings قبل از تولید
- پشتیبانی از تصاویر در sitemap (Google Image Search)
- استفاده از `Spatie\Sitemap`

---

## سیستم تغییر مسیر و 404

### Middleware: HandleRedirections

**مسیر:** `App\Http\Middleware\HandleRedirections`

**عملکرد:**
1. بررسی URL درخواست در جدول `redirects`
2. در صورت وجود: ثبت بازدید و تغییر مسیر
3. در صورت 404: ثبت در `not_found_logs`

**ثبت شده در:** `bootstrap/app.php` (web middleware group)

### Redirect Resource

**ویژگی‌ها:**
- ایجاد/ویرایش/حذف تغییر مسیرها
- Import CSV برای import دسته‌ای
- نمایش آمار بازدیدها

### NotFoundLog Resource

**ویژگی‌ها:**
- مشاهده لاگ 404 ها
- Action برای ایجاد Redirect مستقیم از لاگ
- فیلتر و جستجو

---

## Commerce و پرداخت

### Cart Service

**مسیر:** `App\Domains\Commerce\Services\Cart`

**قابلیت‌ها:**
- افزودن/حذف محصول
- Session-based storage
- محاسبه مجموع

### Actions

1. **AddProductToCartAction**
2. **RemoveFromCartAction**
3. **CreateOrderFromCartAction**
4. **CompletePaymentAction**

### Events

1. **OrderCreated**
2. **OrderPaid**

### Listeners

1. **SendOrderConfirmationEmail**
2. **SendPaymentConfirmationEmail**

### Payment Gateway

**پکیج:** `shetabit/payment`

**Gateway:** Zarinpal (قابل تغییر)

**Controller:** `App\Http\Controllers\Payments\PaymentController`
- `checkout()` - ایجاد سفارش و redirect به درگاه
- `callback()` - تایید پرداخت و ثبت enrollment

---

## Authentication و Authorization

### Auth System

- **Provider:** `legacy-eloquent` - پشتیبانی از WordPress MD5 hash
- **OTP:** SMS.ir integration
- **Routes:** `/auth/otp/request`, `/auth/otp/verify`

### Policies

**مسیر:** `app/Policies/`

1. **AiToolPolicy**
2. **CoursePolicy**
3. **EnrollmentPolicy**
4. **OrderPolicy**
5. **PostPolicy**

### Gates

- Admin email gate (در `AuthServiceProvider`)

---

## ساختار دیتابیس

### جداول اصلی:

1. **users** - کاربران
2. **ai_tools** - ابزارهای AI
3. **courses, chapters, lessons** - دوره‌ها
4. **enrollments** - ثبت‌نام‌ها
5. **posts** - مقالات
6. **news** - اخبار
7. **products** - محصولات
8. **orders, order_items** - سفارش‌ها
9. **pages** - صفحات
10. **categories, categorizables** - دسته‌بندی‌ها
11. **teachers** - مدرسین
12. **careers** - فرصت‌های شغلی
13. **redirects** - تغییر مسیرها
14. **not_found_logs** - لاگ 404
15. **settings** - تنظیمات (Spatie Settings)
16. **navigation_items** - آیتم‌های منوی ناوبری
17. **comments** - نظرات (polymorphic)
18. **reviews** - نقد و بررسی (polymorphic)
19. **clicks** - ردیابی کلیک‌ها
20. **bookmarks** - نشان‌گذاری‌ها (AI Tools)

---

## فایل‌های مهم

### Config Files:
- `config/payment.php` - تنظیمات درگاه پرداخت
- `config/services.php` - SMS.ir و سایر سرویس‌ها

### Migrations:
- همه migrations در `database/migrations/` سازماندهی شده‌اند

### Seeders:
- `CategorySeeder` - دسته‌بندی‌های پیش‌فرض
- `AdminUserSeeder` - کاربر ادمین

---

## Frontend Integration

### Layout
**مسیر:** `resources/views/components/layouts/app.blade.php`

**ویژگی‌ها:**
- SEO meta tags (via `seo()` helper)
- Favicon از settings
- Header scripts از settings
- Footer scripts از settings
- RTL support (Persian)

### View Sharing

**AppServiceProvider:**
```php
View::share('settings', app(GeneralSettings::class));
```

**LayoutComposer:**
```php
// Navigation Menus
$headerMenu = NavigationItem::getMenu('header');
$footerMenu = NavigationItem::getMenu('footer');

// Auth & Cart Data
$isAuthenticated = auth()->check();
$sessionCart = session('cart.items', []);
$cartCount = collect($sessionCart)->sum('quantity');
$userName = auth()->user()?->name;
```

---

## نکات مهم

1. **Soft Deletes:** AiTool, Post, News, Product, Comment, Review از soft deletes استفاده می‌کنند
2. **Scout/Meilisearch:** AiTool, Post, News, Product قابل جستجو هستند
3. **SEO:** همه مدل‌های محتوا از HasSEO trait استفاده می‌کنند
4. **Polymorphic Relations:** 
   - Category (categorizables)
   - OrderItem (orderable)
   - Comment (commentable)
   - Review (reviewable)
5. **Events/Listeners:** برای Order و Enrollment events تعریف شده‌اند
6. **Click Tracking:** کلیک‌های affiliate_url در جدول clicks ذخیره می‌شوند
7. **Bookmarks:** کاربران می‌توانند AI Tools را نشان‌گذاری کنند (bookmarks table)
8. **Navigation:** منوهای Header و Footer به صورت داینامیک از navigation_items مدیریت می‌شوند

---

## Deep Media Integration (Curator)

**وضعیت فعلی:** ✅ **پیاده‌سازی کامل شده**

### Migration ایجاد شده
**فایل:** `database/migrations/2025_12_21_140740_add_curator_media_fields_to_models.php`

**تغییرات اعمال شده:**
- ✅ `ai_tools.logo_id` → foreign key به `media` table
- ✅ `courses.thumbnail_id` → foreign key به `media` table
- ✅ `posts.thumbnail_id` → foreign key به `media` table
- ✅ `news.thumbnail_id` → foreign key به `media` table
- ✅ `products.thumbnail_id` → foreign key به `media` table
- ✅ `teachers.avatar_id` → foreign key به `media` table

**نکته:** فیلدهای قدیمی (`*_path`) برای سازگاری با داده‌های موجود حفظ شده‌اند.

### Models به‌روزرسانی شده (6 مدل)

#### AiTool Model
```php
protected $fillable = [
    // ...
    'logo_path',  // قدیمی (حفظ شده)
    'logo_id',    // جدید (Curator)
];

public function logo(): BelongsTo
{
    return $this->belongsTo(\Awcodes\Curator\Models\Media::class, 'logo_id');
}
```

#### Course, Post, News, Product Models
```php
protected $fillable = [
    // ...
    'thumbnail_path',  // قدیمی (حفظ شده)
    'thumbnail_id',    // جدید (Curator)
];

public function thumbnail(): BelongsTo
{
    return $this->belongsTo(\Awcodes\Curator\Models\Media::class, 'thumbnail_id');
}
```

#### Teacher Model
```php
protected $fillable = [
    // ...
    'avatar_path',  // قدیمی (حفظ شده)
    'avatar_id',    // جدید (Curator)
];

public function avatar(): BelongsTo
{
    return $this->belongsTo(\Awcodes\Curator\Models\Media::class, 'avatar_id');
}
```

### Filament Resources به‌روزرسانی شده (6 Resource)

همه Resources از `CuratorPicker` به جای `FileUpload` استفاده می‌کنند:

**AiToolResource:**
```php
CuratorPicker::make('logo_id')
    ->label('لوگو')
    ->directory('logos')
    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp', 'image/svg+xml'])
```

**CourseResource, PostResource, NewsResource, ProductResource:**
```php
CuratorPicker::make('thumbnail_id')
    ->label('تصویر شاخص')
    ->directory('course-thumbnails') // یا thumbnails, news, products
    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
```

**TeacherResource:**
```php
CuratorPicker::make('avatar_id')
    ->label('آواتار')
    ->directory('teachers')
    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
    ->avatar()
```

### دستورات اجرا

```bash
# Curator migrations منتشر شده
php artisan vendor:publish --tag=curator-migrations

# Migration جدید برای اضافه کردن foreign keys آماده است
# php artisan migrate  # باید اجرا شود
```

---

## 📝 گزارش تغییرات و وضعیت فعلی

### تاریخچه به‌روزرسانی‌ها

**2025-12-21 - Phase 1-5 Complete Implementation:**
- ✅ **Phase 1: Deep Media Integration (Curator)** - کامل شده
  - Migration ایجاد شده برای اضافه کردن `*_id` fields
  - 6 مدل به‌روزرسانی شده با روابط Curator Media
  - 6 Filament Resource به‌روزرسانی شده با CuratorPicker
  - فیلدهای قدیمی (`*_path`) حفظ شده برای سازگاری

- ✅ **Phase 2: Navigation System** - کامل شده
  - NavigationItem Model و Resource موجود بودند
  - LayoutComposer منوها را inject می‌کند
  - Header navigation به‌روزرسانی شده
  - Footer navigation به‌روزرسانی شده (dynamic menu)

- ✅ **Phase 3: Interaction System (Comments & Reviews)** - کامل شده
  - CommentSection Livewire component (nested comments)
  - ReviewForm Livewire component (star rating, duplicate prevention)
  - Relationships اضافه شده به Post, Course, AiTool, Lesson

- ✅ **Phase 4: Click Tracking** - کامل شده
  - ClickController با redirect()->away() برای external URLs
  - Route `/go/{slug}` فعال

- ✅ **Phase 5: Student Dashboard** - کامل شده
  - MyCourses page (thumbnail accessor)
  - OrderHistory page (invoice download)
  - Bookmarks page (logo accessor)
  - InvoiceController و invoice view template

**2025-12-21 - Initial Documentation:**
- ✅ اضافه شدن 5 ماژول جدید به مستندات
  - Dynamic Navigation System
  - Unified Interaction System (Comments & Reviews)
  - Student/User Dashboard (App Panel)
  - Internal Analytics (Click Tracking)
  - Deep Media Integration (Curator) - Pattern

### وضعیت فعلی سیستم

| کامپوننت | وضعیت | یادداشت |
|---------|-------|---------|
| Curator Media Integration | ✅ پیاده‌سازی شده | Migration آماده، نیاز به اجرا |
| Navigation System | ✅ پیاده‌سازی شده | Header و Footer dynamic کامل |
| Comments & Reviews | ✅ کامل | Livewire Components + Relationships |
| Click Tracking | ✅ کامل | Controller + Route فعال |
| Student Dashboard | ✅ کامل | تمام صفحات functional |

---

## ✅ Phase 3: Interaction System (Comments & Reviews) - **پیاده‌سازی کامل شده**

**تاریخ تکمیل:** 2025-12-21

### Livewire Components ایجاد شده

#### CommentSection Component
**مسیر:** `app/Livewire/Interactions/CommentSection.php`

**ویژگی‌ها:**
- ✅ نمایش نظرات تایید شده (nested replies)
- ✅ فرم ارسال نظر (فقط برای کاربران لاگین شده)
- ✅ ذخیره به عنوان `pending` (نیاز به تایید Admin)
- ✅ ثبت IP Address و User Agent

**استفاده:**
```blade
<livewire:interactions.comment-section :model="$post" />
<livewire:interactions.comment-section :model="$lesson" />
```

#### ReviewForm Component
**مسیر:** `app/Livewire/Interactions/ReviewForm.php`

**ویژگی‌ها:**
- ✅ نمایش میانگین امتیاز و تعداد نقدها
- ✅ فرم ارسال نقد با ستاره‌گذاری (1-5)
- ✅ جلوگیری از نقد تکراری (چک می‌کند کاربر قبلاً نقد داده یا نه)
- ✅ ذخیره به عنوان `pending` (نیاز به تایید Admin)

**استفاده:**
```blade
<livewire:interactions.review-form :model="$course" />
<livewire:interactions.review-form :model="$aiTool" />
```

### Relationships اضافه شده

**مدل‌های به‌روزرسانی شده:**
- ✅ `Post::comments()` - MorphMany relationship
- ✅ `Course::reviews()` - MorphMany relationship
- ✅ `AiTool::reviews()` - MorphMany relationship
- ✅ `Lesson::comments()` - MorphMany relationship

---

## ✅ Phase 4: Click Tracking & Analytics - **پیاده‌سازی کامل شده**

**تاریخ تکمیل:** 2025-12-21

### ClickController
**مسیر:** `app/Http/Controllers/Core/ClickController.php`

**عملکرد:**
- ✅ پیدا کردن AiTool با slug
- ✅ ثبت کلیک در دیتابیس (ClickTracker service)
- ✅ Redirect به `affiliate_url` یا `website_url` با `redirect()->away()`

**Route:** `/go/{slug}` - `click.track`

**استفاده:**
```blade
<a href="{{ route('click.track', $aiTool->slug) }}" target="_blank">
    بازدید از سایت
</a>
```

---

## ✅ Phase 5: Student Dashboard (App Panel) - **پیاده‌سازی کامل شده**

**تاریخ تکمیل:** 2025-12-21

### صفحات به‌روزرسانی شده

#### MyCourses Page
- ✅ Table با Enrollment query (user-specific)
- ✅ نمایش thumbnail (از Curator Media یا fallback به thumbnail_path)
- ✅ Columns: تصویر، عنوان دوره، تاریخ ثبت‌نام، تاریخ انقضا
- ✅ Action: مشاهده دوره (link به course show page)

#### OrderHistory Page
- ✅ Table با Order query (user-specific)
- ✅ Columns: شماره سفارش، وضعیت (badge)، مبلغ، تاریخ
- ✅ Action: دانلود فاکتور (HTML view)
- ✅ Route: `/app/invoice/{order}`

**InvoiceController:** `app/Http/Controllers/App/InvoiceController.php`
- ✅ Authorization check (فقط کاربر مالک فاکتور)
- ✅ View: `resources/views/filament/app/invoice.blade.php`
- ✅ نمایش اطلاعات سفارش، آیتم‌ها، جمع کل

#### Bookmarks Page
- ✅ Table با AiTool query (bookmarked by user)
- ✅ نمایش logo (از Curator Media یا fallback به logo_path)
- ✅ Columns: لوگو، نام، نوع قیمت
- ✅ Action: حذف از نشان‌گذاری‌ها

---

### ❓ سوالات باز و تصمیم‌های لازم

1. **Migration Execution:**
   - ⚠️ Migration برای Curator (`2025_12_21_140740_add_curator_media_fields_to_models.php`) آماده است اما هنوز اجرا نشده
   - **سوال:** آیا باید migration را اجرا کنیم؟ یا منتظر migration داده‌ها باشیم؟

2. **Data Migration Strategy:**
   - آیا نیاز به Script برای تبدیل `*_path` به Media records دارید؟
   - چه زمانی فیلدهای قدیمی (`*_path`) را حذف کنیم؟
   - **پیشنهاد:** بعد از migration کامل داده‌ها و تست

3. **Integration in Show Pages:**
   - CommentSection و ReviewForm Components ایجاد شده‌اند اما هنوز در صفحات Show (Post/Course/AiTool) یکپارچه نشده‌اند
   - **سوال:** آیا صفحات Show برای Post و Course وجود دارند؟ یا باید ایجاد شوند؟

4. **Footer Navigation Structure:**
   - Footer از `$footerMenu` استفاده می‌کند اما ساختار آن با Header متفاوت است (هر item خودش می‌تواند parent باشد)
   - **سوال:** آیا این ساختار درست است؟ یا باید footer menu items به صورت section-based باشد (مثلاً "دسترسی سریع" یک parent با children)?

5. **Invoice PDF Generation:**
   - Invoice حالا HTML view است
   - **سوال:** آیا نیاز به PDF generation دارید؟ (barryvdh/laravel-dompdf یا similar)

6. **Comment Moderation:**
   - Comments به صورت `pending` ذخیره می‌شوند
   - **سوال:** آیا نیاز به Email notification برای Admin هنگام دریافت comment جدید دارید؟

7. **Review Average Rating:**
   - ReviewForm میانگین امتیاز را نمایش می‌دهد
   - **سوال:** آیا باید `AiTool::rating` و `Course::rating` fields از reviews محاسبه شوند (via Observer/Cast)؟

8. **Click Count Display:**
   - Click Tracking کار می‌کند
   - **سوال:** آیا باید تعداد کلیک‌ها در AiToolResource نمایش داده شود؟ (column/stat widget)

9. **Livewire Component Styling:**
   - CommentSection و ReviewForm با Tailwind utility classes نوشته شده‌اند
   - **سوال:** آیا باید با design system موجود (Figma styles) هماهنگ شوند؟

10. **Enrollment Progress Tracking:**
    - MyCourses page نمایش می‌دهد اما progress tracking ندارد
    - **سوال:** آیا نیاز به progress bar دارید؟ (چند درس مشاهده شده / کل درس‌ها)

---

**آخرین بروزرسانی:** 2025-12-21  
**وضعیت:** تمام 5 Phase کامل شده - آماده برای integration و testing  
**گزارش کامل:** برای جزئیات بیشتر به `IMPLEMENTATION_STATUS.md` و `FRONTEND_INTEGRATION_GUIDE.md` مراجعه کنید.

