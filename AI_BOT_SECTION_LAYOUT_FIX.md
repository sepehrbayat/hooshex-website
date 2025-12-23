# AI Bot Section Layout Fix - Mobile & Tablet Only

## مشکل
در موبایل و تبلت، ترتیب نمایش کارت‌ها اشتباه است. طبق تصویر الگو (که کارت شیشه‌ای در پایین است):

**وضعیت فعلی (اشتباه):**
- کارت شیشه‌ای (Glass Card) در بالا نمایش داده می‌شود
- فرم صورتی (Pink Form) در پایین نمایش داده می‌شود

**باید (طبق تصویر الگو):**
- **فرم صورتی (Pink Form)** با فیلدهای نام، نام خانوادگی، شماره تماس، علاقه‌مندی‌ها در **بالا** باشد
- **کارت شیشه‌ای (Glass Card)** با سوال "چه دوره ای مناسب منه؟" و دکمه‌های "شروع جستجو"، "اتصال فایل"، "ضبط صدا" در **پایین** باشد

**نکته:** در دسکتاپ درست است و نیازی به تغییر ندارد. در دسکتاپ فرم صورتی در راست و کارت شیشه‌ای در چپ است.

## راه‌حل

در فایل `resources/views/livewire/home.blade.php`:

### تغییر Order Classes

#### 1. AI Form (Pink Background) - خط 172

**قبل:**
```blade
<div class="relative z-10 w-full mt-6 md:mt-8 lg:mt-0 lg:ml-auto lg:w-[758px] lg:absolute lg:right-0 lg:top-[72px] order-2 md:order-2 lg:order-1">
```

**بعد:**
```blade
<div class="relative z-10 w-full mt-6 md:mt-8 lg:mt-0 lg:ml-auto lg:w-[758px] lg:absolute lg:right-0 lg:top-[72px] order-1 md:order-1 lg:order-1">
```

**تغییر:** `order-2 md:order-2` → `order-1 md:order-1` (برای موبایل/تبلت در بالا)

#### 2. AI Generator Chatbox (Glass) - خط 237

**قبل:**
```blade
<div class="relative z-30 w-full md:w-[495px] lg:absolute lg:left-[30px] lg:w-[716px] lg:top-[240px] order-1 md:order-1 lg:order-2">
```

**بعد:**
```blade
<div class="relative z-30 w-full md:w-[495px] lg:absolute lg:left-[30px] lg:w-[716px] lg:top-[240px] order-2 md:order-2 lg:order-2">
```

**تغییر:** `order-1 md:order-1` → `order-2 md:order-2` (برای موبایل/تبلت در پایین)

## توضیح فنی

### نحوه کار CSS Order در Flexbox

در یک container با `display: flex` و `flex-direction: column`:
- عنصر با `order: 1` در **بالا** نمایش داده می‌شود
- عنصر با `order: 2` در **پایین** نمایش داده می‌شود

### ساختار Container

```blade
<div class="relative flex min-h-[400px] flex-col items-center gap-6 md:gap-[24px] lg:min-h-[592px] lg:items-start">
    {{-- Section Title --}}
    <h3>چی برام مناسبه؟</h3>
    
    {{-- AI Form (Pink) - order-1 = بالا در موبایل/تبلت --}}
    <div class="... order-1 md:order-1 lg:order-1">
    
    {{-- Glass Card - order-2 = پایین در موبایل/تبلت --}}
    <div class="... order-2 md:order-2 lg:order-2">
</div>
```

### Breakpoints

- **Mobile (`order-1`, `order-2`)**: فرم صورتی بالا، کارت شیشه‌ای پایین
- **Tablet (`md:order-1`, `md:order-2`)**: همان ترتیب موبایل
- **Desktop (`lg:order-1`, `lg:order-2`)**: بدون تغییر (فرم صورتی راست، کارت شیشه‌ای چپ با absolute positioning)

## خلاصه تغییرات

| Element | Mobile/Tablet | Desktop | توضیح |
|---------|---------------|---------|-------|
| **Pink Form** | `order-1` (بالا) | `lg:order-1` (راست) | در موبایل/تبلت در بالا |
| **Glass Card** | `order-2` (پایین) | `lg:order-2` (چپ) | در موبایل/تبلت در پایین |

