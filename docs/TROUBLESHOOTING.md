# Troubleshooting Guide

## ❌ Error 419 When Connecting to WebSocket

### Problem:
```
POST http://127.0.0.1/broadcast/auth
Status: 419 (CSRF token mismatch)
```

### Cause:
Laravel Reverb requires CSRF token for broadcast connection authentication. Without the token in meta tags and axios configuration, all broadcast auth requests will return 419 error.

### Solution:

#### 1. Add CSRF token to layout

**resources/views/components/guest-layout.blade.php:**
```blade
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}"> <!-- ✅ Required! -->
    <title>{{ config('app.name') }}</title>
    
    @if(session('temp_guest_user_id'))
        <meta name="user-id" content="{{ session('temp_guest_user_id') }}">
    @elseif(auth()->check())
        <meta name="user-id" content="{{ auth()->id() }}">
    @endif
    <!-- ... -->
</head>
```

#### 2. Configure axios for CSRF

**resources/js/bootstrap.js:**
```javascript
import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// ✅ Get CSRF token from meta tag
const token = document.head.querySelector('meta[name="csrf-token"]');

if (token) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
} else {
    console.error('CSRF token not found: https://laravel.com/docs/csrf#csrf-x-csrf-token');
}
```

#### 3. Rebuild assets

```bash
docker compose exec laravel.test npm run build
# or for development
docker compose exec laravel.test npm run dev
```

#### 4. Clear cache

```bash
docker compose exec laravel.test php artisan optimize:clear
```

### Verification:

1. Open DevTools → Network → XHR
2. Find request to `/broadcast/auth`
3. Check Headers → Request Headers
4. Should have header: `X-CSRF-TOKEN: ...`

**Before fix:**
```
Status: 419 CSRF token mismatch
```

**After fix:**
```
Status: 200 OK
Response: {"auth": "..."}
```

---

## 🔍 Other Common Issues

### WebSocket Not Connecting

**Check:**
1. Is Laravel Reverb running: `docker compose ps`
2. Port 8080 is accessible
3. Environment variables in `.env`:
   ```env
   BROADCAST_CONNECTION=reverb
   REVERB_APP_ID=...
   REVERB_APP_KEY=...
   REVERB_APP_SECRET=...
   REVERB_HOST=localhost
   REVERB_PORT=8080
   REVERB_SCHEME=http
   ```

### Queue Worker Not Processing Jobs

**Check:**
1. Is queue worker running: `php artisan queue:work`
2. DB connection is correct
3. Logs: `tail -f storage/logs/laravel.log`

### Notifications Not Arriving

**Check:**
1. CSRF token configured ✅
2. User ID in meta tag ✅
3. Echo.js listening to correct channel ✅
4. Laravel Reverb running ✅
5. Queue Worker working ✅

**Console should show:**
```javascript
🔥 Lesson creation success: {...}
DemoLessonCreator received notification: {...}
```

---

## 📚 Useful Commands

### Check Configuration
```bash
# Check broadcast driver
docker compose exec laravel.test php artisan config:show broadcasting

# Check queues
docker compose exec laravel.test php artisan queue:list

# Check scheduler
docker compose exec laravel.test php artisan schedule:list
```

### Debugging
```bash
# Laravel logs
docker compose exec laravel.test tail -f storage/logs/laravel.log

# Reverb logs
docker compose logs -f reverb

# Queue Worker logs
docker compose logs -f queue
```

### Assets
```bash
# Production build
docker compose exec laravel.test npm run build

# Development watch
docker compose exec laravel.test npm run dev

# Clean
docker compose exec laravel.test npm run clean
```

### Cache
```bash
# Clear all
docker compose exec laravel.test php artisan optimize:clear

# Config only
docker compose exec laravel.test php artisan config:clear

# Views only
docker compose exec laravel.test php artisan view:clear

# Routes only
docker compose exec laravel.test php artisan route:clear
```
