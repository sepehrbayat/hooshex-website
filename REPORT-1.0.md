# Backend & DB Progress Report (v1.0) — File-by-File Detail

## Infrastructure & Config
- `docker-compose.yml`: Added Postgres/Redis/Meilisearch services (env-driven ports/creds, persistent volumes).
- `bootstrap/app.php`: Middleware aliases (Sanctum, enrolled), web stack includes ConvertNumbersToPersian, providers registered (App/Auth/Filament Admin), CSRF exception for payment callback.
- `config/app.php`: Locale `fa`, timezone `Asia/Tehran`, app name HooshEx.
- `config/auth.php`: Sanctum API guard; provider set to `legacy-eloquent`.
- `config/services.php`: SMS.ir block.
- `config/database.php`: Added `legacy` MySQL connection.
- `config/payment.php`: Zarinpal/payment defaults and callback URL.
- `README.md`: Env/ops requirements (DB, Redis, Meilisearch, SMS.ir, Zarinpal, legacy DB, admin email).
- `.cursorrules`: Project operating rules captured.

## Middleware & Providers
- `app/Http/Middleware/ConvertNumbersToPersian.php`: HTML digit localization.
- `app/Http/Middleware/EnsureUserIsEnrolled.php`: Enrollment gate.
- `app/Providers/AppServiceProvider.php`: Registers Horizon (all envs) and Telescope (local), legacy provider binding.
- `app/Providers/AuthServiceProvider.php`: Policies map; admin-email Gate override.
- `app/Providers/Filament/AdminPanelProvider.php`: Panel setup, resource/page/widget discovery, basic nav item.

## Auth & OTP
- `app/Auth/LegacyUserProvider.php`: WP MD5 fallback with promotion to bcrypt.
- `app/Services/SmsIrClient.php`: OTP send wrapper.
- `app/Http/Controllers/Auth/OtpController.php`: OTP request/verify (cache-backed), auto user via mobile.
- Routes: `/auth/otp/*` (web+api) with throttling.

## Models, Enums, Schemas
- Enums: `app/Enums/{OrderStatus,PricingType,CourseStatus,PostStatus,PostType,UserRole}.php`.
- Users: `database/migrations/0001...users` adds username, mobile, legacy_password, bio, avatar_path, social_links; `app/Models/User.php` fillables/casts aligned.
- AiTools: `database/migrations/ai_tools/...` soft deletes, indexes; model `App/Domains/AiTools/Models/AiTool` uses Scout, pricing enum cast, relations.
- Posts: `database/migrations/blog/...` soft deletes, indexes; model `App/Domains/Blog/Models/Post` uses enums + Scout.
- Courses: `database/migrations/courses/...` (courses/chapters/lessons/enrollments); models Course/Chapter/Lesson/Enrollment with casts/relations.
- Commerce: `database/migrations/commerce/...` (orders/order_items with status index); models Order/OrderItem with enum cast.
- Core: `database/migrations/core/...` form_archives.
- Categories: `categories` + `categorizables`; model `App/Models/Category.php`.
- Cart: `app/Domains/Commerce/Services/Cart.php`.

## Seeders
- `database/seeders/CategorySeeder.php`: Default AI/post categories.
- `database/seeders/AdminUserSeeder.php`: Admin user via `APP_ADMIN_EMAIL`.
- `database/seeders/DatabaseSeeder.php`: Calls CategorySeeder, AdminUserSeeder.

## Admin (Filament)
- Resources:
  - `app/Filament/Admin/Resources/AiToolResource.php` (+Pages) with SEO fields, pricing, categories, logo upload, toggles.
  - `app/Filament/Admin/Resources/PostResource.php` (+Pages) with type/status, SEO, categories, thumbnail upload.
  - `app/Filament/Admin/Resources/CourseResource.php` (+Pages) with pricing/status/featured/intro URL.
- Widget: `TrafficLightWidget` + view; analyzer service `TrafficLightAnalyzer`.
- Panel: `AdminPanelProvider` configured.

## Frontend (server-rendered)
- AI tools listing: `resources/views/ai-tools/index.blade.php`, Livewire `app/Http/Livewire/AiTools/Grid.php` + view (facets).
- Video player stub: `app/Http/Livewire/Courses/VideoPlayer.php` + view.
- Tailwind: `tailwind.config.js` (Vazirmatn, colors); `resources/css/app.css` imports Vazirmatn.

## Commerce & Payments
- `app/Http/Controllers/Payments/PaymentController.php`: Zarinpal checkout via shetabit/payment, order creation, callback verification, enrollment provisioning.
- Route: `/payment/callback`.

## Legacy Import
- `app/Console/Commands/ImportLegacy.php`: Imports users (mobile/bio/legacy pwd), AI tools (meta), posts, Elementor submissions archive, orders + addresses + items with status mapping. Uses legacy DB connection.

## Search
- Scout enabled on AiTool/Post via `toSearchableArray` (Meilisearch required; index sync pending manual run).

## Tests (Pest)
- `tests/Feature/OtpTest.php`: OTP request/verify.
- `tests/Feature/AiToolFilterTest.php`: Pricing filter listing.
- `database/factories/AiToolFactory.php`: Factory for tests.

## Gaps / Caveats
- Env not configured/tested (DB/Redis/Meilisearch/SMS.ir/Zarinpal/APP_KEY). Composer previously used `--ignore-platform-req=ext-intl`; enable ext-intl.
- Payment UX minimal (plain responses); no discounts/taxes; legacy order items imported with null orderable_type (no product binding).
- Scout index settings not synced; run imports after Meilisearch up.
- Filament/Horizon/Telescope route protection minimal (admin-email override only); roles/guards need hardening.
- Tests are starter-level; no payment/import end-to-end or browser/UI coverage.
- Uploads use default disk; configure S3/minio if required.

## Ready-to-Run Checklist
1) Set env: DB (Postgres), Redis, Meilisearch, SMS.ir, Zarinpal, legacy DB, `APP_ADMIN_EMAIL`, `APP_KEY`, `APP_URL`.
2) `composer install` (with ext-intl), `npm install && npm run build`, `php artisan migrate --seed`.
3) Start services: `docker-compose up -d`; `php artisan horizon`; Telescope (local).
4) Import legacy: set `LEGACY_*`, run `php artisan import:legacy`.
5) Scout: `php artisan scout:import "App\\Domains\\AiTools\\Models\\AiTool"` and `...Post`.
6) Tests: `php artisan test`.

