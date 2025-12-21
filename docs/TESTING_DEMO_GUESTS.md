# Testing Temporary Guest System

## How to Test:

### 1. Open Demo Page in Multiple Browsers/Tabs

```
http://localhost/welcome
```

**Important**: Use different browsers or incognito mode to simulate different users.

### 2. Create Lessons Simultaneously

In each browser:
- Select a topic
- Click "Create Demo Lesson"
- Wait for lesson creation

**Expected result**: 
- Each guest sees only their own lesson
- Lessons don't overlap between sessions

### 3. Check Database

```bash
# Enter container
docker compose exec laravel.test bash

# Connect to MySQL
mysql -u sail -p'password' ai_demo

# Check temporary guests
SELECT id, email, is_temp_guest, temp_guest_expires_at 
FROM users 
WHERE is_temp_guest = true;

# Check their lessons
SELECT l.id, l.lesson_name, u.email 
FROM lessons l 
JOIN users u ON l.user_id = u.id 
WHERE u.is_temp_guest = true;
```

### 4. Test Cleanup

```bash
# Create test expired guest
docker compose exec laravel.test php artisan tinker

>>> $user = \App\Models\User::create([
...     'name' => 'Test Expired Guest',
...     'email' => 'test_expired@temp.local',
...     'password' => bcrypt('test'),
...     'email_verified_at' => now(),
...     'native_lang' => 'vi',
...     'learning_lang' => 'en',
...     'role' => 'student',
...     'is_temp_guest' => true,
...     'temp_guest_expires_at' => now()->subHours(1),
... ]);
>>> exit

# Run cleanup
docker compose exec laravel.test php artisan guests:cleanup --force

# Verify user was deleted
docker compose exec laravel.test php artisan tinker
>>> \App\Models\User::where('email', 'test_expired@temp.local')->count()
=> 0
```

### 5. Test Timeout (2 minutes)

1. Create a lesson as guest
2. Wait 2 minutes
3. An error with "Try Again" button should appear
4. Click "Try Again" - should create new lesson

### 6. Check Scheduler

```bash
# Check registered tasks
docker compose exec laravel.test php artisan schedule:list

# Should show task:
# guests:cleanup --force  Daily at 03:00  Clean up expired temporary guest users
```

## What to Check:

✅ Each session creates its own temporary user  
✅ Lessons are isolated between sessions  
✅ Expired users are deleted by cleanup command  
✅ Timeout works (2 minutes)  
✅ Retry button creates new lesson  
✅ Scheduler configured for automatic cleanup  

## Troubleshooting:

### Error when creating lesson:
```bash
# Check logs
docker compose exec laravel.test tail -f storage/logs/laravel.log
```

### Users not being deleted:
```bash
# Check is_temp_guest and expires_at
SELECT * FROM users WHERE is_temp_guest = true;

# Run cleanup with output
docker compose exec laravel.test php artisan guests:cleanup
```

### Queue not processing:
```bash
# Check queue worker
docker compose exec laravel.test php artisan queue:work --once
```
