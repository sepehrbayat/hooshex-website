# Quick Start After Livewire 500 Error Fix

## To restart your development environment:

### Option 1: Using npm (Recommended)
```bash
npm run start
```
This starts both the Laravel server (with 300s timeout) and Vite dev server.

### Option 2: Laravel server only
```bash
npm run serve
```
Runs on `http://127.0.0.1:7668`

### Option 3: Vite dev server only (if Laravel is already running elsewhere)
```bash
npm run dev
```
Runs on `http://127.0.0.1:6365`

## What Changed?

The `package.json` `serve` script now includes:
```
php -d max_execution_time=300
```

This increases the PHP execution time limit from 30 seconds to 300 seconds (5 minutes) for the development server.

## If you still see timeout errors:

1. **Check what's taking so long**:
   - Use Laravel Telescope: visit `/telescope` in your app
   - Check database queries with `php artisan query-dump`
   - Look for N+1 query problems in Livewire components

2. **Further increase timeout** (if needed):
   ```bash
   php -d max_execution_time=600 artisan serve --host 0.0.0.0 --port 7668
   ```

3. **Optimize your code**:
   - Add eager loading: `->with('relationships')`
   - Limit results: `->limit(10)`
   - Cache expensive queries: `->remember(3600)`
   - Queue long operations: `dispatch(new YourJob())`

---

For more details, see: `LIVEWIRE_500_ERROR_FIX.md`
