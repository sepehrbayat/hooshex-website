# پرامپت آماده برای کپی در Cursor AI

```
من یک خطای دیتابیس دارم که می‌گوید جدول careers وجود ندارد:

SQLSTATE[HY000]: General error: 1 no such table: careers

این خطا زمانی رخ می‌دهد که سعی می‌کنم به صفحه /admin/careers در پنل Filament دسترسی پیدا کنم.

وضعیت فعلی:
- PHP 8.4.16 (وب سرور)، Laravel 12.41.1، SQLite
- Migration files موجود هستند اما اجرا نشده‌اند
- Model و Resource موجود هستند
- یک route موقت در routes/web.php اضافه شده که می‌تواند جدول را ایجاد کند

لطفاً:
1. بررسی کن که آیا جدول careers در دیتابیس وجود دارد
2. اگر وجود ندارد، از route موقت /admin/create-careers-table استفاده کن یا مستقیماً با Laravel Schema Builder جدول را ایجاد کن
3. مطمئن شو تمام ستون‌ها و index ها ایجاد شده‌اند
4. تست کن که صفحه /admin/careers بدون خطا کار می‌کند
5. اگر route موقت کار کرد، آن را حذف کن

فایل‌های مرتبط:
- database/migrations/core/2025_12_21_123641_create_careers_table.php
- database/migrations/core/2025_12_21_165608_refactor_careers_table_for_job_postings.php
- app/Domains/Core/Models/Career.php
- routes/web.php (route موقت در انتها)
- database/database.sqlite

لطفاً این مشکل را به طور کامل حل کن.
```










