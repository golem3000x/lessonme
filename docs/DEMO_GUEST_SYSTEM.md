# Demo Guest User System

## Problem

In demo mode, all guests used a single shared user (`guest@system.local`), which led to:
- Conflicts when multiple guests created lessons simultaneously
- Ability to see other guests' lessons
- Database cluttering with all guests' lessons

## Solution

Each guest session gets a **temporary user**:

### Temporary User Characteristics:

- **Unique email**: `demo_{unique_id}@temp.local`
- **Flag**: `is_temp_guest = true`
- **Lifetime**: 24 hours (`temp_guest_expires_at`)
- **Session storage**: `temp_guest_user_id`

### Automatic Cleanup

The system automatically deletes expired temporary guests and their lessons:

```bash
# Manual run
php artisan guests:cleanup

# Force cleanup without confirmation
php artisan guests:cleanup --force
```

**Automatic run**: Every day at 03:00 (configured in `routes/console.php`)

### Workflow:

1. **Guest visits page** → Temp user created (WITHOUT login!)
2. **User ID saved in session** → Used for WebSocket authorization
3. **Temp user connects to WebSocket** → Private channel for notifications
4. **Guest creates lesson** → Lesson linked to temp user
5. **Lesson ready** → Notification arrives via WebSocket (not polling!)
6. **Guest works with lesson** → All data isolated from other guests
7. **After 24 hours** → Automatic cleanup deletes user and their lessons

### 🔒 Security

Temporary guests are managed via `EnsureTempGuestUser` middleware:

- ✅ Automatic temporary user creation
- ✅ Limited access to demo pages only
- ✅ `is_temp_guest = true` flag for identification
- ✅ Automatic expiration via `temp_guest_expires_at`
- ✅ No access to dashboard, API, admin panel

**Channel Authorization:**

```php
// routes/channels.php
Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});
```

### WebSocket Notifications:

**Real-time WebSocket notifications** are used with polling fallback:

- ✅ **Instant updates** - lesson appears immediately after creation
- ✅ **Laravel Reverb** - built-in WebSocket server
- ✅ **Echo.js** - automatic connection and event handling
- ✅ **Fallback polling** - `checkLessonStatus()` with 3 minute timeout

**Technical details:**
- User ID stored in session (`temp_guest_user_id`)
- Connects to private channel: `App.Models.User.{user_id}`
- Listens to events: `lesson.creation.successfully`, `lesson.creation.error`, `lesson.creation.exists`
- Livewire component handles `lesson-notification` event

### Components:

#### 1. Migration
```
database/migrations/2025_11_19_173600_add_temp_guest_fields_to_users_table.php
```
Adds `is_temp_guest` and `temp_guest_expires_at` fields

#### 2. Command
```
app/Console/Commands/CleanupTempGuestUsers.php
```
Command for cleaning up expired temporary guests

#### 3. Scheduler
```
routes/console.php
```
Automatic cleanup run every day

#### 4. Component Logic
```
app/Livewire/Welcome/DemoLessonCreator.php
```
Method `ensureTempGuestUser()` creates and manages temporary users

### Advantages:

✅ Full isolation between guest sessions  
✅ No conflicts during simultaneous work  
✅ Automatic cleanup of old data  
✅ No authentication required  
✅ Foreign key constraints work correctly  

### Lifetime Configuration:

Default: **24 hours**

Change in `DemoLessonCreator.php`:
```php
'temp_guest_expires_at' => now()->addHours(24), // <- here
```

### Monitoring:

Active temporary guests count:
```sql
SELECT COUNT(*) FROM users 
WHERE is_temp_guest = true 
AND temp_guest_expires_at > NOW();
```

Expired guests count (ready for deletion):
```sql
SELECT COUNT(*) FROM users 
WHERE is_temp_guest = true 
AND temp_guest_expires_at < NOW();
```
