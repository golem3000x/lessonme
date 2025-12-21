# Migration from Polling to WebSocket for Demo Guests

## What Was Changed

### ❌ Old Approach (Polling):

```blade
<!-- Poll every 5 seconds -->
<div wire:poll.5s="checkLessonStatus">
```

**Problems:**
- Constant HTTP requests every 5 seconds
- Up to 5 second delay before showing lesson
- Server load
- Inefficient resource usage

### ✅ New Approach (WebSocket):

**Technologies:**
- Laravel Reverb (WebSocket server)
- Echo.js (WebSocket client)
- Livewire Events
- Laravel Notifications + Broadcasting

**Advantages:**
- ⚡ Instant real-time notifications
- 📉 Reduced server load (no polling)
- 🔔 Uses existing notification system
- 🎯 Isolation between different guest sessions

## Key Changes

### 1. Channel Authorization

**routes/channels.php:**

```php
Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});
```

**IMPORTANT:** For WebSocket notifications to work with temporary guests, `EnsureTempGuestUser` middleware is used, which automatically logs in the temporary user for private channel access.

**Protection:**
- ✅ Temporary guests have limited access (demo pages only)
- ✅ Automatic cleanup of expired guests
- ✅ Check `is_temp_guest` and `temp_guest_expires_at`

### 2. Meta Tags for Echo.js

**resources/views/components/guest-layout.blade.php:**
```blade
<meta name="csrf-token" content="{{ csrf_token() }}">

@if(session('temp_guest_user_id'))
    <meta name="user-id" content="{{ session('temp_guest_user_id') }}">
@elseif(auth()->check())
    <meta name="user-id" content="{{ auth()->id() }}">
@endif
```

**Why:** 
- `csrf-token` - **required** for broadcast authentication (otherwise 419 error)
- `user-id` - Echo.js uses it to connect to private channel
- Session priority for temp guests, then auth for regular users

### 3. Axios Configuration for CSRF

**resources/js/bootstrap.js:**
```javascript
// Get CSRF token from meta tag
const token = document.head.querySelector('meta[name="csrf-token"]');

if (token) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
} else {
    console.error('CSRF token not found');
}
```

**Why:** Axios sends CSRF token with broadcast auth requests

### 4. Livewire Event Listener

**app/Livewire/Welcome/DemoLessonCreator.php:**
```php
#[On('lesson-notification')]
public function handleLessonNotification(array $notification): void
{
    if ($notification['status'] === 'successfully') {
        $this->demo_lesson_url = $notification['lesson_url'];
        $this->demo_lesson_name = $notification['lesson_name'];
        $this->show_success_message = true;
        // ...
    }
}
```

**Why:** Handles notifications from Echo.js.

### 5. Fallback polling

For reliability, `checkLessonStatus()` method is kept as fallback in case of WebSocket issues:
- Timeout: 3 minutes
- Checks lesson existence in DB if WebSocket didn't deliver notification

## Workflow

```
1. Guest opens /welcome
   ↓
2. DemoLessonCreator::mount() creates temp user (WITHOUT login!)
   ↓
3. temp_guest_user_id saved in session
   ↓
4. Echo.js connects to App.Models.User.{user_id} (from session)
   ↓
5. Guest creates lesson
   ↓
6. CreateLessonJob executes
   ↓
7. Job sends LessonCreationNotification
   ↓
8. Laravel Reverb broadcasts to WebSocket
   ↓
9. Echo.js receives .lesson.creation.successfully event
   ↓
10. Echo.js calls Livewire.dispatch('lesson-notification')
    ↓
11. DemoLessonCreator::handleLessonNotification() processes
    ↓
12. UI updates instantly ⚡
```

## 🔒 Security

### Middleware EnsureTempGuestUser

Temporary guests are managed via `EnsureTempGuestUser` middleware:

```php
// app/Http/Middleware/EnsureTempGuestUser.php
// Automatically creates and authorizes temporary user
```

**Protection:**
- ✅ Temporary guests have `is_temp_guest = true` flag
- ✅ Expiration via `temp_guest_expires_at`
- ✅ Limited access to demo pages only
- ✅ Automatic cleanup of expired guests

## Existing Infrastructure

All WebSocket infrastructure **was already configured**:

### ✅ Backend:
- `AbstractLessonNotification` - base notification class
- `LessonCreationNotification` - lesson creation notification
- `CreateLessonJob` - sends notifications
- `routes/channels.php` - broadcast channel authorization

### ✅ Frontend:
- `resources/js/echo.js` - already listens to `lesson.creation.*` events
- `Echo.js` configured and working
- Laravel Reverb running

## What Didn't Change

- ✅ Temporary user system works as before
- ✅ Expired guests cleanup works
- ✅ Session isolation preserved
- ✅ All existing notifications continue to work

## Testing

### 1. Check WebSocket Connection

Open DevTools → Network → WS:
- Should have active WebSocket connection
- URL: `ws://localhost:8080/app/{APP_KEY}`

### 2. Check Notifications

Create a lesson and check Console:
```
🔥 Lesson creation success: {...}
DemoLessonCreator received notification: {...}
```

### 3. Check No Polling

Network → XHR:
- Should NOT have repeating requests every 5 seconds

## Requirements

### Services Must Be Running:

```bash
# Laravel Reverb (WebSocket server)
docker compose exec laravel.test php artisan reverb:start

# Queue Worker (notification processing)
docker compose exec laravel.test php artisan queue:work
```

### Configuration (.env):

```env
BROADCAST_CONNECTION=reverb
QUEUE_CONNECTION=database

REVERB_APP_ID=your_app_id
REVERB_APP_KEY=your_key
REVERB_APP_SECRET=your_secret
REVERB_HOST=localhost
REVERB_PORT=8080
REVERB_SCHEME=http
```

## Troubleshooting

### Notifications Not Arriving?

1. Check that Reverb is running: `docker compose ps`
2. Check that Queue Worker is working
3. Check `user-id` meta tag in HTML
4. Check Console for WebSocket errors
5. Check that user is logged in: `auth()->check()`

### WebSocket Not Connecting?

1. Check `REVERB_*` variables in `.env`
2. Check that port 8080 is accessible
3. Clear cache: `php artisan optimize:clear`

## Migration Benefits

| Metric | Polling | WebSocket |
|--------|---------|-----------|
| Delay | Up to 5 sec | ~100ms |
| Load | Constant requests | Single connection |
| Scalability | Poor | Excellent |
| UX | Delays | Instant |
| Server resources | High | Low |

## Conclusion

WebSocket migration:
- ✅ Uses existing infrastructure
- ✅ Minimal code changes
- ✅ Significantly improves UX
- ✅ Reduces server load
- ✅ Preserves all temporary guest system benefits
