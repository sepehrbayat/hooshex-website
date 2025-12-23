# مشاوره معماری با Gemini - پروژه هوشکس (آپدیت شده)

## 📋 معرفی پروژه

من یک پلتفرم آموزش آنلاین به نام **هوشکس** دارم که با **Laravel 12 + Livewire 3 + Filament 3** ساخته شده است. می‌خواهم معماری پروژه را به بالاترین سطح استاندارد جهانی برسانم.

### مشخصات فنی فعلی:
- **Backend**: Laravel 12, PHP 8.2+
- **Frontend**: Livewire 3, Tailwind CSS v4, Alpine.js
- **Admin**: Filament 3
- **Database**: PostgreSQL (primary), Redis (cache/queue), Meilisearch (search)
- **Architecture**: Modular Monolith با Domain-Driven Design

---

## ✅ پیشرفت‌های انجام شده

### فاز ۱: تکمیل شده (100%)

1. ✅ **Models Migration**: User و Category به Domains منتقل شدند
2. ✅ **Action Classes**: 5 Action Class ایجاد شد (EnrollUser, AddToCart, CreateOrder, RemoveFromCart, CompletePayment)
3. ✅ **Form Requests**: 3 Form Request ایجاد شد (RequestOtp, VerifyOtp, SyncCart)
4. ✅ **Controllers Refactored**: همه Controllers برای استفاده از Actions و Form Requests refactor شدند

### ساختار فعلی:

```
app/Domains/
├── AiTools/Models/
├── Auth/Models/User.php ✅
├── Blog/Models/Post.php
├── Commerce/
│   ├── Actions/ ✅ (5 Actions)
│   ├── Models/Order.php, OrderItem.php
│   └── Services/Cart.php
├── Core/Models/Category.php ✅
└── Courses/
    ├── Actions/EnrollUserAction.php ✅
    └── Models/Course.php, Chapter.php, Lesson.php, Enrollment.php
```

---

## ❓ سوالات جدید برای شما (Gemini)

### 🎨 Frontend Architecture

#### سوال ۱: JavaScript Code Organization
**Context**: ما یک فایل `resources/js/app.js` داریم که 400+ خط کد دارد و شامل:
- Swiper initialization (testimonials, courses, blog)
- Cart management (hxCart object)
- Login modal (Alpine.js)
- Cart modal (Alpine.js)

**سوال**:
- آیا باید این کد را split کنیم به فایل‌های جداگانه؟ (مثلاً `swiper.js`, `cart.js`, `modals.js`)
- چگونه باید JavaScript modules را در Laravel + Vite organize کنیم؟
- آیا باید از ES6 modules استفاده کنیم؟
- بهترین practice برای Alpine.js components در Livewire 3 چیست؟

---

#### سوال ۲: Tailwind CSS v4 Architecture
**Context**: ما Tailwind CSS v4 با structure زیر داریم:
```
resources/css/
├── app.css
├── base/ (fonts, typography)
├── components/ (buttons, cards, forms, hero, features, ai-bot, sections, swiper)
├── utilities/ (rtl, animations, layout)
├── pages/ (خالی)
└── vendors/ (خالی)
```

**سوال**:
- آیا این ساختار برای یک پروژه بزرگ مناسب است؟
- آیا باید component styles را بیشتر granular کنیم؟
- چگونه باید CSS custom properties (CSS variables) را مدیریت کنیم؟
- آیا باید از `@layer` directive بیشتر استفاده کنیم؟

---

#### سوال ۳: Blade Components Organization
**Context**: ما Blade Components در `resources/views/components/` داریم:
- `home/` - Home page sections (hero, features, ai-bot, etc.)
- `ui/` - Reusable UI (button, section, section-header)
- `auth/` - Auth components (login-modal, profile-menu)
- `cart/` - Cart components (cart-modal)

**سوال**:
- آیا این organization مناسب است یا باید structure را تغییر دهیم؟
- آیا باید Livewire Components و Blade Components را جدا کنیم؟
- چگونه باید component props و slots را document کنیم؟
- آیا باید Storybook یا documentation tool اضافه کنیم؟

