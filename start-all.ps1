# اسکریپت کامل راه‌اندازی - HooshEx Website
# این اسکریپت همه چیز را بررسی و راه‌اندازی می‌کند

param(
    [switch]$SkipDocker = $false
)

$ErrorActionPreference = "Stop"

Write-Host ""
Write-Host "╔═══════════════════════════════════════════════════╗" -ForegroundColor Cyan
Write-Host "║   راه‌اندازی کامل سرورهای HooshEx Website      ║" -ForegroundColor Cyan
Write-Host "╚═══════════════════════════════════════════════════╝" -ForegroundColor Cyan
Write-Host ""

$scriptRoot = Split-Path -Parent $MyInvocation.MyCommand.Path
Set-Location $scriptRoot

# مرحله 1: بررسی Docker
if (-not $SkipDocker) {
    Write-Host "[1/4] بررسی Docker Desktop..." -ForegroundColor Yellow
    try {
        docker ps | Out-Null
        Write-Host "    ✅ Docker Desktop در حال اجرا است" -ForegroundColor Green
    } catch {
        Write-Host "    ❌ Docker Desktop در حال اجرا نیست!" -ForegroundColor Red
        Write-Host "    لطفاً Docker Desktop را باز کنید و دوباره تلاش کنید." -ForegroundColor Yellow
        exit 1
    }

    # راه‌اندازی Docker Services
    Write-Host "[2/4] راه‌اندازی سرویس‌های Docker..." -ForegroundColor Yellow
    docker compose up -d
    
    if ($LASTEXITCODE -ne 0) {
        Write-Host "    ❌ خطا در راه‌اندازی Docker services!" -ForegroundColor Red
        exit 1
    }
    
    Write-Host "    ✅ Docker services راه‌اندازی شدند" -ForegroundColor Green
    Start-Sleep -Seconds 3
} else {
    Write-Host "[1/4] رد کردن Docker (--SkipDocker فعال است)" -ForegroundColor Gray
    Write-Host "[2/4] رد کردن Docker (--SkipDocker فعال است)" -ForegroundColor Gray
}

# مرحله 3: بررسی وابستگی‌ها
Write-Host "[3/4] بررسی وابستگی‌ها..." -ForegroundColor Yellow

if (-not (Test-Path "node_modules")) {
    Write-Host "    📦 نصب وابستگی‌های Node.js..." -ForegroundColor Cyan
    npm install
    if ($LASTEXITCODE -ne 0) {
        Write-Host "    ❌ خطا در نصب وابستگی‌های Node.js!" -ForegroundColor Red
        exit 1
    }
}

if (-not (Test-Path "vendor")) {
    Write-Host "    📦 نصب وابستگی‌های Composer..." -ForegroundColor Cyan
    composer install
    if ($LASTEXITCODE -ne 0) {
        Write-Host "    ❌ خطا در نصب وابستگی‌های Composer!" -ForegroundColor Red
        exit 1
    }
}

Write-Host "    ✅ وابستگی‌ها آماده هستند" -ForegroundColor Green

# مرحله 4: راه‌اندازی سرورها
Write-Host "[4/4] راه‌اندازی Laravel و Vite..." -ForegroundColor Yellow
Write-Host ""
Write-Host "═══════════════════════════════════════════════════" -ForegroundColor Cyan
Write-Host "   🌐 سرورها در حال راه‌اندازی..." -ForegroundColor Green
Write-Host "═══════════════════════════════════════════════════" -ForegroundColor Cyan
Write-Host ""
Write-Host "📌 Laravel:  http://127.0.0.1:7668" -ForegroundColor White
Write-Host "📌 Vite:     http://127.0.0.1:6365" -ForegroundColor White
Write-Host ""
Write-Host "📌 پنل کاربری:  http://127.0.0.1:7668/app/login" -ForegroundColor Cyan
Write-Host "📌 پنل ادمین:   http://127.0.0.1:7668/admin/login" -ForegroundColor Cyan
Write-Host ""
Write-Host "⏹️  برای توقف سرورها: Ctrl+C" -ForegroundColor Yellow
Write-Host ""
Write-Host "═══════════════════════════════════════════════════" -ForegroundColor Cyan
Write-Host ""

# متوقف کردن سرورهای قبلی (اگر وجود دارند)
$nodeProcesses = Get-Process -Name node -ErrorAction SilentlyContinue | Where-Object { $_.Path -like "*$scriptRoot*" }
if ($nodeProcesses) {
    Write-Host "⚠️  متوقف کردن سرورهای قبلی..." -ForegroundColor Yellow
    Stop-Process -Name node -Force -ErrorAction SilentlyContinue
    Start-Sleep -Seconds 2
}

# راه‌اندازی سرورها
npm run start

