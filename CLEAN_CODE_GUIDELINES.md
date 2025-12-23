# راهنمای کد تمیز و یکپارچه

## اصول کلی

### 1. هیچ Inline Style استفاده نشود
❌ **بد:**
```blade
<div style="padding: 13px 16px; width: 340px;">
```

✅ **خوب:**
```blade
<div class="hero-search-input-container">
```

### 2. استفاده از Design Tokens
❌ **بد:**
```blade
<div class="text-[#22165E] bg-[#FCF1FB]">
```

✅ **خوب:**
```blade
<div class="text-text-primary bg-surface">
```

### 3. استفاده از Component Classes
❌ **بد:**
```blade
<section class="py-12 md:py-16 lg:py-20 bg-transparent">
```

✅ **خوب:**
```blade
<x-ui.section background="transparent">
```

### 4. Hardcoded Values
❌ **بد:**
```blade
<div class="w-[340px] h-[56px]">
```

✅ **خوب:**
```blade
<div class="hero-search-form">
```

## ساختار CSS

### Component CSS Files
هر بخش اصلی باید CSS file مخصوص خود را داشته باشد:

```
resources/css/components/
├── hero.css           # Hero section styles
├── features.css       # Features section styles
├── ai-bot.css         # AI bot section styles
├── ai-bot-forms.css   # AI bot form field styles
├── sections.css       # Common section patterns
└── ...
```

### Naming Convention

#### Component Classes
- پیشوند با نام component: `.hero-*`, `.ai-bot-*`, `.feature-*`
- نام‌های معنادار: `.hero-title` نه `.ht`

#### Utility Classes
- پیشوند `section-*` برای section patterns
- پیشوند `container-*` برای container patterns

### Responsive Design

استفاده از media queries در CSS به جای تکرار در Blade:

```css
.hero-title {
    @apply text-2xl;
}

@media (min-width: 768px) {
    .hero-title {
        @apply text-4xl;
    }
}
```

نه:
```blade
<h1 class="text-2xl md:text-4xl">
```

## ساختار کامپوننت‌ها

### Props vs Defaults

استفاده از props برای داده‌های قابل تغییر:

```blade
@props([
    'title' => 'Default Title',
    'subtitle' => null,
])
```

### Class Composition

استفاده از match expressions برای class selection:

```blade
@php
    $variantClass = match($variant) {
        'primary' => 'btn-primary',
        'secondary' => 'btn-secondary',
        default => 'btn-primary',
    };
@endphp
```

## File Organization

### Component Hierarchy

```
resources/views/components/
├── ui/              # Reusable UI components
│   ├── button.blade.php
│   ├── section.blade.php
│   └── section-header.blade.php
├── home/            # Home page specific components
│   ├── hero.blade.php
│   ├── features.blade.php
│   └── ...
└── layouts/         # Layout components
    └── app.blade.php
```

### CSS Organization

```
resources/css/
├── base/            # Base styles (fonts, typography, reset)
├── components/      # Component-specific styles
└── utilities/       # Utility classes
```

## Best Practices

### 1. Single Responsibility
هر component باید یک کار انجام دهد.

### 2. Composition over Configuration
استفاده از composition برای ساخت components پیچیده:

```blade
<x-ui.section>
    <x-ui.section-header title="..." />
    <div class="content">
        {{ $slot }}
    </div>
</x-ui.section>
```

### 3. DRY (Don't Repeat Yourself)
استفاده از component classes به جای تکرار styles.

### 4. Design System First
همیشه از design tokens استفاده کنید، نه hardcoded values.

## Migration Checklist

برای هر component:

- [ ] تمام inline styles به CSS classes تبدیل شده
- [ ] Hardcoded colors به design tokens تبدیل شده
- [ ] Hardcoded spacing به utility classes تبدیل شده
- [ ] Component CSS file ایجاد شده
- [ ] Responsive styles در CSS تعریف شده
- [ ] Props برای customization اضافه شده
- [ ] Comments برای clarity اضافه شده

## Examples

### Before (❌ Dirty Code)
```blade
<section id="section-hero" class="relative overflow-hidden bg-[#FCF1FB] w-full h-[527px] md:h-[448px] lg:h-[842px]">
    <div class="relative mx-auto w-full max-w-[430px] md:max-w-[768px] lg:max-w-[1024px] xl:max-w-[1444px] h-full px-[25px_27px] md:px-14 lg:px-10 xl:px-5">
        <div style="padding: 13px 16px;">
            <h1 style="font-family: 'Vazirmatn', sans-serif; font-weight: 700; font-feature-settings: 'ss01' on, 'ss02' on, 'ss03' on, 'ss04' on;">
                Title
            </h1>
        </div>
    </div>
</section>
```

### After (✅ Clean Code)
```blade
<section id="section-hero" class="hero-container">
    <div class="hero-inner">
        <div class="hero-search-input-container">
            <h1 class="hero-title">
                {{ $title }}
            </h1>
        </div>
    </div>
</section>
```

## Maintenance

### Regular Cleanup
- Review code برای inline styles
- Check برای hardcoded values
- Update design tokens اگر لازم باشد
- Refactor components برای better reusability

### Code Review Checklist
- [ ] No inline styles
- [ ] No hardcoded colors
- [ ] Uses design tokens
- [ ] Uses component classes
- [ ] Properly documented
- [ ] Responsive design implemented correctly

