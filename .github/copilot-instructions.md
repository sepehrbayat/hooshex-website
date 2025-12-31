## HooshEx — Copilot Instructions

Essential knowledge for AI agents working in this Persian-first, RTL Laravel monolith.

### Architecture Overview

**Modular Monolith** organized under `app/Domains/*`:
- `AiTools/` — AI tool directory with metadata
- `Auth/` — Custom OTP auth, legacy WordPress password migration
- `Blog/` — Posts, comments, traffic light analyzer
- `Commerce/` — Orders, cart service, payment integration
- `Courses/` — Course/chapter/lesson hierarchy, enrollments, teachers
- `Core/` — Shared utilities (clicks tracking, redirects)

**Action-Event-Listener Pattern**: Business logic lives in `Actions/` (e.g., `CreateOrderFromCartAction`, `EnrollUserAction`). Events dispatched via `Events/`, handled by `Listeners/`. See [app/Domains/Commerce/Actions/CreateOrderFromCartAction.php](app/Domains/Commerce/Actions/CreateOrderFromCartAction.php) for canonical example.

**Tech Stack**: Laravel 11/12, PHP 8.2+, Filament v3 (admin), Livewire 3 (UI), Tailwind v4, PostgreSQL, Redis, Meilisearch, Zarinpal payments.

### Developer Workflows

```bash
# Start both servers (recommended)
npm run start  # Laravel:7668 + Vite:3001

# Separate starts
npm run serve  # Laravel on custom port (see package.json)
npm run dev    # Vite HMR only

# Background jobs
php artisan horizon  # Redis-backed queues

# Legacy import (one-way WordPress → Laravel)
php artisan import:legacy  # See MIGRATION_INSTRUCTIONS.md

# Tests
vendor/bin/phpunit  # PHPUnit (config: phpunit.xml)
```

**Port Configuration**: Laravel runs on `127.0.0.1:7668`, Vite on `127.0.0.1:3001` (custom to avoid conflicts). See [README.md](README.md#L28-L42) and [vite.config.js](vite.config.js).

### Critical RTL Patterns

**Flexbox in RTL** (`dir="rtl"`):
- `justify-start` → aligns RIGHT (not left)
- `justify-end` → aligns LEFT (not right)
- HTML order reverses visually: `<Icon /><Text />` displays icon on right

**Example** (right-aligned icon in RTL):
```html
<div class="flex flex-row" dir="rtl">
  <svg><!-- icon first in DOM --></svg>
  <span class="justify-start">متن فارسی</span>
</div>
```

See [.cursorrules](./cursorrules#L1-L8) for comprehensive RTL rules.

### Project-Specific Conventions

1. **No Magic Strings**: Use Enums in `app/Enums/` (`OrderStatus`, `CourseStatus`, `UserRole`, etc.)
2. **Alpine.js Bundled**: Livewire 3 includes Alpine — never install separately. Use `window.Alpine.data()` via `alpine:init` event.
3. **Tailwind Important Strategy**: `important: '#app'` in [tailwind.config.js](tailwind.config.js#L18) for third-party overrides. Extend tokens instead of arbitrary values.
4. **Strict Typing**: All new code requires PHP 8.2+ strict types (`declare(strict_types=1)`) and full type hints.
5. **Form Validation**: Use Form Requests or Filament validation. Policies for authorization.
6. **Layout**: Prefer flex/grid; avoid absolute positioning. See [.cursorrules](./cursorrules#L72-L76) for frontend checklist.

### Integration Points

**Payments** ([config/payment.php](config/payment.php)):
- Default gateway: `zarinpal` (configurable via `PAYMENT_GATEWAY` env)
- Callback URL: `PAYMENT_CALLBACK_URL` (default: `/payment/callback`)
- Payment flow: Cart → `CreateOrderFromCartAction` → Zarinpal → `CompletePaymentAction` → `OrderPaid` event

**Authentication**:
- OTP-based (SMS.ir): `POST /auth/otp/request`, `POST /auth/otp/verify` (throttled 10/min)
- Legacy WordPress users: `LegacyUserProvider` handles bcrypt hash migration
- See [routes/web.php](routes/web.php#L18-L21)

**Search**: Meilisearch via Laravel Scout. Sync indexes: `php artisan scout:sync-index-settings`.

**Admin Access**: Filament panels at `/admin` (protected by auth). Resources in `app/Filament/Admin/Resources/`.

### File Locations & Examples

**Domain Models**: `app/Domains/*/Models/` (e.g., `Courses/Models/Course.php`, `Commerce/Models/Order.php`)  
**Filament Resources**: `app/Filament/Admin/Resources/` (13 resources: AiTool, Course, Post, Career, etc.)  
**Blade Components**: `resources/views/components/` (layouts, home sections, auth modals, cart)  
**Migrations**: `database/migrations/core/` for domain migrations  
**Legacy Import**: [app/Console/Commands/ImportLegacy.php](app/Console/Commands/ImportLegacy.php) — handles WP users, posts, WooCommerce orders

### Common Pitfalls

1. **Missing Migrations**: If "no such table" error, check PHP version mismatch (project requires 8.2+). Run `php artisan migrate` with correct PHP binary (see [MIGRATION_INSTRUCTIONS.md](MIGRATION_INSTRUCTIONS.md)).
2. **RTL Visual Bugs**: Icon on wrong side? Reverse HTML order. Text misaligned? Check `justify-start` vs `justify-end`.
3. **Alpine Conflicts**: Don't install `alpinejs` in `package.json` — Livewire bundles it.
4. **CSS Overrides**: Global Figma CSS files (`desktop-figma.css`, etc.) may conflict. Use Tailwind utilities and proper specificity instead of `!important`.

### What to Ask vs Change

**Change freely**: Tailwind classes, Blade templates, Filament forms, migration tweaks, test additions.  
**Ask first**: Payment gateway switches, production env changes (ports, credentials), schema-breaking DB changes, business logic alterations.

### Testing Strategy

**Feature Tests**: `tests/Feature/` — critical flows (course enrollment, checkout, legacy import). Run via `vendor/bin/phpunit`.  
**Test DB**: SQLite (`database/database.sqlite`) for speed. Migrations auto-run in `TestCase::setUp()`.

### Reference Documents

- [.cursorrules](./.cursorrules) — Frontend rules, RTL patterns, pitfalls checklist
- [README.md](README.md) — Dev setup, ports, environment variables
- [ARCHITECTURE_ANALYSIS_FOR_GEMINI.md](ARCHITECTURE_ANALYSIS_FOR_GEMINI.md) — Detailed domain structure
- [CURSOR_AI_PROMPT.md](CURSOR_AI_PROMPT.md) — Example prompts and fixes

---

**Questions?** If domain boundaries are unclear or you need examples of Filament patterns, event wiring, or Livewire component structure, ask for targeted snippets with file references.
