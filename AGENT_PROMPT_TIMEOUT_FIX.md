# پرامپت برای VS Code Agent - رفع مشکل Timeout در Dashboard Filament

## مشکل:
خطای `Maximum execution time of 30 seconds exceeded` در صفحه `/admin` پنل ادمین Filament رخ می‌دهد. سرور PHP با timeout پیش‌فرض 30 ثانیه اجرا می‌شود در حالی که صفحه Dashboard نیاز به زمان بیشتری دارد.

## تغییرات لازم (انجام شده):
✅ `package.json` - پورت به 7668 و max_execution_time=300 اضافه شد
✅ `app/Providers/AppServiceProvider.php` - timeout پشتیبان اضافه شد

## کارهایی که Agent باید انجام دهد:

### مرحله 1: بررسی وضعیت فعلی سرور
1. بررسی کن که آیا سرور Laravel در حال اجرا است (پورت 7668)
2. اگر سرور در حال اجرا است، آن را متوقف کن (Ctrl+C یا kill process)

### مرحله 2: پاک کردن Cache های Laravel
اجرا کن این دستورات به ترتیب:
```bash
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear
php artisan optimize:clear
```

### مرحله 3: راه‌اندازی مجدد سرور با تنظیمات جدید
اجرا کن:
```bash
npm run serve
```
یا اگر می‌خواهی همزمان با Vite:
```bash
npm run start
```

### مرحله 4: بررسی و اطمینان
1. صبر کن تا سرور کاملاً بالا بیاید (چند ثانیه)
2. بررسی کن که سرور روی پورت 7668 در حال اجرا است
3. اگر سرور با موفقیت راه‌اندازی شد، به کاربر گزارش بده که آماده تست است

### مرحله 5: تست (اختیاری - اگر Agent دسترسی به browser دارد)
به `http://127.0.0.1:7668/admin` برو و بررسی کن که:
- صفحه Dashboard لود می‌شود
- خطای timeout برطرف شده
- Widget ها نمایش داده می‌شوند

## دستورات کامل به ترتیب:

```bash
# 1. پاک کردن cache
php artisan cache:clear && php artisan config:clear && php artisan view:clear && php artisan route:clear && php artisan optimize:clear

# 2. راه‌اندازی سرور (در background)
npm run serve
```

یا اگر می‌خواهی همزمان با Vite:

```bash
# 1. پاک کردن cache
php artisan cache:clear && php artisan config:clear && php artisan view:clear && php artisan route:clear && php artisan optimize:clear

# 2. راه‌اندازی هر دو سرور
npm run start
```

## انتظارات:

بعد از اجرای این مراحل:
- ✅ سرور باید روی پورت 7668 با timeout 300 ثانیه اجرا شود
- ✅ Cache های Laravel پاک شده باشند
- ✅ صفحه Dashboard باید بدون خطای timeout لود شود

## نکات مهم:

1. **اگر سرور در حال اجرا است**: حتماً اول آن را متوقف کن قبل از restart
2. **Cache**: حتماً همه cache ها را پاک کن تا تغییرات اعمال شوند
3. **Background execution**: اگر `npm run serve` را در background اجرا می‌کنی، مطمئن شو که process در حال اجرا است
4. **Port conflict**: اگر پورت 7668 در حال استفاده است، اول process قبلی را kill کن

## گزارش نهایی:

بعد از انجام مراحل، به کاربر گزارش بده:
- ✅ Cache ها پاک شدند
- ✅ سرور روی پورت 7668 با timeout 300 ثانیه راه‌اندازی شد
- ✅ آماده تست است - کاربر می‌تواند به `http://127.0.0.1:7668/admin` برود

---

**این پرامپت را کپی کن و به VS Code Agent بده تا تمام مراحل را به صورت خودکار انجام دهد.**

