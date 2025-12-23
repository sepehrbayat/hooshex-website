# راهنمای پیاده‌سازی 5 ماژول ناقص

## 1. Dynamic Navigation System

### Migration
```bash
php artisan migrate
```

### Filament Resource
- **مسیر:** `app/Filament/Admin/Resources/NavigationItemResource.php`
- **مدل:** `App\Domains\Core\Models\NavigationItem`
- **استفاده:** ایجاد منوهای Header و Footer

### Frontend Integration
**ViewComposer:** `app/Http/ViewComposers/LayoutComposer.php` به‌روزرسانی شده
- `$headerMenu` و `$footerMenu` در همه view ها در دسترس است

**استفاده در Blade:**
```blade
@foreach($headerMenu as $item)
    <a href="{{ $item->href }}" {{ $item->open_in_new_tab ? 'target="_blank"' : '' }}>
        {{ $item->label }}
    </a>
@endforeach
```

---

## 2. Deep Media Integration (Curator)

### Refactoring Required

**مدل‌های نیازمند تغییر:**
- `AiTool` - `logo_path` → Curator Media
- `Course` - `thumbnail_path` → Curator Media
- `Post` - `thumbnail_path` → Curator Media
- `Product` - `thumbnail_path` → Curator Media

**الگوی تغییر:**
```php
// در Filament Resource:
Awcodes\Curator\Components\Forms\CuratorPicker::make('logo_id')
    ->label('لوگو')
    ->directory('logos')
    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])

// در Model:
public function logo(): BelongsTo
{
    return $this->belongsTo(\Awcodes\Curator\Models\Media::class, 'logo_id');
}
```

**Migration برای اضافه کردن foreign key:**
```php
Schema::table('ai_tools', function (Blueprint $table) {
    $table->foreignId('logo_id')->nullable()->constrained('media')->nullOnDelete();
    $table->dropColumn('logo_path');
});
```

---

## 3. Unified Interaction System

### Models Created
- `App\Interactions\Comment` - Polymorphic (Post, Lesson)
- `App\Interactions\Review` - Polymorphic (AiTool, Course)

### Filament Resources
- `CommentResource` - با Actions: Approve, Spam
- `ReviewResource` - با Actions: Approve

### Relationships to Add

**در Post Model:**
```php
public function comments(): MorphMany
{
    return $this->morphMany(Comment::class, 'commentable');
}
```

**در AiTool Model:**
```php
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

## 4. Student/User Dashboard

### Filament App Panel
**مسیر:** `/app`
**Provider:** `App\Providers\Filament\AppPanelProvider`

### Pages Created
1. **MyCourses** - لیست دوره‌های ثبت‌نام شده
2. **OrderHistory** - تاریخچه سفارش‌ها + دانلود فاکتور
3. **Bookmarks** - نشان‌گذاری‌های AI Tools
4. **Profile** - ویرایش پروفایل و تغییر رمز

### Migration Required
```bash
php artisan migrate
```

### Routes
پنل App به صورت خودکار در `/app` در دسترس است.

---

## 5. Internal Analytics (Click Tracking)

### Service
**مسیر:** `app/Domains/Core/Services/ClickTracker.php`

### Controller
**مسیر:** `app/Http/Controllers/Core/ClickController.php`
**Route:** `/go/{slug}`

### Route Added
```php
Route::get('/go/{slug}', [ClickController::class, 'go'])->name('click.track');
```

### AiToolResource Updated
- ستون `click_count` اضافه شده (counts relationship)

### Usage in Frontend
```blade
<a href="{{ route('click.track', $aiTool->slug) }}">
    بازدید از سایت
</a>
```

---

## Commands to Run

```bash
# 1. Run Migrations
php artisan migrate

# 2. Publish Curator (if not already)
php artisan vendor:publish --tag=curator-migrations
php artisan migrate

# 3. Clear Cache
php artisan optimize:clear
```

---

## Database Tables Created

1. `comments` - نظرات (polymorphic)
2. `reviews` - نقد و بررسی (polymorphic)
3. `clicks` - ردیابی کلیک‌ها
4. `navigation_items` - آیتم‌های منو
5. `bookmarks` - نشان‌گذاری‌ها

---

## Next Steps

1. **Curator Integration:** Refactor همه `*_path` fields به Curator Media
2. **Frontend Comments:** ایجاد کامپوننت Livewire برای نمایش/ارسال نظرات
3. **Review System:** ایجاد کامپوننت برای نمایش/ارسال نقد
4. **Bookmark Toggle:** اضافه کردن دکمه Bookmark در صفحات AI Tools
5. **Invoice Generation:** پیاده‌سازی PDF generation برای فاکتورها

---

## Files Created/Modified

### New Models
- `app/Interactions/Comment.php`
- `app/Interactions/Review.php`
- `app/Domains/Core/Models/NavigationItem.php`
- `app/Domains/Core/Models/Click.php`

### New Services
- `app/Domains/Core/Services/ClickTracker.php`

### New Controllers
- `app/Http/Controllers/Core/ClickController.php`

### New Filament Resources
- `app/Filament/Admin/Resources/CommentResource.php`
- `app/Filament/Admin/Resources/ReviewResource.php`
- `app/Filament/Admin/Resources/NavigationItemResource.php`

### New Filament Pages (App Panel)
- `app/Filament/App/Pages/MyCourses.php`
- `app/Filament/App/Pages/OrderHistory.php`
- `app/Filament/App/Pages/Bookmarks.php`
- `app/Filament/App/Pages/Profile.php`

### Modified Files
- `app/Http/ViewComposers/LayoutComposer.php` - Navigation injection
- `app/Filament/Admin/Resources/AiToolResource.php` - Click count column
- `app/Domains/AiTools/Models/AiTool.php` - Clicks relationship
- `routes/web.php` - Click tracking route
- `app/Providers/Filament/AppPanelProvider.php` - App panel config

