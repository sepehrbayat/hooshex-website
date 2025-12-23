# راهنمای یکپارچه‌سازی Frontend

**تاریخ:** 2025-12-21

---

## ✅ Phase 1: Footer Navigation - کامل شده

**فایل به‌روزرسانی شده:** `resources/views/components/footer.blade.php`

Footer حالا از `$footerMenu` استفاده می‌کند. منوها باید در Admin Panel (`/admin/navigation-items`) ایجاد شوند.

---

## ✅ Phase 2: Livewire Components - ایجاد شده

### CommentSection Component

**مسیر:** `app/Livewire/Interactions/CommentSection.php`  
**View:** `resources/views/livewire/interactions/comment-section.blade.php`

**ویژگی‌ها:**
- نمایش نظرات تایید شده (nested replies)
- فرم ارسال نظر (فقط برای کاربران لاگین شده)
- ذخیره به عنوان `pending` (نیاز به تایید Admin)

**استفاده در Blade:**
```blade
{{-- در Post Show Page --}}
<livewire:interactions.comment-section :model="$post" />

{{-- در Lesson Show Page --}}
<livewire:interactions.comment-section :model="$lesson" />
```

### ReviewForm Component

**مسیر:** `app/Livewire/Interactions/ReviewForm.php`  
**View:** `resources/views/livewire/interactions/review-form.blade.php`

**ویژگی‌ها:**
- نمایش میانگین امتیاز و تعداد نقدها
- فرم ارسال نقد با ستاره‌گذاری (1-5)
- جلوگیری از نقد تکراری
- ذخیره به عنوان `pending` (نیاز به تایید Admin)

**استفاده در Blade:**
```blade
{{-- در Course Show Page --}}
<livewire:interactions.review-form :model="$course" />

{{-- در AiTool Show Page --}}
<livewire:interactions.review-form :model="$aiTool" />
```

**نکته:** مدل‌های Post, Course, AiTool باید relationship ها را داشته باشند:

```php
// در Post Model
public function comments(): MorphMany
{
    return $this->morphMany(\App\Interactions\Comment::class, 'commentable');
}

// در Course Model
public function reviews(): MorphMany
{
    return $this->morphMany(\App\Interactions\Review::class, 'reviewable');
}

// در AiTool Model
public function reviews(): MorphMany
{
    return $this->morphMany(\App\Interactions\Review::class, 'reviewable');
}
```

---

## ✅ Phase 3: Click Tracking - کامل شده

**Controller:** `app/Http/Controllers/Core/ClickController.php`  
**Route:** `/go/{slug}` - `click.track`

**استفاده:**
```blade
<a href="{{ route('click.track', $aiTool->slug) }}" target="_blank">
    بازدید از سایت
</a>
```

**عملکرد:**
1. ثبت کلیک در دیتابیس (IP, User Agent, Referer)
2. Redirect به `affiliate_url` یا `website_url` (external)

---

## ✅ Phase 4: Student Dashboard - کامل شده

### MyCourses Page
- ✅ Table با Enrollment query
- ✅ نمایش thumbnail (از Curator یا fallback به path)
- ✅ Action: مشاهده دوره

### OrderHistory Page
- ✅ Table با Order query
- ✅ Action: دانلود فاکتور (HTML view)
- ✅ Route: `/app/invoice/{order}`

**Controller:** `app/Http/Controllers/App/InvoiceController.php`

### Bookmarks Page
- ✅ Table با AiTool query
- ✅ Action: حذف از نشان‌گذاری‌ها
- ✅ نمایش logo (از Curator یا fallback به path)

### Profile Page
- ✅ فرم ویرایش پروفایل
- ✅ تغییر رمز عبور

---

## 📝 مثال‌های یکپارچه‌سازی

### Post Show Page Example

```blade
{{-- resources/views/posts/show.blade.php --}}
<x-layouts.app>
    <article>
        <h1>{{ $post->title }}</h1>
        <div>{!! $post->content !!}</div>
        
        {{-- Comments Section --}}
        <section class="mt-12">
            <livewire:interactions.comment-section :model="$post" />
        </section>
    </article>
</x-layouts.app>
```

### Course Show Page Example

```blade
{{-- resources/views/courses/show.blade.php --}}
<x-layouts.app>
    <article>
        <h1>{{ $course->title }}</h1>
        <div>{!! $course->content !!}</div>
        
        {{-- Reviews Section --}}
        <section class="mt-12">
            <livewire:interactions.review-form :model="$course" />
        </section>
    </article>
</x-layouts.app>
```

---

## 🔧 Relationships Required

برای اینکه Components کار کنند، باید Relationships به مدل‌ها اضافه شوند:

### Post Model
```php
use Illuminate\Database\Eloquent\Relations\MorphMany;

public function comments(): MorphMany
{
    return $this->morphMany(\App\Interactions\Comment::class, 'commentable');
}
```

### Course Model
```php
use Illuminate\Database\Eloquent\Relations\MorphMany;

public function reviews(): MorphMany
{
    return $this->morphMany(\App\Interactions\Review::class, 'reviewable');
}
```

### AiTool Model
```php
use Illuminate\Database\Eloquent\Relations\MorphMany;

public function reviews(): MorphMany
{
    return $this->morphMany(\App\Interactions\Review::class, 'reviewable');
}
```

### Lesson Model
```php
use Illuminate\Database\Eloquent\Relations\MorphMany;

public function comments(): MorphMany
{
    return $this->morphMany(\App\Interactions\Comment::class, 'commentable');
}
```

---

## 🚀 مراحل بعدی

1. **ایجاد Post/Course Show Pages** (اگر وجود ندارند)
2. **اضافه کردن Relationships** به مدل‌ها (اگر اضافه نشده‌اند)
3. **تست Components** در صفحات واقعی
4. **تنظیمات Curator** (در صورت نیاز)
5. **اجرای Migrations**: `php artisan migrate`

---

**آماده برای استفاده!** 🎉

