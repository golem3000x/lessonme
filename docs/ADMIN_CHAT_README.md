# Admin Panel for Chat Management

## What's Implemented

### ✅ Filament Admin Panel
- **Path:** `/admin`
- **Stack:** Filament 3.2 + Laravel 11 + Livewire 3
- **Authentication:** Only for users with `is_admin = true`

### ✅ Administrator Role
- **Migration:** `2025_11_16_095140_add_is_admin_to_users_table.php`
- **Model:** `User` implements `FilamentUser` interface
- **Field:** `is_admin` (boolean, default: false)

### ✅ Chat Management
**Resource:** `ChatSessionResource`

**Features:**
- 📋 List all chats with filtering
- 🔴 Badge with unread chat count
- 👁️ View message history
- ✉️ Send replies to users
- 🔒 Close/open chats
- 🔍 Search by username
- 📊 Sort by last message date

**Chat Table:**
- User (name + email)
- Last message (date and time)
- Unread (badge)
- Status (open/closed)

**Filters:**
- By status (open/closed)
- With unread messages

### ✅ WebSocket Broadcasting
**Admin Channel:** `admin-chat`

**Authorization:** `routes/channels.php`
```php
Broadcast::channel('admin-chat', function ($user) {
    return $user->is_admin;
});
```

**Message Flow:**
1. User sends message
2. `MessageSent` event → WebSocket
3. Message arrives in `chat.{userId}` channel (to user)
4. Message arrives in `admin-chat` channel (to all admins)
5. Admin sees notification in Filament

**When admin replies:**
1. Admin sends message with `is_from_admin = true`
2. `MessageSent` event → WebSocket
3. Message arrives only in `chat.{userId}` (to user)
4. User sees reply in real-time

---

## File Structure

```
app/
├── Events/
│   └── MessageSent.php                              # MODIFIED - admin broadcasting
├── Filament/
│   └── Resources/
│       ├── ChatSessionResource.php                  # NEW - chat resource
│       └── ChatSessionResource/
│           └── Pages/
│               ├── ListChatSessions.php             # NEW - chat list
│               └── ViewChatSession.php              # NEW - view + reply
├── Models/
│   └── User.php                                     # MODIFIED - FilamentUser interface
└── Providers/
    └── Filament/
        └── AdminPanelProvider.php                   # NEW - Filament config

database/
└── migrations/
    └── 2025_11_16_095140_add_is_admin_to_users_table.php  # NEW

resources/
└── views/
    └── filament/
        └── resources/
            └── chat-session/
                └── send-message-form.blade.php      # NEW - reply form

routes/
└── channels.php                                     # MODIFIED - admin-chat channel
```

---

## Setup

### 1. Run Migration

```bash
docker compose exec laravel.test php artisan migrate
```

### 2. Create Admin User

**Option A: Via Tinker**
```bash
docker compose exec laravel.test php artisan tinker
```

```php
$user = User::find(1); // Or create new
$user->is_admin = true;
$user->save();
```

**Option B: SQL**
```sql
UPDATE users SET is_admin = 1 WHERE email = 'admin@example.com';
```

**Option C: Seeder**
Create `database/seeders/AdminSeeder.php`:

```php
<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'is_admin' => true,
            'native_lang' => 'vi',
            'learning_lang' => 'en',
            'email_verified_at' => now(),
        ]);
    }
}
```

Run:
```bash
docker compose exec laravel.test php artisan db:seed --class=AdminSeeder
```

### 3. Clear Cache

```bash
docker compose exec laravel.test php artisan optimize:clear
```

### 4. Open Admin Panel

Go to: **http://your-domain.com/admin**

Login with admin credentials.

---

## Usage

### Chat List

1. Open `/admin/chat-sessions`
2. See all user chats
3. Badge shows unread chat count
4. Use filters to search

### Reply to Message

1. Click "Open" on desired chat
2. View message history
3. Enter reply in form below
4. Click "Send"
5. Message sends via WebSocket to user

### Close Chat

1. Open chat
2. Click "Close Chat" in top right corner
3. Chat closes, reply form hides
4. Can reopen with "Open Chat" button

---

## Real-time WebSocket

### For Real-time Updates to Work

**1. Start Laravel Reverb:**
```bash
docker compose exec laravel.test php artisan reverb:start
```

**2. Start Queue Worker:**
```bash
docker compose exec laravel.test php artisan queue:work
```

**3. Start Vite:**
```bash
npm run dev
```

### How It Works:

**When user writes:**
1. Message saved to DB
2. Sent to `chat.{userId}` channel (to user)
3. Sent to `admin-chat` channel (to all admins)
4. Badge in admin menu updates automatically
5. Chat list updates

**When admin replies:**
1. Message with `is_from_admin = true`
2. Sent only to user
3. User sees reply instantly
4. User's unread counter increases

---

## Security

### Authorization

✅ **Only admins** can access `/admin`
```php
public function canAccessPanel(Panel $panel): bool
{
    return $this->is_admin;
}
```

✅ **WebSocket channel protected:**
```php
Broadcast::channel('admin-chat', function ($user) {
    return $user->is_admin; // Only admins
});
```

### Validation

✅ Messages validated (max: 1000 characters)
✅ XSS protection via Blade (automatic escaping)
✅ CSRF protection (Laravel + Livewire)

---

## Future Improvements

### Priority:
- [ ] **Real-time updates** of chat view page via Echo.js
- [ ] **Desktop notifications** for new messages
- [ ] **Sound notification** for new messages
- [ ] **Typing indicator** - "User is typing..."
- [ ] **Rate limiting** for spam protection

### Optional:
- [ ] Quick reply templates
- [ ] Statistics (average response time, processed chats count)
- [ ] Chat history export
- [ ] Message content search
- [ ] Tags for chat categorization
- [ ] Assign chats to specific admins
- [ ] Action history (who replied when)

---

## FAQ

### Q: How to add multiple admins?
**A:** Set `is_admin = true` for needed users:
```php
User::whereIn('email', ['admin1@example.com', 'admin2@example.com'])
    ->update(['is_admin' => true]);
```

### Q: How to remove admin rights?
**A:**
```php
User::where('email', 'user@example.com')->update(['is_admin' => false]);
```

### Q: Do all admins receive new message notifications?
**A:** Yes, all admins connected to WebSocket receive messages in `admin-chat` channel.

### Q: Can admin panel design be customized?
**A:** Yes, in `app/Providers/Filament/AdminPanelProvider.php` you can change colors, add dark theme, etc.

### Q: Does chat work without WebSocket?
**A:** Yes, messages save to DB. But real-time updates require WebSocket (Reverb).

---

## Troubleshooting

### Can't login to admin
- Check `is_admin = true` in DB
- Clear cache: `php artisan optimize:clear`
- Check using correct email/password

### Real-time notifications not arriving
- Start Reverb: `php artisan reverb:start`
- Start Queue Worker: `php artisan queue:work`
- Check `.env` settings (BROADCAST_CONNECTION=reverb)
- Open DevTools → Console - check WebSocket errors

### Badge not updating
- Refresh page
- Check `unread_count` updates correctly in DB
- Clear cache

---

**Created:** 2025-11-16  
**Version:** 1.0.0  
**Stack:** Laravel 11 + Filament 3.2 + Livewire 3 + Laravel Reverb + Tailwind CSS
