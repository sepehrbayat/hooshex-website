# اسکریپت راه‌اندازی سرورها - HooshEx Website
# این اسکریپت تمام سرویس‌های مورد نیاز را راه‌اندازی می‌کند

Write-Host "🚀 راه‌اندازی سرورهای HooshEx..." -ForegroundColor Cyan
Write-Host ""

# بررسی Docker Desktop
Write-Host "📦 بررسی Docker Desktop..." -ForegroundColor Yellow
$dockerRunning = docker ps 2>&1
if ($LASTEXITCODE -ne 0) {
    Write-Host "⚠️  Docker Desktop در حال اجرا نیست!" -ForegroundColor Red
    Write-Host "   لطفاً Docker Desktop را باز کنید و دوباره تلاش کنید." -ForegroundColor Yellow
    exit 1
}

# راه‌اندازی Docker Services
Write-Host "🐳 راه‌اندازی سرویس‌های Docker..." -ForegroundColor Yellow
Set-Location $PSScriptRoot
docker compose up -d

if ($LASTEXITCODE -ne 0) {
    Write-Host "❌ خطا در راه‌اندازی Docker services!" -ForegroundColor Red
    exit 1
}

Write-Host "✅ Docker services راه‌اندازی شدند" -ForegroundColor Green
Start-Sleep -Seconds 3

# بررسی پورت‌ها
Write-Host ""
Write-Host "🔍 بررسی پورت‌ها..." -ForegroundColor Yellow

$ports = @(7668, 6365, 5432, 6379, 7700)
$portNames = @{
    7668 = "Laravel"
    6365 = "Vite"
    5432 = "PostgreSQL"
    6379 = "Redis"
    7700 = "Meilisearch"
}

foreach ($port in $ports) {
    $listening = Get-NetTCPConnection -LocalPort $port -State Listen -ErrorAction SilentlyContinue
    if ($listening) {
        Write-Host "✅ $($portNames[$port]) (پورت $port) در حال اجرا" -ForegroundColor Green
    } else {
        Write-Host "⚠️  $($portNames[$port]) (پورت $port) در حال اجرا نیست" -ForegroundColor Yellow
    }
}

Write-Host ""
Write-Host "🎯 راه‌اندازی Laravel و Vite..." -ForegroundColor Yellow
Write-Host ""

# بررسی اینکه npm run start در حال اجرا نیست
$existingProcess = Get-Process -Name node -ErrorAction SilentlyContinue | Where-Object { $_.Path -like "*$PSScriptRoot*" }
if ($existingProcess) {
    Write-Host "⚠️  سرورهای قبلی در حال اجرا هستند. در حال متوقف کردن..." -ForegroundColor Yellow
    Stop-Process -Name node -Force -ErrorAction SilentlyContinue
    Start-Sleep -Seconds 2
}

# راه‌اندازی Laravel و Vite
Write-Host "🚀 در حال راه‌اندازی Laravel (پورت 7668) و Vite (پورت 6365)..." -ForegroundColor Cyan
Write-Host ""
Write-Host "📝 برای توقف سرورها، Ctrl+C را فشار دهید" -ForegroundColor Yellow
Write-Host ""

# راه‌اندازی npm run start
npm run start

