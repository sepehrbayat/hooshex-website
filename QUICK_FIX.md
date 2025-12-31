# Quick Fix: Create Careers Table

## The Problem
The `careers` table doesn't exist in your database. Your CLI PHP (8.3.29) doesn't match the project requirement (8.4.0+), but your web server is using PHP 8.4.16.

## Solution: Use the Artisan Command

I've created a custom Artisan command that will create the table. You need to run it with PHP 8.4.

### Step 1: Find Your PHP 8.4 Installation

Since your web server is running on PHP 8.4.16, it's installed somewhere. Common locations:

1. **Check your web server configuration:**
   - If using XAMPP: `C:\xampp\php\php.exe`
   - If using Laragon: `C:\laragon\bin\php\php-8.4\php.exe`
   - If using WAMP: `C:\wamp64\bin\php\php8.4\php.exe`
   - If using standalone: Check where you installed PHP 8.4

2. **Or check running processes:**
   ```powershell
   Get-Process php | Select-Object Path
   ```

### Step 2: Run the Command

Once you find PHP 8.4, run:

```bash
<path-to-php84>\php.exe artisan careers:create-table
```

For example:
```bash
C:\xampp\php\php.exe artisan careers:create-table
```

### Step 3: Verify

After running the command, refresh:
```
http://localhost:6012/admin/careers
```

## Alternative: Temporary Route (Less Secure)

If you can't find PHP 8.4, I can create a temporary route that creates the table when you visit it. This is less secure but works as a quick fix. Let me know if you want this option.

## Alternative: Use DB Browser

1. Download DB Browser for SQLite: https://sqlitebrowser.org/
2. Open `database/database.sqlite`
3. Go to "Execute SQL" tab
4. Copy and paste the SQL from `create-careers-table.sql`
5. Execute it

