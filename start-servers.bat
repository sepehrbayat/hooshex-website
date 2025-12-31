@echo off
REM اسکریپت راه‌اندازی سرورها - HooshEx Website
REM این اسکریپت تمام سرویس‌های مورد نیاز را راه‌اندازی می‌کند

echo.
echo ========================================
echo   راه‌اندازی سرورهای HooshEx
echo ========================================
echo.

REM بررسی Docker Desktop
echo [1/3] بررسی Docker Desktop...
docker ps >nul 2>&1
if errorlevel 1 (
    echo.
    echo ⚠️  Docker Desktop در حال اجرا نیست!
    echo    لطفاً Docker Desktop را باز کنید و دوباره تلاش کنید.
    pause
    exit /b 1
)

REM راه‌اندازی Docker Services
echo [2/3] راه‌اندازی سرویس‌های Docker...
docker compose up -d
if errorlevel 1 (
    echo.
    echo ❌ خطا در راه‌اندازی Docker services!
    pause
    exit /b 1
)

timeout /t 3 /nobreak >nul

echo [3/3] راه‌اندازی Laravel و Vite...
echo.
echo 🚀 در حال راه‌اندازی Laravel (پورت 7668) و Vite (پورت 6365)...
echo.
echo 📝 برای توقف سرورها، Ctrl+C را فشار دهید
echo.

REM راه‌اندازی npm run start
call npm run start

pause

