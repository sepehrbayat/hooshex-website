# پرامپت برای Cursor AI - رفع مشکل جدول careers

## مشکل:
من یک خطای دیتابیس دارم که می‌گوید جدول `careers` وجود ندارد:

```
SQLSTATE[HY000]: General error: 1 no such table: careers
```

این خطا زمانی رخ می‌دهد که سعی می‌کنم به صفحه `/admin/careers` در پنل Filament دسترسی پیدا کنم.

## جزئیات فنی:

- **PHP Version**: 8.4.16 (در وب سرور)
- **Laravel Version**: 12.41.1
- **Database**: SQLite (`database/database.sqlite`)
- **مشکل اصلی**: جدول `careers` در دیتابیس وجود ندارد

## وضعیت فعلی:

1. **Migration Files موجود هستند:**
   - `database/migrations/core/2025_12_21_123641_create_careers_table.php`
   - `database/migrations/core/2025_12_21_165608_refactor_careers_table_for_job_postings.php`

2. **Model و Resource موجود هستند:**
   - `app/Domains/Core/Models/Career.php`
   - `app/Filament/Admin/Resources/CareerResource.php`

3. **مشکل**: Migration ها اجرا نشده‌اند چون:
   - PHP CLI من نسخه 8.3.29 است
   - پروژه نیاز به PHP 8.4.0+ دارد
   - وب سرور از PHP 8.4.16 استفاده می‌کند اما CLI نمی‌تواند migration ها را اجرا کند

## راه حل‌های موجود (که نیاز به بررسی دارند):

1. یک route موقت در `routes/web.php` اضافه شده که می‌تواند جدول را ایجاد کند
2. یک Artisan command در `app/Console/Commands/CreateCareersTable.php` ایجاد شده
3. فایل SQL در `create-careers-table.sql` موجود است

## درخواست من از تو:

**لطفاً مشکل را به طور کامل برطرف کن:**

1. **بررسی کن** که آیا جدول `careers` در دیتابیس وجود دارد یا نه
2. **اگر وجود ندارد**، یکی از این روش‌ها را استفاده کن:
   - Route موقت را تست کن (`/admin/create-careers-table`)
   - یا Artisan command را با PHP 8.4 اجرا کن
   - یا مستقیماً با Laravel Schema Builder جدول را ایجاد کن
3. **مطمئن شو** که:
   - تمام ستون‌های مورد نیاز ایجاد شده‌اند
   - Index ها به درستی اضافه شده‌اند
   - Migration ها در جدول `migrations` ثبت شده‌اند (اگر ممکن است)
4. **تست کن** که صفحه `/admin/careers` بدون خطا کار می‌کند
5. **اگر route موقت کار کرد**، آن را از `routes/web.php` حذف کن (چون دیگر نیاز نیست)

## فایل‌های مرتبط:

- `database/migrations/core/2025_12_21_123641_create_careers_table.php`
- `database/migrations/core/2025_12_21_165608_refactor_careers_table_for_job_postings.php`
- `app/Domains/Core/Models/Career.php`
- `app/Filament/Admin/Resources/CareerResource.php`
- `routes/web.php` (route موقت در انتها)
- `app/Console/Commands/CreateCareersTable.php`
- `database/database.sqlite` (فایل دیتابیس)

## نتیجه مورد انتظار:

بعد از رفع مشکل، باید بتوانم:
- بدون خطا به `/admin/careers` دسترسی داشته باشم
- جدول `careers` در دیتابیس وجود داشته باشد
- تمام ستون‌ها و index ها به درستی ایجاد شده باشند
- Filament Resource برای careers به درستی کار کند

---

**لطفاً این مشکل را به طور کامل حل کن و مطمئن شو که همه چیز درست کار می‌کند.**
