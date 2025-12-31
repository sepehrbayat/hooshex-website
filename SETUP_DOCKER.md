# راهنمای نصب و راه‌اندازی Docker

## ⚠️ توجه مهم

Docker Desktop نیاز به نصب دستی دارد و نمی‌توان آن را به صورت خودکار نصب کرد. این راهنما مراحل نصب را توضیح می‌دهد.

---

## 📥 مراحل نصب Docker Desktop

### 1. دانلود Docker Desktop
- به آدرس زیر بروید: https://www.docker.com/products/docker-desktop/
- یا مستقیماً: https://desktop.docker.com/win/main/amd64/Docker%20Desktop%20Installer.exe
- فایل نصب را دانلود کنید

### 2. نصب Docker Desktop
- فایل نصب را اجرا کنید
- دستورالعمل‌های نصب را دنبال کنید
- پس از نصب، Docker Desktop را راه‌اندازی کنید
- صبر کنید تا Docker Engine راه‌اندازی شود (آیکون Docker در system tray سبز شود)

### 3. بررسی نصب
پس از نصب، در PowerShell/CMD اجرا کنید:
```powershell
docker --version
docker-compose --version
```

---

## 🚀 راه‌اندازی سرویس‌ها با Docker

پس از نصب Docker Desktop:

```bash
# رفتن به پوشه پروژه
cd C:\Users\dell\Documents\hooshex-website

# راه‌اندازی سرویس‌ها
docker-compose up -d

# بررسی وضعیت سرویس‌ها
docker-compose ps

# مشاهده لاگ‌ها
docker-compose logs -f
```

---

## ⚙️ تنظیمات .env برای Docker

اگر می‌خواهید از PostgreSQL/Redis/Meilisearch استفاده کنید، باید فایل `.env` را به‌روزرسانی کنید:

```env
# Database Configuration
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=hooshex
DB_USERNAME=hooshex
DB_PASSWORD=secret

# Redis Configuration (optional)
REDIS_HOST=127.0.0.1
REDIS_PORT=6379

# Meilisearch Configuration (optional)
MEILISEARCH_HOST=http://127.0.0.1:7700
MEILISEARCH_KEY=masterKey
```

---

## 🔄 راه‌اندازی Frontend (Vite)

در حالی که Docker در حال نصب است، می‌توانید Frontend را راه‌اندازی کنید:

```bash
# در ترمینال جدید
cd C:\Users\dell\Documents\hooshex-website
npm run dev
```

یا برای اجرای همزمان Laravel و Vite:

```bash
npm run start
```

---

## 📋 خلاصه دستورات

```bash
# 1. بررسی نصب Docker
docker --version

# 2. راه‌اندازی Docker services
docker-compose up -d

# 3. بررسی وضعیت
docker-compose ps

# 4. توقف سرویس‌ها
docker-compose down

# 5. مشاهده لاگ‌ها
docker-compose logs -f postgres
docker-compose logs -f redis
docker-compose logs -f meilisearch
```

---

## ⚠️ اگر Docker نصب نشد

اگر نمی‌خواهید Docker را نصب کنید، می‌توانید از SQLite استفاده کنید (که در حال حاضر فعال است).

برای استفاده از SQLite، فایل `.env` باید این تنظیمات را داشته باشد:

```env
DB_CONNECTION=sqlite
# DB_DATABASE=  # می‌تواند خالی باشد یا database/database.sqlite
```

---

## 🆘 عیب‌یابی

### Docker Desktop راه‌اندازی نمی‌شود
- مطمئن شوید Windows Subsystem for Linux (WSL 2) نصب است
- Hyper-V را فعال کنید
- Virtualization را در BIOS فعال کنید

### پورت‌ها در حال استفاده هستند
اگر پورت‌های 5432، 6379، یا 7700 در حال استفاده هستند:
- در `docker-compose.yml` پورت‌ها را تغییر دهید
- یا در `.env` با استفاده از متغیرهای محیطی پورت‌ها را تغییر دهید

---

**تاریخ ایجاد**: 2025-12-27

