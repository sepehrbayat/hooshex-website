# Laravel Frontend Best Practices - راهنمای پیاده‌سازی

این سند Best Practiceهای پیاده‌سازی شده در پروژه را توضیح می‌دهد.

---

## ۱. استفاده حداکثری از Blade Components

### ✅ پیاده‌سازی شده

تمام `@include` به Blade Components تبدیل شده‌اند:

**قبل (❌):**
```blade
@include('components.auth.profile-menu')
```

**بعد (✅):**
```blade
<x-auth.profile-menu :user-name="$userName" />
```

### مزایا:
- ✅ Props برای انتقال داده‌ها
- ✅ مقادیر پیش‌فرض قابل تعریف
- ✅ جدا کردن منطق از HTML
- ✅ ساختار شبیه React/Vue

### مثال‌های موجود:

```blade
{{-- UI Components --}}
<x-ui.button variant="primary" size="lg">کلیک کنید</x-ui.button>
<x-ui.section id="hero" background="surface">
    {{ $slot }}
</x-ui.section>

{{-- Home Components --}}
<x-home.hero :title="$title" />
<x-home.features :features="$features" />
```

---

## ۲. منطق صفر در View (Logic-less Views)

### ✅ پیاده‌سازی شده

#### 1. View Composer برای داده‌های مشترک

**LayoutComposer.php:**
```php
class LayoutComposer
{
    public function compose(View $view): void
    {
        $view->with([
            'isAuthenticated' => auth()->check(),
            'sessionCart' => array_values(session('cart.items', [])),
            'cartCount' => collect($sessionCart)->sum(...),
            'userName' => auth()->user()?->name,
        ]);
    }
}
```

**AppServiceProvider.php:**
```php
View::composer('components.layouts.app', LayoutComposer::class);
```

#### 2. ViewModel برای داده‌های صفحه اصلی

**HomePageData.php:**
```php
class HomePageData
{
    public static function features(): array { ... }
    public static function testimonials(): array { ... }
    public static function blogs(): array { ... }
}
```

**Home.php (Livewire Component):**
```php
public function render(): View
{
    return view('livewire.home', [
        'features' => HomePageData::features(),
        'testimonials' => HomePageData::testimonials(),
        'blogs' => HomePageData::blogs(),
    ]);
}
```

### مزایا:
- ✅ Blade files فقط نمایش می‌دهند
- ✅ منطق در PHP classes
- ✅ قابل تست
- ✅ قابل استفاده مجدد

---

## ۳. مدیریت حرفه‌ای Assets با Vite

### ✅ پیکربندی موجود

**vite.config.js:**
```javascript
export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
});
```

### Best Practices:
- ✅ JavaScript در `resources/js/`
- ✅ CSS در `resources/css/`
- ✅ استفاده از `@vite` directive
- ✅ Code splitting برای صفحات خاص

### مثال:
```blade
@vite(['resources/css/app.css', 'resources/js/app.js'])
```

---

## ۴. استراتژی Tailwind CSS

### ✅ پیاده‌سازی شده

#### 1. Extracting Components

**قبل (❌):**
```blade
<div class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600">
```

**بعد (✅):**
```blade
<x-ui.button variant="primary">کلیک کنید</x-ui.button>
```

#### 2. Component CSS Files

استایل‌های component در فایل‌های جداگانه:

```
resources/css/components/
├── buttons.css
├── cards.css
├── forms.css
├── hero.css
└── ...
```

#### 3. استفاده از @layer

```css
@layer components {
    .btn-primary {
        @apply bg-accent-500 text-white;
    }
}
```

---

## ۵. انتخاب بین Livewire و Inertia.js

### ✅ انتخاب شده: Livewire

پروژه از **Livewire** استفاده می‌کند:
- ✅ Real-time interactions
- ✅ Server-side rendering
- ✅ یکپارچگی با Laravel
- ✅ Alpine.js built-in

### ساختار:
```
app/Http/Livewire/
├── Home.php
└── ...

resources/views/livewire/
├── home.blade.php
└── ...
```

---

## ۶. ساختار پوشه‌بندی استاندارد

### ✅ ساختار فعلی

```
resources/views/
├── components/
│   ├── layouts/          # Layout templates
│   │   └── app.blade.php
│   ├── ui/               # Reusable UI components
│   │   ├── button.blade.php
│   │   ├── section.blade.php
│   │   └── section-header.blade.php
│   ├── home/             # Home page components
│   │   ├── hero.blade.php
│   │   ├── features.blade.php
│   │   └── ...
│   ├── auth/             # Auth components
│   │   └── profile-menu.blade.php
│   └── cart/             # Cart components
│       └── cart-modal.blade.php
├── livewire/             # Livewire components
│   ├── home.blade.php
│   └── ...
└── ai-tools/             # Domain-specific views
    └── index.blade.php
```

### اصول:
- ✅ Components قابل استفاده مجدد
- ✅ Domain-based organization
- ✅ Separation of concerns

---

## ۷. استفاده از Alpine.js

### ✅ پیاده‌سازی شده

Alpine.js برای کارهای کوچک (modal, dropdown, toggle):

```blade
<div x-data="{ open: false }">
    <button @click="open = !open">Toggle</button>
    <div x-show="open" x-cloak>Content</div>
</div>
```

### مثال‌های موجود:
- Profile menu dropdown
- Mobile navigation toggle
- Modal dialogs

---

## ساختار داده‌ها

### View Composers
```php
app/Http/ViewComposers/
└── LayoutComposer.php    # Shared layout data
```

### ViewModels
```php
app/ViewModels/
└── HomePageData.php      # Home page data structures
```

### Livewire Components
```php
app/Http/Livewire/
└── Home.php              # Home page logic
```

---

## Best Practices Checklist

### ✅ انجام شده:
- [x] استفاده از Blade Components به جای @include
- [x] View Composer برای داده‌های مشترک
- [x] ViewModel برای ساختار داده‌ها
- [x] Logic-less views (منطق در PHP)
- [x] Component CSS files
- [x] استفاده از @layer
- [x] ساختار پوشه‌بندی استاندارد
- [x] Alpine.js برای تعاملات کوچک
- [x] Livewire برای تعاملات پیچیده

### 🔄 در حال بهبود:
- [ ] Prettier plugin برای Tailwind
- [ ] Code splitting برای صفحات خاص
- [ ] تست‌های واحد برای ViewModels

---

## مثال‌های عملی

### 1. Component با Props

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

### 2. استفاده از Component

```blade
<x-ui.button variant="primary" size="lg">
    کلیک کنید
</x-ui.button>
```

### 3. View Composer

```php
// app/Providers/AppServiceProvider.php
View::composer('components.layouts.app', LayoutComposer::class);
```

### 4. ViewModel

```php
// app/ViewModels/HomePageData.php
public static function features(): array
{
    return [
        ['title' => '...', 'description' => '...'],
    ];
}
```

---

## نتیجه

پروژه از Best Practiceهای Laravel Frontend پیروی می‌کند:
- ✅ ماژولار و قابل استفاده مجدد
- ✅ Logic-less views
- ✅ تمیز و قابل نگهداری
- ✅ مقیاس‌پذیر