## بررسی و تست

بعد از اعمال تغییرات:

1. **موبایل (430px):**
   - ✅ فرم صورتی باید در بالا باشد
   - ✅ کارت شیشه‌ای باید در پایین باشد

2. **تبلت (768px):**
   - ✅ فرم صورتی باید در بالا باشد
   - ✅ کارت شیشه‌ای باید در پایین باشد

3. **دسکتاپ (1440px+):**
   - ✅ فرم صورتی باید در راست باشد (absolute positioning)
   - ✅ کارت شیشه‌ای باید در چپ باشد (absolute positioning)
   - ✅ ترتیب دسکتاپ تغییر نکرده است

## نکات مهم

1. **Z-Index:** 
   - Glass Card: `z-30` (بالاتر)
   - Pink Form: `z-10` (پایین‌تر)
   - این برای دسکتاپ است که کارت شیشه‌ای روی فرم صورتی قرار می‌گیرد

2. **Absolute Positioning در دسکتاپ:**
   - در دسکتاپ (`lg:`), هر دو عنصر از flow خارج می‌شوند و با `absolute` positioning قرار می‌گیرند
   - `order` در دسکتاپ تاثیری ندارد چون از flow خارج شده‌اند

3. **Responsive Gap:**
   - موبایل: `gap-6` (24px)
   - تبلت: `md:gap-[24px]` (24px)
   - دسکتاپ: gap با absolute positioning تنظیم می‌شود

4. **استایل شیشه‌ای (Glass Effect) - بسیار مهم:**
   
   **⚠️ هشدار:** استایل شیشه‌ای کارت شیشه‌ای باید دقیقاً عین نسخه دسکتاپ باشد. هیچ تغییری در استایل‌های CSS نباید اعمال شود.
   
   **استایل‌های CSS در `resources/css/app.css`:**
   ```css
   .liquid-glass {
       position: relative;
       isolation: isolate;
       box-shadow: 0px 6px 24px rgba(0, 0, 0, 0.2);
       display: flex;
       align-items: center;
       justify-content: center;
       border: none;
       background: none;
       padding: 0;
       margin: 0;
   }
   
   .liquid-glass::before {
       content: '';
       position: absolute;
       inset: 0;
       z-index: 0;
       border-radius: inherit;
       box-shadow: inset 0 0 41px -14px rgba(255, 255, 255, 0.3);
       background-color: rgba(255, 255, 255, 0.04);
   }
   
   .liquid-glass::after {
       content: '';
       position: absolute;
       inset: 0;
       z-index: -1;
       border-radius: inherit;
       backdrop-filter: blur(20px);
       -webkit-backdrop-filter: blur(20px);
       filter: url(#glass-distortion);
       -webkit-filter: url(#glass-distortion);
   }
   ```
   
   **مطابق Figma Desktop CSS:**
   - Background: `rgba(224, 224, 224, 0.16)` (در container اصلی)
   - Border-radius: `32px`
   - Backdrop-filter: `blur(20px)`
   - Box-shadow: `0px 6px 24px rgba(0, 0, 0, 0.2)`
   
   **قوانین مهم:**
   - ✅ استایل شیشه‌ای در موبایل/تبلت باید **دقیقاً همان استایل دسکتاپ** باشد
   - ✅ هیچ تغییری در `opacity`، `blur`، `background-color`، یا `border-radius` نباید اعمال شود
   - ✅ کلاس `liquid-glass` و تمام pseudo-elements (`::before`, `::after`) باید یکسان باقی بمانند
   - ✅ فقط ترتیب نمایش (order) تغییر می‌کند، **نه استایل ظاهری**
   - ❌ نباید استایل‌های responsive برای glass effect اضافه شود (مثلاً `md:backdrop-blur-sm` یا `lg:backdrop-blur-lg`)
   - ❌ نباید opacity یا background color برای موبایل/تبلت تغییر کند

## فایل‌های تغییر یافته

- `resources/views/livewire/home.blade.php` (خطوط 172 و 237)

## تاریخچه تغییرات

- **تاریخ:** 2025-01-XX
- **تغییر:** جابجایی order classes برای موبایل/تبلت
- **نتیجه:** فرم صورتی در بالا، کارت شیشه‌ای در پایین (مطابق تصویر الگو)
