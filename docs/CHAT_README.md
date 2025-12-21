# Chat Feature - Guide

## What's Implemented

### 1. ✅ About Page
- **Path:** `/about`
- **Files:**
  - `routes/web.php` - route
  - `resources/views/about.blade.php` - page
  - `resources/lang/*/nav.php` - localization
- **Navigation:** Link added to main menu

### 2. ✅ Chat Button (Bottom Right Corner)
- **Component:** `app/Livewire/ChatButton.php`
- **View:** `resources/views/livewire/chat-button.blade.php`
- **Features:**
  - Beautiful circular button with gradient
  - Pulsing animation for attention
  - Badge with unread message count
  - Tooltip on hover
  - Auto-appears only for authenticated users

### 3. ✅ Database
- **Migrations:**
  - `create_chat_sessions_table.php` - chat sessions
  - `create_messages_table.php` - messages
- **Models:**
  - `app/Models/ChatSession.php`
  - `app/Models/Message.php`
- **Enum:** Added `CHAT_SESSIONS` and `MESSAGES` to `TableNameEnum`

### 4. ✅ WebSocket Broadcasting
- **Event:** `app/Events/MessageSent.php`
- **Channel:** `routes/channels.php` - channel authorization `chat.{userId}`
- **Laravel Reverb:** Uses existing configuration

### 5. ✅ Livewire Chat Component
- **Component:** `app/Livewire/ChatPanel.php`
- **View:** `resources/views/livewire/chat-panel.blade.php`
- **Features:**
  - Message history display (last 50)
  - Real-time message sending
  - Auto-scroll to last message
  - Mark messages as read
  - Message validation (max 1000 chars)
  - Beautiful UI with animations

### 6. ✅ Echo.js Integration
- **File:** `resources/js/echo.js`
- **Features:**
  - Subscribe to private channel `chat.{userId}`
  - Listen to `.message.sent` event
  - Dispatch Livewire event for UI update

---

## How It Works

### Message Sending Flow:

1. **User enters message** in `ChatPanel`
2. **Livewire sends** via `wire:submit="sendMessage"`
3. **Validation** in `ChatPanel::sendMessage()`
4. **Save to DB** via `Message` model
5. **Broadcasting** via `MessageSent` event
6. **Laravel Reverb** sends to WebSocket
7. **Echo.js receives** `.message.sent` event
8. **Livewire updates UI** via `message-received` dispatch
9. **Auto-scroll** via Alpine.js

### Architecture:

```
User Input (Blade)
    ↓
Livewire Component (ChatPanel.php)
    ↓
Model (Message::create)
    ↓
Event (MessageSent)
    ↓
Laravel Reverb (WebSocket)
    ↓
Echo.js (Frontend)
    ↓
Livewire Dispatch (message-received)
    ↓
UI Update (Blade)
```

---

## File Structure

```
app/
├── Enums/
│   └── TableNameEnum.php          # +CHAT_SESSIONS, +MESSAGES
├── Events/
│   └── MessageSent.php            # Broadcasting event
├── Livewire/
│   ├── ChatButton.php             # Chat button
│   └── ChatPanel.php              # Chat panel
├── Models/
│   ├── ChatSession.php            # Session model
│   ├── Message.php                # Message model
│   └── User.php                   # +chatSessions(), +messages()

database/
└── migrations/
    ├── *_create_chat_sessions_table.php
    └── *_create_messages_table.php

resources/
├── js/
│   └── echo.js                    # +chat channel listener
├── lang/
│   ├── en/nav.php                 # +'about' key
│   └── vi/nav.php                 # +'about' key
└── views/
    ├── about.blade.php            # About page
    ├── components/
    │   └── app-layout.blade.php   # +chat button, +chat panel
    └── livewire/
        ├── chat-button.blade.php  # Button UI
        └── chat-panel.blade.php   # Chat panel UI

routes/
├── channels.php                   # +chat.{userId} authorization
└── web.php                        # +/about route

CHAT_SECURITY.md                   # Security documentation
CHAT_README.md                     # This guide
```

---

## Running and Testing

### 1. Ensure Laravel Reverb is Running

```bash
docker compose exec laravel.test php artisan reverb:start
```

Or check it's running in background.

### 2. Ensure Queue Worker is Running

```bash
docker compose exec laravel.test php artisan queue:work
```

Broadcasting works through queues.

### 3. Compile Frontend

```bash
npm run dev
# or
npm run build
```

### 4. Open Application

1. Log in to the system
2. Chat button with pulse appears in bottom right corner
3. Click the button - chat panel opens
4. Enter message and send

### 5. Test WebSocket

**Browser console should show:**
```
🔥 Chat message received: {id: 1, message: "...", ...}
```

**If messages aren't arriving:**
- Check Reverb is running
- Check `.env` Reverb settings
- Open DevTools → Network → WS and check WebSocket connection
- Check browser console for errors

---

## .env Settings

Ensure you have these settings:

```env
BROADCAST_CONNECTION=reverb
QUEUE_CONNECTION=database

REVERB_APP_ID=your-app-id
REVERB_APP_KEY=your-app-key
REVERB_APP_SECRET=your-secret
REVERB_HOST=localhost
REVERB_PORT=8080
REVERB_SCHEME=http

VITE_REVERB_APP_KEY="${REVERB_APP_KEY}"
VITE_REVERB_HOST="${REVERB_HOST}"
VITE_REVERB_PORT="${REVERB_PORT}"
VITE_REVERB_SCHEME="${REVERB_SCHEME}"
```

---

## Future Improvements

### Priority:
- [ ] **Rate Limiting** - limit message frequency
- [ ] **Logging** - save message send history
- [ ] **Admin Panel** - admin view of all chats
- [ ] **Typing indicator** - "typing..." indicator

### Optional:
- [ ] File/image sending
- [ ] Emoji picker
- [ ] Markdown support
- [ ] Sound notifications
- [ ] Desktop notifications
- [ ] Chat history with pagination
- [ ] Message search
- [ ] Old chat archiving
- [ ] Chat history export

---

## Security

Detailed security documentation in `CHAT_SECURITY.md`

**Key points:**
- ✅ Private channel authorization
- ✅ Input data validation
- ✅ XSS protection via Blade
- ⚠️ Rate Limiting - recommended to add
- ⚠️ Logging - recommended to add

---

## FAQ

### Q: How can admin respond to users?
**A:** Not implemented yet. Need to create admin panel with component for viewing all chats and sending messages with `is_from_admin = true` flag.

### Q: How to delete a message?
**A:** Delete function not implemented. Can add method to `ChatPanel`:
```php
public function deleteMessage($messageId)
{
    Message::where('id', $messageId)
        ->where('user_id', auth()->id())
        ->delete();
}
```

### Q: Can chat be disabled for specific user?
**A:** Yes, add `is_blocked` field to `chat_sessions` table and check it before sending.

### Q: How many messages load when opening chat?
**A:** Last 50 messages. Can change in `ChatPanel::loadChatSession()`:
```php
->limit(50) // Change to desired amount
```

### Q: Does chat work offline?
**A:** No, requires active WebSocket connection. Messages sent offline will be lost.

---

## Support

If issues occur:
1. Check Laravel logs: `storage/logs/laravel.log`
2. Check browser console for JavaScript errors
3. Check Reverb is running
4. Check Queue Worker is running
5. Check file permissions

---

**Created:** 2025-11-16  
**Version:** 1.0.0  
**Stack:** Laravel 11, Livewire 3.5+, Laravel Reverb, Alpine.js, TailwindCSS