---

#### سوال ۴: Performance Optimization (Frontend)
**Context**: صفحه اصلی ما کند لود می‌شود. ما از:
- Livewire 3 برای SSR
- Swiper.js برای carousels
- Alpine.js برای interactivity

**سوال**:
- چگونه باید images را lazy load کنیم؟
- آیا باید code splitting برای JavaScript انجام دهیم؟
- چگونه باید Livewire performance را optimize کنیم؟
- آیا باید از `wire:navigate` بیشتر استفاده کنیم؟
- بهترین practice برای caching static assets چیست؟

---

### 🔧 Backend Architecture

#### سوال ۵: Service Layer vs Actions (تکمیل شده اما سوال داریم)
**Context**: ما 5 Action Class ایجاد کرده‌ایم و از Service Layer (مثل Cart Service) هم استفاده می‌کنیم.

**سوال**:
- آیا Cart Service را باید به Actions تبدیل کنیم یا نگه داریم؟
- چه زمانی باید از Service استفاده کنیم و چه زمانی از Action؟
- آیا باید Services را در Domain structure قرار دهیم یا در `app/Services`؟
- چگونه باید shared services را manage کنیم؟ (مثل Cart, Notification)

---

#### سوال ۶: Events and Listeners
**Context**: در CompletePaymentAction، ما comment کرده‌ایم که Events را در Phase 2 اضافه می‌کنیم.

**سوال**:
- چه Eventهایی باید داشته باشیم؟ (مثلاً OrderPaid, CourseEnrolled, UserRegistered)
- چگونه باید Event Listeners را organize کنیم؟
- آیا باید Events را در Domain structure قرار دهیم؟ (مثلاً `app/Domains/Commerce/Events/OrderPaid.php`)
- آیا باید Events را queue کنیم؟
- چگونه باید Event-driven architecture را test کنیم؟

---

#### سوال ۷: Exception Handling
**Context**: فعلاً در Actions از `\RuntimeException` استفاده می‌کنیم.

**سوال**:
- آیا باید Custom Exceptions بسازیم؟ (مثلاً `AlreadyEnrolledException`, `CartEmptyException`)
- چگونه باید exception handling را centralize کنیم؟
- آیا باید exception handling middleware اضافه کنیم؟
- چگونه باید errors را log کنیم و به کاربر نمایش دهیم؟

---

#### سوال ۸: API Design
**Context**: ما فعلاً فقط چند API endpoint داریم (auth/otp, cart/sync, payment/callback).

**سوال**:
- آیا باید Laravel API Resources استفاده کنیم؟
- چگونه باید API versioning پیاده‌سازی کنیم؟ (`/api/v1/...`)
- آیا باید API documentation (Swagger/OpenAPI) اضافه کنیم؟
- چگونه باید API authentication را مدیریت کنیم؟ (فعلاً Sanctum داریم)
- آیا باید API rate limiting اضافه کنیم؟

---

### 🗄️ Database Architecture

#### سوال ۹: Database Schema Design
**Context**: ما جداول زیر را داریم:
- `users` - کاربران
- `courses`, `chapters`, `lessons`, `enrollments` - دوره‌ها
- `orders`, `order_items` - سفارشات
- `posts`, `categories`, `categorizables` - بلاگ
- `ai_tools` - ابزارهای هوش مصنوعی
- `form_archives` - آرشیو فرم‌ها

**سوال**:
- آیا schema design ما optimal است؟
- آیا باید indexes بیشتری اضافه کنیم؟
- چگونه باید soft deletes را optimize کنیم؟
- آیا باید database partitioning استفاده کنیم؟ (برای جداول بزرگ)
- بهترین practice برای database migrations در تیم چیست؟

---

#### سوال ۱۰: Database Relationships
**Context**: ما از:
- Polymorphic relations استفاده می‌کنیم (categorizables, order_items)
- Many-to-Many relations (enrollments)
- HasMany/BelongsTo relations

