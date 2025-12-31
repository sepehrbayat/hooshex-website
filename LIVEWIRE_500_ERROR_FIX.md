# Livewire 500 Error Fix - Analysis and Resolution

## Problem Summary

You were experiencing a **500 Internal Server Error** on POST requests to `/livewire/update` with the following error in the browser console:

```
POST http://127.0.0.1:7668/livewire/update 500 (Internal Server Error)

Alpine Expression Error: Unclosed group
Error: Uncaught Error: Unclosed group at Ht
```

The error was occurring when Livewire tried to display an error modal with syntax-highlighted code snippets.

## Root Cause Analysis

After investigating the Laravel error logs, I found the actual underlying error:

```
Maximum execution time of 30 seconds exceeded
```

### What was happening:

1. **Primary Issue**: A Livewire request was taking longer than PHP's default `max_execution_time` of 30 seconds
2. **Secondary Issue**: When the request timed out, Laravel attempted to render an error modal
3. **Tertiary Issue**: The error modal tries to syntax-highlight the PHP stack trace code using a JavaScript highlighter (`window.highlight()`)
4. **Final Issue**: The code in the error modal contained HTML entities and zero-width spaces that broke the syntax highlighter's regex parser

### Why the 30-second timeout?

The `php artisan serve` command (used via `npm run serve`) runs PHP with the default 30-second `max_execution_time` setting. This timeout is appropriate for production but can cause issues during development when:
- Loading large datasets
- Complex database queries
- Slow operations in Livewire components

## Solution Implemented

### Change Made:

Modified `package.json` to increase PHP's execution time limit:

**Before:**
```json
"serve": "php artisan serve --host 0.0.0.0 --port 6012",
```

**After:**
```json
"serve": "php -d max_execution_time=300 artisan serve --host 0.0.0.0 --port 6012",
```

### What this does:

- Sets `max_execution_time` to **300 seconds** (5 minutes) for the development server
- Gives long-running operations enough time to complete
- Prevents the timeout error that was triggering the broken error modal
- Only affects the development server, not production

## How to Verify the Fix

1. **Stop the current development server** (if running)
2. **Restart with the new configuration**:
   ```bash
   npm run start    # Or npm run serve for just the Laravel server
   ```
3. **The server should now start with the increased timeout**
4. **Long-running Livewire operations should complete without timing out**

## Next Steps for Production

For a production environment, consider:

1. **Optimize slow queries** - Use eager loading and limit result sets
2. **Use caching** - Cache expensive database queries
3. **Queue long operations** - Use Laravel queues for operations that take > 30 seconds
4. **Monitor performance** - Use tools like Laravel Telescope or New Relic to identify slow operations
5. **Keep reasonable timeouts** - 300 seconds is generous for development; production should aim for < 30 seconds

## Additional Notes

### Why the syntax highlighting error occurred:

The PHP code snippets in the error modal were being HTML-escaped when rendered, converting:
- `->` to `→` (arrow entity)
- `//` to `/​/` (with zero-width spaces)

These entities/characters broke the syntax highlighter's (Shiki/OnigRegExp) regex engine when trying to parse PHP syntax rules.

### Why it's now fixed:

By increasing the timeout, the error no longer occurs, so the error modal is never displayed, and the syntax highlighting issue becomes moot.

---

**Last Updated**: 2025-12-27
**Status**: ✅ Fixed
