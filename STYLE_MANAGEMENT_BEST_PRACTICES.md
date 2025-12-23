# مدیریت استایل در Laravel - Best Practices

این سند راهکارهای پیاده‌سازی شده برای مدیریت استایل‌ها در پروژه را توضیح می‌دهد.

---

## ۱. پذیرش فلسفه Utility-First (Tailwind)

### ✅ پیاده‌سازی شده

پروژه از **Tailwind CSS** به صورت Utility-First استفاده می‌کند. کلاس‌های Tailwind تک‌منظوره هستند و تداخل استایل‌ها را کاهش می‌دهند.

### Best Practice: Blade Components به جای CSS Classes

**قبل (❌):**
```css
/* resources/css/components/cards.css */
.card {
    @apply rounded-card bg-white shadow-md;
}
```

```blade
<div class="card">
    Content
</div>
```

**بعد (✅):**
```blade
{{-- resources/views/components/ui/card.blade.php --}}
<div {{ $attributes->merge(['class' => 'rounded-card bg-white shadow-md p-4']) }}>
    {{ $slot }}
</div>
```

```blade
<x-ui.card>
    Content
</x-ui.card>
```

### مزایا:
- ✅ استایل‌ها همراه با کد هستند (کپسوله‌سازی)
- ✅ قابل استفاده مجدد
- ✅ Override آسان با props
- ✅ بدون تداخل با سایر بخش‌ها

---

## ۲. استفاده از @stack و @push برای استایل‌های خاص هر صفحه

### ✅ پیاده‌سازی شده

**Layout (app.blade.php):**
```blade
<head>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    @stack('styles') {{-- Stack for page-specific styles --}}
</head>
```

**Page-specific View (contact.blade.php):**
```blade
@push('styles')
<style>
    /* این استایل فقط در همین صفحه لود می‌شود */
    .unique-contact-header {
        background: url('/map.png');
    }
</style>
@endpush
```

### مزایا:
- ✅ استایل‌های خاص هر صفحه در همان صفحه
- ✅ بدون تداخل با صفحات دیگر
- ✅ لود بهینه (فقط در صفحه مورد نیاز)

---

## ۳. کپسوله‌سازی با Blade Components

### ✅ پیاده‌سازی شده

تمام استایل‌های تکراری در Blade Components کپسوله شده‌اند:

**مثال: Button Component**
```blade
{{-- resources/views/components/ui/button.blade.php --}}
@props([
    'variant' => 'primary',
    'size' => 'md',
])

<button {{ $attributes->merge([
    'class' => "btn btn-{$variant} btn-{$size}",
]) }}>
    {{ $slot }}
</button>
```

**استفاده:**
```blade
<x-ui.button variant="primary" size="lg">
    کلیک کنید
</x-ui.button>
```

### Components موجود:
- `x-ui.button` - دکمه‌های قابل استفاده مجدد
- `x-ui.section` - wrapper برای section ها
- `x-ui.section-header` - هدر بخش‌ها
- `x-home.*` - کامپوننت‌های خاص صفحه اصلی

---

## ۴. ساختاردهی فایل‌های CSS

### ✅ ساختار فعلی

```
resources/css/
├── app.css              # فایل اصلی (وارد کردن لایه‌ها)
├── base/                # استایل‌های پایه
│   ├── fonts.css        # فونت‌ها
│   └── typography.css   # تایپوگرافی
├── components/          # استایل‌های کامپوننت‌های خاص
│   ├── buttons.css      # Button styles (برای @layer components)
│   ├── cards.css        # Card styles
│   ├── forms.css        # Form styles
│   ├── hero.css         # Hero section styles
│   ├── features.css     # Features section styles
│   ├── ai-bot.css       # AI Bot section styles
│   ├── sections.css     # Common section patterns
│   ├── liquid-glass.css # Glass effect styles
│   └── swiper.css       # Swiper.js styles
├── utilities/           # Utility classes
│   ├── animations.css   # Animation utilities
│   ├── layout.css       # Layout utilities
│   └── rtl.css          # RTL-specific utilities
└── pages/               # استایل‌های منحصر به فرد هر صفحه (برای آینده)
    └── (empty for now)
```

### اصول ساختاردهی:

1. **base/**: استایل‌های پایه که در کل پروژه استفاده می‌شوند
2. **components/**: استایل‌های کامپوننت‌های خاص (ترجیحاً از Blade Components استفاده شود)
3. **utilities/**: کلاس‌های utility قابل استفاده مجدد
4. **pages/**: استایل‌های منحصر به فرد هر صفحه (در صورت نیاز)

---

## ۵. استفاده از @layer در Tailwind

### ✅ پیاده‌سازی شده

تمام استایل‌های component در `@layer components` قرار گرفته‌اند:

```css
@layer components {
    .btn {
        @apply inline-flex items-center justify-center;
    }
    
    .card {
        @apply rounded-card bg-white shadow-md;
    }
}
```

### مزایا:
- ✅ Cascade به درستی توسط Tailwind مدیریت می‌شود
- ✅ امکان override با utility classes
- ✅ Organization بهتر

---

## ۶. CSS Classes vs Blade Components

### تصمیم‌گیری: چه زمانی از CSS Class استفاده کنیم؟

#### ❌ از CSS Class استفاده نکنید وقتی:
- استایل یک المان تکراری است (از Blade Component استفاده کنید)
- می‌خواهید props و customization داشته باشید
- نیاز به composition دارید

#### ✅ از CSS Class استفاده کنید وقتی:
- استایل یک effect خاص است (مثل liquid-glass)
- استایل برای کتابخانه خارجی است (مثل swiper)
- استایل یک utility class است (مثل animations)

### مثال‌های موجود:

**CSS Class (برای effects):**
```css
@layer components {
    .liquid-glass {
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(10px);
    }
}
```

**Blade Component (برای UI elements):**
```blade
{{-- resources/views/components/ui/card.blade.php --}}
<div {{ $attributes->merge(['class' => 'rounded-card bg-white']) }}>
    {{ $slot }}
</div>
```

---

## ۷. پیشگیری از تداخل با Prefix در Tailwind

### ⚠️ در حال حاضر استفاده نمی‌شود

اگر نیاز به prefix دارید (مثلاً برای پکیج Laravel):

```javascript
// tailwind.config.js
export default {
    prefix: 'tw-', // همه کلاس‌ها: tw-flex, tw-bg-red-500
    // ...
}
```

**توجه:** در حال حاضر از `important: '#app'` استفاده می‌شود که برای اکثر موارد کافی است.

---

## ۸. PurgeCSS / Tree Shaking

### ✅ خودکار در Tailwind/Vite

Tailwind به صورت خودکار کلاس‌های استفاده نشده را حذف می‌کند. Vite نیز tree shaking انجام می‌دهد.

**تنظیمات در tailwind.config.js:**
```javascript
export default {
    content: [
        './resources/views/**/*.blade.php',
        './resources/js/**/*.js',
        // ...
    ],
}
```

---

## ۹. Best Practices Checklist

### ✅ انجام شده:
- [x] استفاده از Tailwind CSS Utility-First
- [x] استفاده از Blade Components برای UI elements
- [x] استفاده از @stack و @push برای page-specific styles
- [x] ساختاردهی CSS به base/components/utilities
- [x] استفاده از @layer components
- [x] کپسوله‌سازی استایل‌ها در Components
- [x] استفاده از important: '#app' strategy

### 🔄 در حال بهبود:
- [ ] تبدیل تمام CSS classes به Blade Components (جایی که منطقی است)
- [ ] اضافه کردن pages/ directory برای page-specific CSS
- [ ] استفاده از CSS Modules برای Vue/React (اگر اضافه شوند)

---

## ۱۰. مثال‌های عملی

### مثال 1: Page-specific Style

```blade
{{-- resources/views/contact.blade.php --}}
@push('styles')
<style>
    .contact-hero {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }
</style>
@endpush

<div class="contact-hero">
    <!-- Content -->
</div>
```

### مثال 2: Blade Component با Tailwind

```blade
{{-- resources/views/components/ui/alert.blade.php --}}
@props([
    'type' => 'info',
])

@php
    $colors = match($type) {
        'success' => 'bg-green-100 text-green-800 border-green-200',
        'error' => 'bg-red-100 text-red-800 border-red-200',
        'warning' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
        default => 'bg-blue-100 text-blue-800 border-blue-200',
    };
@endphp

<div {{ $attributes->merge([
    'class' => "rounded-lg border p-4 {$colors}",
]) }}>
    {{ $slot }}
</div>
```

### مثال 3: Utility Class

```css
/* resources/css/utilities/animations.css */
@layer utilities {
    .fade-in {
        animation: fadeIn 0.5s ease-in;
    }
    
    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }
}
```

---

## ۱۱. Migration Guide

### تبدیل CSS Classes به Blade Components

**قبل:**
```css
/* resources/css/components/cards.css */
.user-card {
    @apply rounded-lg bg-white p-4 shadow-md;
}
```

```blade
<div class="user-card">
    Content
</div>
```

**بعد:**
```blade
{{-- resources/views/components/ui/card.blade.php --}}
@props([
    'variant' => 'default',
])

<div {{ $attributes->merge([
    'class' => 'rounded-lg bg-white p-4 shadow-md',
]) }}>
    {{ $slot }}
</div>
```

```blade
<x-ui.card>
    Content
</x-ui.card>
```

---

## ۱۲. نتیجه

با استفاده از این راهکارها:
- ✅ استایل‌ها کپسوله و قابل استفاده مجدد هستند
- ✅ تداخل استایل‌ها به حداقل رسیده
- ✅ کد تمیز و قابل نگهداری است
- ✅ Performance بهینه است (PurgeCSS)
- ✅ ساختار منظم و قابل توسعه است

---

## منابع

- [Tailwind CSS Documentation](https://tailwindcss.com/docs)
- [Laravel Blade Components](https://laravel.com/docs/blade#components)
- [Vite Documentation](https://vitejs.dev/)

