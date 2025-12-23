# راهنمای بازسازی صفحه اصلی

## خلاصه تغییرات

صفحه اصلی به صورت کاملاً ماژولار بازسازی شده است. تمام بخش‌ها به کامپوننت‌های قابل استفاده مجدد تبدیل شده‌اند.

## ساختار جدید

### کامپوننت‌های صفحه اصلی (`resources/views/components/home/`)

1. **hero.blade.php** - بخش هیرو با جستجو
2. **features.blade.php** - شبکه ویژگی‌ها
3. **career-paths.blade.php** - مسیرهای شغلی (موجود)
4. **super-app.blade.php** - بخش سوپر اپلیکیشن (موجود)
5. **popular-courses.blade.php** - دوره‌های محبوب (موجود)
6. **ai-bot.blade.php** - بخش بات هوش مصنوعی
7. **instagram.blade.php** - بخش اینستاگرام
8. **testimonials.blade.php** - نظرات کاربران
9. **blog.blade.php** - بخش بلاگ
10. **banner.blade.php** - بنر تبلیغاتی

### کامپوننت‌های UI (`resources/views/components/ui/`)

1. **button.blade.php** - دکمه قابل استفاده مجدد
2. **section-header.blade.php** - هدر بخش‌ها
3. **section.blade.php** - wrapper برای بخش‌ها

## نحوه استفاده

### استفاده از نسخه بازسازی شده

فایل `resources/views/livewire/home-refactored.blade.php` را به `home.blade.php` تغییر نام دهید:

```bash
mv resources/views/livewire/home-refactored.blade.php resources/views/livewire/home.blade.php
```

یا محتوای `home-refactored.blade.php` را به `home.blade.php` کپی کنید.

### استفاده از کامپوننت‌ها

```blade
{{-- استفاده از بخش هیرو --}}
<x-home.hero 
    title="عنوان اصلی"
    searchPlaceholder="متن placeholder"
    :searchAction="route('ai-tools.index')"
/>

{{-- استفاده از بخش ویژگی‌ها --}}
<x-home.features :features="$featuresArray" />

{{-- استفاده از بخش نظرات --}}
<x-home.testimonials :testimonials="$testimonialsArray" />
```

## Design System

تمام توکن‌های طراحی در `tailwind.config.js` و CSS فایل‌ها سازمان‌دهی شده‌اند:

- **رنگ‌ها**: در `tailwind.config.js` تعریف شده‌اند
- **تایپوگرافی**: در `resources/css/base/typography.css`
- **کامپوننت‌های CSS**: در `resources/css/components/`

## مزایای ساختار جدید

1. **ماژولاریتی**: هر بخش کاملاً مستقل است
2. **قابلیت استفاده مجدد**: کامپوننت‌ها در صفحات دیگر قابل استفاده هستند
3. **نگهداری آسان**: تغییرات در یک بخش بر بخش‌های دیگر تأثیر نمی‌گذارد
4. **خوانایی بهتر**: کد تمیزتر و قابل فهم‌تر است
5. **تست آسان‌تر**: هر بخش به صورت جداگانه قابل تست است

## نکات مهم

- تمام استایل‌های inline به کلاس‌های Tailwind تبدیل شده‌اند
- استایل‌های خاص در فایل‌های CSS مربوطه قرار دارند
- کامپوننت‌ها از props برای تنظیم رفتار استفاده می‌کنند
- RTL به صورت کامل پشتیبانی می‌شود

