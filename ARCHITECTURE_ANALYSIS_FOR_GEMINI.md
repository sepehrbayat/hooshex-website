# معماری پروژه هوشکس - تحلیل و سوالات برای Gemini

## 📋 خلاصه پروژه

**نام پروژه**: هوشکس (Hooshex)  
**نوع**: پلتفرم آموزش آنلاین با تمرکز بر هوش مصنوعی  
**Stack اصلی**: Laravel 12 + Livewire 3 + Filament 3 + Tailwind CSS v4  
**PHP Version**: 8.2+  
**Database**: PostgreSQL (primary), MySQL (legacy), Redis (cache/queue), Meilisearch (search)

---

## 🏗️ ساختار فعلی پروژه

### 1. معماری کلی

```
Laravel Modular Monolith با Domain-Driven Design (DDD) سبک
```

#### ساختار Domain:
```
app/Domains/
├── AiTools/          # ابزارهای هوش مصنوعی
│   └── Models/
│       └── AiTool.php
├── Auth/             # احراز هویت (خالی - نیاز به تکمیل)
├── Blog/             # بلاگ و مقالات
│   ├── Models/
│   │   └── Post.php
│   └── Services/
│       └── TrafficLightAnalyzer.php
├── Commerce/         # تجارت (سفارشات، سبد خرید)
│   ├── Models/
│   │   ├── Order.php
│   │   └── OrderItem.php
│   └── Services/
│       └── Cart.php
├── Core/             # هسته مشترک (خالی - نیاز به تکمیل)
└── Courses/          # دوره‌های آموزشی
    └── Models/
        ├── Course.php
        ├── Chapter.php
        ├── Lesson.php
        └── Enrollment.php
```

### 2. لایه‌های Frontend

```
resources/
├── css/
│   ├── app.css                  # Entry point
│   ├── base/                    # Typography, fonts
│   ├── components/              # Component styles (hero, features, ai-bot, etc.)
│   ├── utilities/               # RTL, animations, layout
│   ├── pages/                   # Page-specific (خالی)
│   └── vendors/                 # Third-party overrides (خالی)
├── js/
│   ├── app.js                   # Main JS (Swiper, Cart, Alpine.js)
│   └── bootstrap.js             # Axios setup
└── views/
    ├── components/
    │   ├── layouts/
    │   │   └── app.blade.php    # Main layout
    │   ├── home/                # Home page sections (hero, features, etc.)
    │   ├── auth/                # Login modal, profile menu
    │   ├── cart/                # Cart modal
    │   ├── ui/                  # Reusable UI (button, section, section-header)
    │   └── footer.blade.php     # Footer component
    └── livewire/
        ├── home.blade.php       # Home page Livewire component
        ├── ai-tools/
        └── courses/
```

### 3. Backend Structure

```
app/
├── Auth/
│   └── LegacyUserProvider.php   # WordPress password migration
├── Console/Commands/
│   └── ImportLegacy.php         # Legacy data import
├── Domains/                     # Domain modules (see above)
├── Enums/                       # Type-safe enums
│   ├── CourseStatus.php
│   ├── OrderStatus.php
│   ├── PostStatus.php
│   ├── PostType.php
│   ├── PricingType.php
│   └── UserRole.php
├── Filament/                    # Admin panel
│   ├── Admin/
│   │   ├── Resources/           # CRUD resources
│   │   └── Widgets/
│   └── Student/
├── Http/
│   ├── Controllers/
│   │   ├── Auth/OtpController.php
│   │   ├── Commerce/CartController.php
│   │   └── Payments/PaymentController.php
│   ├── Livewire/
│   │   ├── Home.php
│   │   ├── AiTools/Grid.php
│   │   └── Courses/VideoPlayer.php
│   ├── Middleware/
│   │   ├── ConvertNumbersToPersian.php
│   │   └── EnsureUserIsEnrolled.php
│   └── ViewComposers/
│       └── LayoutComposer.php
├── Models/
│   ├── Category.php             # Shared category model
│   └── User.php
├── Policies/                    # Authorization policies
├── Providers/
│   ├── AppServiceProvider.php
│   ├── AuthServiceProvider.php
│   └── Filament/
└── ViewModels/
    └── HomePageData.php
```

---

## 🛠️ Stack تکنولوژی

