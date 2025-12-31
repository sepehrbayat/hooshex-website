# راه‌حل کامل مشکل Timeout در پنل ادمین Filament

## 🔍 تحلیل مشکل

### مشکل اصلی:
```
Maximum execution time of 30 seconds exceeded
```

این خطا زمانی رخ می‌دهد که:
1. **سرور PHP با timeout پیش‌فرض 30 ثانیه در حال اجرا است** (نه با تنظیمات افزایش‌یافته)
2. **کد در حال اجرا بیش از 30 ثانیه طول می‌کشد** که می‌تواند به دلایل زیر باشد:
   - کامپایل اولیه Blade templates
   - لود شدن Widgets در Dashboard
   - پردازش‌های سنگین در Service Layer

### وضعیت فعلی:
- سرور روی پورت **7668** در حال اجرا است
- اما `package.json` پورت **6012** را با `max_execution_time=300` تنظیم کرده
- احتمالاً سرور با دستور دیگری راه‌اندازی شده که timeout پیش‌فرض دارد

---

## ✅ راه‌حل‌های پیشنهادی (به ترتیب اولویت)

### راه‌حل 1: افزایش Timeout در package.json (توصیه می‌شود)

فایل `package.json` را به‌روزرسانی کنید تا سرور روی پورت 7668 با timeout افزایش‌یافته اجرا شود:

```json
{
  "scripts": {
    "serve": "php -d max_execution_time=300 artisan serve --host 0.0.0.0 --port 7668",
    "start": "concurrently \"npm run serve\" \"npm run dev\""
  }
}
```

**سپس سرور را restart کنید:**
```bash
# توقف سرور فعلی (Ctrl+C در ترمینال)
# سپس:
npm run serve
# یا برای اجرای همزمان با Vite:
npm run start
```

---

### راه‌حل 2: تنظیم Timeout در bootstrap/app.php (برای همه درخواست‌ها)

اگر می‌خواهید timeout برای همه درخواست‌ها افزایش یابد، در فایل `bootstrap/app.php` اضافه کنید:

```php
// قبل از return $app
$app->terminating(function () {
    ini_set('max_execution_time', '300');
});
```

**یا در ServiceProvider:**

در `app/Providers/AppServiceProvider.php` در متد `boot()`:

```php
public function boot(): void
{
    // افزایش timeout برای درخواست‌های وب
    if (!app()->runningInConsole()) {
        set_time_limit(300);
        ini_set('max_execution_time', '300');
    }
    
    // ... کدهای موجود
}
```

---

### راه‌حل 3: بهینه‌سازی Widget (رفع مشکل ریشه‌ای)

ویجت `TrafficLightWidget` را طبق الگوی صحیح Filament v3 بازنویسی کنید:

**قبل:**
```php
public function getViewData(): array
{
    $analysis = app(TrafficLightAnalyzer::class)->analyze($this->content ?? '');
    return ['analysis' => $analysis];
}
```

**بعد (الگوی صحیح Filament v3):**
```php
class TrafficLightWidget extends Widget
{
    protected static string $view = 'filament.admin.widgets.traffic-light-widget';
    
    public ?string $content = null;
    
    protected function getViewData(): array
    {
        return [
            'analysis' => $this->getAnalysis(),
        ];
    }
    
    protected function getAnalysis(): array
    {
        return app(TrafficLightAnalyzer::class)->analyze($this->content ?? '');
    }
}
```

**یا بهتر است از Property استفاده کنید:**

```php
class TrafficLightWidget extends Widget
{
    protected static string $view = 'filament.admin.widgets.traffic-light-widget';
    
    public ?string $content = null;
    
    public function getAnalysisProperty(): array
    {
        return app(TrafficLightAnalyzer::class)->analyze($this->content ?? '');
    }
}
```

**و در view:**
```blade
{{-- استفاده از $this->analysis --}}
<div>{{ $this->analysis['score'] }}</div>
```

---

### راه‌حل 4: غیرفعال‌سازی موقت Widgets (برای تست)

اگر می‌خواهید مطمئن شوید مشکل از Widget است، می‌توانید Widget را موقتاً غیرفعال کنید:

در `app/Filament/Admin/Pages/Dashboard.php`:

```php
class Dashboard extends BaseDashboard
{
    protected static ?string $navigationLabel = 'داشبورد';
    protected static ?string $navigationIcon = 'heroicon-o-home';
    protected static ?int $navigationSort = 0;
    
    // غیرفعال کردن همه widgets
    protected function getWidgets(): array
    {
        return [];
    }
}
```

---

## 🔧 مراحل اجرای راه‌حل

### مرحله 1: به‌روزرسانی package.json

```bash
# فایل package.json را ویرایش کنید
```

### مرحله 2: توقف سرور فعلی

در ترمینالی که سرور در حال اجرا است:
- `Ctrl + C` برای توقف

### مرحله 3: پاک کردن Cache

```bash
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear
```

### مرحله 4: راه‌اندازی مجدد سرور

```bash
npm run serve
# یا
npm run start
```

### مرحله 5: تست صفحه Dashboard

به `http://127.0.0.1:7668/admin` بروید و بررسی کنید که خطا برطرف شده باشد.

---

## 🐛 Debugging (اگر مشکل باقی ماند)

### 1. لاگ‌گیری برای شناسایی بخش کند

در `app/Filament/Admin/Pages/Dashboard.php`:

```php
use Illuminate\Support\Facades\Log;

public function mount(): void
{
    Log::info('Dashboard mounting started');
    
    // افزودن timing
    $start = microtime(true);
    
    // کدهای موجود
    
    Log::info('Dashboard mounting completed', [
        'time' => microtime(true) - $start
    ]);
}
```

### 2. بررسی Query های کند

```bash
# فعال کردن query log
php artisan tinker
>>> DB::enableQueryLog();
>>> // اجرای درخواست
>>> DB::getQueryLog();
```

### 3. بررسی Memory Usage

در ابتدای `bootstrap/app.php`:

```php
ini_set('memory_limit', '512M');
```

---

## ⚠️ نکات مهم

1. **Production**: در production، timeout را بیشتر از 60 ثانیه نگذارید. اگر کد شما بیش از 30 ثانیه طول می‌کشد، باید بهینه‌سازی شود.

2. **Development**: برای development، timeout 300 ثانیه (5 دقیقه) قابل قبول است.

3. **Widget Performance**: مطمئن شوید Widget ها query های سنگین یا پردازش‌های زمان‌بر ندارند.

4. **Cache**: از Laravel Cache برای داده‌هایی که تغییر نمی‌کنند استفاده کنید.

---

## 📝 خلاصه تغییرات لازم

1. ✅ به‌روزرسانی `package.json` - تغییر پورت به 7668 و اضافه کردن `max_execution_time=300`
2. ✅ Restart کردن سرور
3. ✅ (اختیاری) بهینه‌سازی Widget structure
4. ✅ (اختیاری) اضافه کردن timeout در ServiceProvider

---

## 🎯 نتیجه نهایی

بعد از اعمال راه‌حل‌ها:
- ✅ صفحه Dashboard باید بدون timeout لود شود
- ✅ Widget ها باید به درستی نمایش داده شوند
- ✅ سرور باید با timeout افزایش‌یافته اجرا شود