**سوال**:
- آیا polymorphic relations ما optimal هستند؟
- چگونه باید eager loading را optimize کنیم؟
- آیا باید database foreign keys استفاده کنیم؟
- چگونه باید N+1 query problems را detect و fix کنیم؟
- بهترین practice برای database query optimization چیست؟

---

#### سوال ۱۱: Database Seeding and Factories
**Context**: ما Factory و Seeder برای User داریم.

**سوال**:
- آیا باید Factories برای همه Models بسازیم؟
- چگونه باید realistic test data generate کنیم؟
- آیا باید database seeding strategy بهبود دهیم؟
- چگونه باید seed data را برای different environments manage کنیم؟ (local, staging, production)

---

#### سوال ۱۲: Database Caching Strategy
**Context**: ما Redis برای cache استفاده می‌کنیم.

**سوال**:
- چه queryهایی را باید cache کنیم؟
- چگونه باید cache invalidation را manage کنیم؟
- آیا باید query result caching استفاده کنیم؟
- چگونه باید cache tags استفاده کنیم؟
- بهترین practice برای caching در Laravel چیست؟

---

### 🔄 Integration Questions

#### سوال ۱۳: Livewire + Actions Integration
**Context**: ما Livewire Components داریم (Home, AiTools/Grid, Courses/VideoPlayer) و Actions.

**سوال**:
- چگونه باید Actions را در Livewire Components استفاده کنیم؟
- آیا باید business logic را در Livewire Components نگه داریم یا به Actions منتقل کنیم؟
- چگونه باید data را از Actions به Livewire Components pass کنیم؟
- بهترین practice برای Livewire + Action architecture چیست؟

---

#### سوال ۱۴: Frontend-Backend Communication
**Context**: ما از:
- Livewire برای SSR
- Axios برای API calls (OTP, Cart sync)
- Alpine.js برای client-side interactivity

**سوال**:
- چگونه باید بین این سه تکنولوژی balance برقرار کنیم؟
- چه زمانی باید از Livewire استفاده کنیم و چه زمانی از API calls؟
- چگونه باید error handling را در frontend manage کنیم؟
- آیا باید یک centralized API client داشته باشیم؟

---

## 📊 درخواست نهایی

لطفاً بر اساس:

1. ✅ **اطلاعات موجود در پروژه**: فایل‌های موجود، structure فعلی، migrations
2. ✅ **پیشرفت‌های انجام شده**: فاز ۱ کامل شده (Actions, Form Requests, Controllers refactored)
3. ✅ **Stack تکنولوژی**: Laravel 12, Livewire 3, Tailwind CSS v4, Alpine.js
4. ✅ **Architecture فعلی**: Modular Monolith با DDD

به سوالات بالا پاسخ دهید و **بهترین practices** را برای هر مورد پیشنهاد دهید.

---

## 🔗 فایل‌های مهم برای بررسی

برای بررسی بیشتر، لطفاً فایل‌های زیر را بخوانید:

1. `ARCHITECTURE_REFACTORING_PROGRESS.md` - پیشرفت refactoring
2. `ARCHITECTURE_ANALYSIS_FOR_GEMINI.md` - تحلیل کامل معماری
3. Structure فعلی در `app/Domains/`
4. Frontend structure در `resources/views/components/` و `resources/css/`
5. Database migrations در `database/migrations/`

---

## 🎯 اولویت‌بندی

### فوری (این هفته):
1. Frontend performance optimization
2. JavaScript code organization
3. Exception handling strategy

### مهم (این ماه):
4. Events implementation
5. Database optimization
6. API design

### متوسط (۳-۶ ماه):
7. Advanced caching
8. API versioning
9. Documentation

---

**تاریخ**: ۲۰ دی ۱۴۰۳  
**نسخه**: 2.0 (آپدیت شده پس از تکمیل فاز ۱)

**لطفاً سوالات بالا را پاسخ دهید و بهترین practices را برای هر مورد پیشنهاد دهید. هدف ما ساختن معماری در سطح جهانی است! 🌍**
