# How to Fix the "no such table: careers" Error

## Problem
The `careers` table doesn't exist in your database because the migrations haven't been run. Your CLI PHP version (8.3.29) doesn't match the project requirement (PHP 8.4.0+), while your web server is using PHP 8.4.16.

## Solution Options

### Option 1: Run Migrations with PHP 8.4 (Recommended)

1. **Find your PHP 8.4 installation:**
   - Check your web server configuration to find where PHP 8.4 is installed
   - Common locations on Windows:
     - `C:\php\php.exe`
     - `C:\xampp\php\php.exe`
     - `C:\laragon\bin\php\php-8.4\php.exe`
     - Or check your web server's PHP path

2. **Run migrations using PHP 8.4:**
   ```bash
   # Replace <path-to-php84> with your actual PHP 8.4 path
   <path-to-php84>\php.exe artisan migrate
   ```

3. **Or use the helper script:**
   ```bash
   <path-to-php84>\php.exe run-migrations.php
   ```

### Option 2: Install PHP 8.4 for CLI

1. **Download PHP 8.4 for Windows:**
   - Visit: https://windows.php.net/download/
   - Download PHP 8.4 Thread Safe version
   - Extract to `C:\php84\` or similar

2. **Add to PATH:**
   - Add `C:\php84\` to your system PATH
   - Restart your terminal

3. **Verify:**
   ```bash
   php -v
   # Should show PHP 8.4.x
   ```

4. **Run migrations:**
   ```bash
   php artisan migrate
   ```

### Option 3: Manual SQL Creation (Quick Fix)

If you need a quick fix and can't run migrations right now:

1. **Using SQLite command line:**
   ```bash
   sqlite3 database/database.sqlite < create-careers-table.sql
   ```

2. **Or using a SQLite GUI tool:**
   - Open `database/database.sqlite` in a SQLite browser
   - Run the SQL from `create-careers-table.sql`

## Verify the Fix

After running migrations or the SQL script, refresh your browser at:
```
http://localhost:6012/admin/careers
```

The error should be resolved and you should see the careers listing page.

## Notes

- The migrations will create the `careers` table with all required columns
- The refactor migration adds additional columns for job postings
- Both migrations are safe to run multiple times (they check if columns exist)

