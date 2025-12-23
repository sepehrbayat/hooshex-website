# پرامپت رفع مشکلات بخش بلاگ هوشکس

## مشکل فعلی

بخش بلاگ هوشکس در صفحه اصلی (`resources/views/livewire/home.blade.php`) دارای مشکلات زیر است:

### 1. مشکل عنوان "بلاگ هوشکس"

**موقعیت:** 
- DOM Path: `section#blog > div.flex flex-col items-end > div.flex items-center justify-end > h2`
- Element: `<h2 class="text-[28px] md:text-[30px] lg:text-[32px] font-bold leading-[48px] text-right">`

**مشکل:**
- عنوان "بلاگ هوشکس" باید `justify-content: center` داشته باشد
- در حال حاضر `justify-content: normal` یا `justify-content: flex-end` دارد
- عنوان باید در مرکز container قرار بگیرد نه در سمت راست

**کد فعلی:**
```blade
<div class="flex items-center justify-end" style="width: 1216px; height: 48px;">
    <h2 class="text-[28px] md:text-[30px] lg:text-[32px] font-bold leading-[48px] text-right" style="... justify-content: flex-end; ...">
        <span class="text-[#22165E]">بلاگ </span><span class="text-[#EB55C8]">هوشکس</span>
    </h2>
</div>
```

**راه حل:**
1. در container div، `justify-end` را به `justify-center` تغییر بده
2. در inline style ه2، `justify-content: flex-end` را به `justify-content: center` تغییر بده

**کد صحیح:**
```blade
<div class="flex items-center justify-center" style="width: 1216px; height: 48px;">
    <h2 class="text-[28px] md:text-[30px] lg:text-[32px] font-bold leading-[48px] text-right" style="... justify-content: center; ...">
        <span class="text-[#22165E]">بلاگ </span><span class="text-[#EB55C8]">هوشکس</span>
    </h2>
</div>
```

## دستورالعمل برای Cursor AI

```
در فایل resources/views/livewire/home.blade.php، بخش بلاگ هوشکس را پیدا کن.

مشکل: عنوان "بلاگ هوشکس" باید در مرکز container قرار بگیرد اما در حال حاضر در سمت راست است.

تغییرات مورد نیاز:
1. در div container عنوان (کلاس: "flex items-center justify-end")، کلاس justify-end را به justify-center تغییر بده
2. در h2 عنوان، در inline style، justify-content: flex-end را به justify-content: center تغییر بده

بعد از اعمال تغییرات:
- عنوان باید در مرکز container قرار بگیرد
- کلمه "بلاگ" باید آبی (#22165E) و کلمه "هوشکس" باید صورتی (#EB55C8) باشد
- همه چیز باید راست‌چین (RTL) باقی بماند
```

## چک‌لیست بررسی

بعد از اعمال تغییرات، بررسی کن:

- [ ] عنوان "بلاگ هوشکس" در مرکز container قرار دارد
- [ ] `justify-content: center` در h2 اعمال شده است
- [ ] `justify-center` در container div اعمال شده است
- [ ] کلمه "بلاگ" آبی (#22165E) است
- [ ] کلمه "هوشکس" صورتی (#EB55C8) است
- [ ] متن راست‌چین (RTL) است
- [ ] هیچ خطای linting وجود ندارد

