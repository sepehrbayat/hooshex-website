# راهنمای استایل برند هوشکس

## رنگ‌ها (Colors)

### Primary (بنفش)
- `primary-50`: `rgba(119, 95, 238, 0.1)` - پس‌زمینه روشن
- `primary-400`: `#442CBB` - تیره
- `primary-500`: `#775FEE` - اصلی
- `primary-600`: `#5537EA` - hover/active
- `primary-800`: `#22165E` - متن تیره

### Accent (صورتی)
- `accent-400`: `rgba(235, 85, 200, 0.36)` - شفاف
- `accent-500`: `#EB55C8` - اصلی

### Surface (سطح)
- `surface`: `#FCF1FB` - پس‌زمینه اصلی
- `surface-light`: `#F5F5F5` - پس‌زمینه روشن

### Text (متن)
- `text-primary`: `#22165E` - اصلی
- `text-secondary`: `#2D2D2D` - ثانویه
- `text-muted`: `#AAAAAA` - کم‌رنگ

### Status (وضعیت)
- `success`: `#10B981` - موفقیت
- `warning`: `#F59E0B` - هشدار
- `error`: `#EF4444` - خطا
- `info`: `#3B82F6` - اطلاعات

## تایپوگرافی (Typography)

### فونت
- **Font Family**: `Vazirmatn` (فارسی‌اول)
- **Font Features**: `font-feature-settings: 'ss01' on, 'ss02' on, 'ss03' on, 'ss04' on`
- **Font Variation**: `font-variation-settings: 'DOTS' 7`

### سایزها
| کلاس | Font Size | Line Height |
|------|-----------|-------------|
| `text-xs` | 12px | 18px |
| `text-sm` | 14px | 21px |
| `text-base` | 16px | 24px |
| `text-lg` | 18px | 27px |
| `text-xl` | 20px | 30px |
| `text-2xl` | 24px | 36px |
| `text-3xl` | 32px | 48px |
| `text-4xl` | 36px | 54px |
| `text-5xl` | 56px | 84px |

### وزن‌ها
- `font-normal`: 400
- `font-medium`: 500
- `font-semibold`: 600
- `font-bold`: 700
- `font-black`: 900

## Border Radius

- `rounded-sm`: 4px
- `rounded`: 8px
- `rounded-md`: 12px
- `rounded-lg`: 16px
- `rounded-xl`: 24px
- `rounded-2xl`: 32px
- `rounded-pill`: 9999px
- `rounded-card`: 16px
- `rounded-btn`: 8px

## فاصله‌گذاری (Spacing)

- `gap-18`: 72px
- `gap-22`: 88px
- Container padding: `1rem` → `2.5rem` (responsive)

## Breakpoints

- `sm`: 430px
- `md`: 768px
- `lg`: 1024px
- `xl`: 1280px
- `2xl`: 1440px

## سایه‌ها (Shadows)

- `shadow-glass`: `0px 6px 24px rgba(0, 0, 0, 0.2)`
- `shadow-button-primary`: `0px 2px 8px rgba(235, 85, 200, 0.46), inset 0px -4px 16px rgba(102, 24, 84, 0.13), inset 0px 4px 16px rgba(102, 24, 84, 0.11)`

## RTL (راست‌به‌چپ)

- همیشه از `dir="rtl"` استفاده کنید
- در RTL: `justify-start` = راست، `justify-end` = چپ
- متن فارسی: `text-right`

## استفاده در Tailwind

```html
<!-- رنگ اصلی -->
<div class="bg-primary-500 text-primary-800">...</div>

<!-- متن -->
<p class="text-text-primary font-semibold text-lg">...</p>

<!-- دکمه -->
<button class="bg-accent-500 text-white rounded-btn px-6 py-3 shadow-button-primary">...</button>

<!-- کارت -->
<div class="bg-surface rounded-card p-6 shadow-glass">...</div>
```

