# دسترسی به صفحه افزودن مقاله

## لینک مستقیم:

```
http://127.0.0.1:7668/admin/posts/create
```

## لیست مقالات:

```
http://127.0.0.1:7668/admin/posts
```

## نکات مهم:

1. ✅ PostResource فعال و آماده است
2. ✅ Navigation با نام "مقالات" در گروه "محتوا" 
3. ✅ Policy بررسی شده - فقط Admin و Teacher دسترسی دارند
4. ✅ کاربر شما Admin است
5. ✅ تمام صفحات (List, Create, Edit) وجود دارند

## دستور بررسی:

```bash
# بررسی تمام روت‌های posts
php artisan route:list --name=posts

# پاک کردن cache
php artisan optimize:clear

# دیدن تمام Resources ثبت شده در Filament
php artisan filament:about
```

## اگر در navigation نمیاد:

احتمالاً باید صفحه را refresh کنید یا Ctrl+Shift+R بزنید تا cache مرورگر پاک شود.