### Backend
- **Framework**: Laravel 12
- **Admin Panel**: Filament 3
- **Reactive UI**: Livewire 3
- **Authentication**: Laravel Sanctum (API), Session (Web)
- **Search**: Laravel Scout + Meilisearch
- **Queue**: Laravel Horizon + Redis
- **Payment**: shetabit/payment (Zarinpal)
- **Monitoring**: Laravel Telescope (dev)

### Frontend
- **CSS Framework**: Tailwind CSS v4 (with @theme)
- **JavaScript**: Alpine.js (bundled with Livewire)
- **Build Tool**: Vite
- **UI Components**: Swiper.js (carousels)
- **Font**: Vazirmatn (Persian)

### Database & Infrastructure
- **Primary DB**: PostgreSQL
- **Legacy DB**: MySQL (read-only for migration)
- **Cache/Queue**: Redis
- **Search**: Meilisearch
- **SMS**: SMS.ir API

---

## 🎨 Design System

### Colors (Tailwind Config)
```javascript
primary: {
  50: 'rgba(119, 95, 238, 0.1)',
  400: '#442CBB',
  500: '#775FEE',
  600: '#5537EA',
  800: '#22165E',
}
accent: {
  400: 'rgba(235, 85, 200, 0.36)',
  500: '#EB55C8',
}
surface: '#FCF1FB'
text: {
  primary: '#22165E',
  secondary: '#2D2D2D',
  muted: '#AAAAAA',
}
```

### Typography
- **Font**: Vazirmatn (with font-feature-settings for Persian)
- **RTL**: Full RTL support with `dir="rtl"`

---

## ✅ نکات مثبت معماری فعلی

1. ✅ **Domain-Driven Design**: ساختار Domain-based برای جداسازی concerns
2. ✅ **Type Safety**: استفاده از Enums به جای magic strings
3. ✅ **Component-Based Frontend**: Blade Components برای reusability
4. ✅ **Design System**: Tailwind config با tokens یکپارچه
5. ✅ **Modular CSS**: سازمان‌دهی CSS به base/components/utilities/pages/vendors
6. ✅ **View Models**: استفاده از ViewModels برای data preparation
7. ✅ **Policies**: Authorization با Laravel Policies
8. ✅ **Service Layer**: Services برای business logic (Cart, TrafficLightAnalyzer)

---

## ⚠️ مشکلات و چالش‌های فعلی

### 1. ساختار Domain ناقص
- ❌ Domain `Auth/` و `Core/` خالی هستند
- ❌ Services در Domain‌ها به صورت پراکنده هستند
- ❌ عدم وجود Repository Pattern
- ❌ عدم وجود DTOs/Data Transfer Objects

### 2. Frontend Architecture
- ⚠️ برخی component‌ها هنوز inline styles دارند
- ⚠️ عدم وجود Storybook یا component documentation
- ⚠️ JavaScript در `app.js` زیاد بزرگ شده (نیاز به code splitting)

### 3. Backend Architecture
- ⚠️ Controllers هنوز business logic دارند (باید به Services منتقل شود)
- ⚠️ عدم وجود Form Requests برای validation
- ⚠️ عدم وجود Events/Listeners برای decoupling
- ⚠️ عدم وجود Action classes برای single responsibility

### 4. Testing
- ❌ Test coverage بسیار پایین
- ❌ عدم وجود Integration tests برای critical flows
- ❌ عدم وجود Feature tests برای Livewire components

### 5. Code Organization
- ⚠️ برخی Models در `app/Models` هستند (Category, User) که باید به Domains منتقل شوند
- ⚠️ ViewModels فقط برای Home page است، نیاز به گسترش
- ⚠️ عدم وجود Contracts/Interfaces برای abstraction

### 6. Performance
- ⚠️ عدم وجود Caching strategy برای queries
- ⚠️ عدم وجود API versioning
- ⚠️ عدم وجود CDN strategy برای assets

### 7. Documentation
- ⚠️ عدم وجود API documentation
- ⚠️ عدم وجود Architecture Decision Records (ADRs)
- ⚠️ عدم وجود Component documentation

---

## 🎯 اهداف معماری

### کوتاه‌مدت (۱-۲ ماه)
1. تکمیل ساختار Domain (Auth, Core)
2. اضافه کردن Repository Pattern
3. انتقال business logic از Controllers به Services
4. اضافه کردن Form Requests
5. بهبود Test coverage به 60%+

### میان‌مدت (۳-۶ ماه)
1. پیاده‌سازی Event-driven architecture
2. اضافه کردن Action classes
3. Code splitting برای JavaScript
4. پیاده‌سازی Caching strategy
5. API versioning

### بلند‌مدت (۶+ ماه)
1. Microservices migration (در صورت نیاز)
2. Advanced monitoring و observability
3. Performance optimization
4. Advanced security measures

---

## ❓ سوالات کلیدی برای Gemini

### سوال ۱: ساختار Domain
**سوال**: آیا ساختار Domain فعلی ما (Modular Monolith با DDD) برای یک پلتفرم آموزش آنلاین مناسب است؟ آیا باید Repository Pattern اضافه کنیم یا از Eloquent مستقیم استفاده کنیم؟ بهترین practice برای Laravel 12 چیست؟

### سوال ۲: Service Layer
**سوال**: چگونه باید Service classes را سازمان‌دهی کنیم؟ آیا باید در هر Domain یک Service folder داشته باشیم؟ یا یک Application Service layer جداگانه؟ تفاوت بین Domain Services و Application Services در Laravel چیست؟

### سوال ۳: Action Classes
**سوال**: آیا Action classes (single-purpose classes) بهتر از Service classes برای Laravel هستند؟ چه زمانی باید از Action استفاده کنیم و چه زمانی از Service؟ آیا Laravel Action packages (مثل spatie/laravel-actions) توصیه می‌شود؟

### سوال ۴: Event-Driven Architecture
**سوال**: چگونه باید Event-driven architecture را در Laravel پیاده‌سازی کنیم؟ آیا برای یک پلتفرم آموزش آنلاین لازم است؟ چه نوع eventهایی باید داشته باشیم؟ (مثلاً CourseEnrolled, PaymentCompleted, etc.)

### سوال ۵: API Design
**سوال**: آیا باید API Resources (Laravel API Resources) استفاده کنیم؟ چگونه باید API versioning پیاده‌سازی کنیم؟ آیا GraphQL بهتر از REST برای این پروژه است؟

### سوال ۶: Caching Strategy
**سوال**: بهترین caching strategy برای Laravel چیست؟ Query caching, Model caching, یا Response caching؟ چگونه باید cache invalidation را مدیریت کنیم؟

### سوال ۷: Testing Strategy
**سوال**: بهترین testing strategy برای Laravel + Livewire چیست؟ چگونه باید Livewire components را test کنیم؟ آیا باید Feature tests بیشتری بنویسیم یا Unit tests؟

### سوال ۸: Frontend Architecture
**سوال**: آیا ساختار CSS فعلی (base/components/utilities/pages/vendors) بهترین است؟ آیا باید CSS Modules یا styled-components استفاده کنیم؟ چگونه باید JavaScript را code split کنیم؟

### سوال ۹: Performance
**سوال**: چگونه باید performance را optimize کنیم؟ آیا باید eager loading استفاده کنیم؟ آیا باید database indexing را بهبود دهیم؟ بهترین practices برای Laravel performance چیست؟

### سوال ۱۰: Security
**سوال**: چه security measures اضافی نیاز داریم؟ آیا باید rate limiting اضافه کنیم؟ آیا باید CSRF protection را بهبود دهیم؟ بهترین practices برای Laravel security چیست؟

### سوال ۱۱: Monitoring & Observability
**سوال**: چگونه باید monitoring و observability را پیاده‌سازی کنیم؟ آیا Laravel Telescope کافی است یا باید tools دیگری اضافه کنیم؟ چگونه باید logging strategy را بهبود دهیم؟

### سوال ۱۲: Scalability
**سوال**: چگونه باید برای scalability آماده شویم؟ آیا Modular Monolith کافی است یا باید به Microservices فکر کنیم؟ چه زمانی باید scale کنیم؟

---

## 📊 Metrics برای اندازه‌گیری موفقیت

### Code Quality
- Test Coverage: هدف 80%+
- Code Complexity: Cyclomatic Complexity < 10
- Code Duplication: < 3%

### Performance
- Page Load Time: < 2s
- API Response Time: < 200ms (p95)
- Database Query Time: < 50ms (p95)

### Maintainability
- Component Reusability: > 70%
- Code Documentation: > 80%
- Architecture Documentation: Complete

---

## 🔗 منابع و References

- Laravel 12 Documentation
- Filament 3 Documentation
- Livewire 3 Documentation
- Domain-Driven Design Patterns
- Clean Architecture by Robert C. Martin
- Laravel Best Practices

---

**تاریخ ایجاد**: ۲۰ دی ۱۴۰۳  
**آخرین بروزرسانی**: ۲۰ دی ۱۴۰۳

