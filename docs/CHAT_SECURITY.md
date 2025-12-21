# Chat Security

## Table of Contents
1. [Channel Authorization](#channel-authorization)
2. [Data Validation](#data-validation)
3. [XSS Protection](#xss-protection)
4. [Rate Limiting](#rate-limiting)
5. [Monitoring and Logging](#monitoring-and-logging)
6. [Improvement Recommendations](#improvement-recommendations)

---

## Channel Authorization

### Current Implementation

**File:** `routes/channels.php`

```php
Broadcast::channel('chat.{userId}', function ($user, $userId) {
    return (int) $user->id === (int) $userId;
});
```

**How it works:**
- Each user can only subscribe to their own chat channel
- Laravel automatically verifies that `$user->id` matches `$userId` in channel name
- Unauthorized users cannot connect to private channels

**Protection:**
- ✅ User cannot listen to other users' channels
- ✅ Administrator can send messages to any user
- ✅ WebSocket connections protected by Laravel Sanctum/Session

---

## Data Validation

### Message Sending Validation

**File:** `app/Livewire/ChatPanel.php`

```php
public function sendMessage(): void
{
    $this->validate([
        'new_message' => 'required|string|max:1000',
    ]);
    
    // ... создание сообщения
}
```

**What is validated:**
- ✅ Message is not empty (`required`)
- ✅ Message is a string (`string`)
- ✅ Maximum length 1000 characters (`max:1000`)

### Validation Improvement Recommendations

**1. Add additional rules:**

```php
$this->validate([
    'new_message' => [
        'required',
        'string',
        'max:1000',
        'min:1',
        // Disallow only spaces
        'regex:/^(?!\s*$).+/',
    ],
]);
```

**2. Filter unwanted content:**

```php
use Illuminate\Support\Str;

// Remove extra spaces
$this->new_message = trim($this->new_message);
$this->new_message = preg_replace('/\s+/', ' ', $this->new_message);

// Filter HTML tags (if HTML not needed)
$this->new_message = strip_tags($this->new_message);
```

---

## XSS Protection

### Automatic Blade Protection

**File:** `resources/views/livewire/chat-panel.blade.php`

```blade
<p class="text-sm break-words">{{ $message->message }}</p>
```

**How it works:**
- Blade syntax `{{ }}` automatically escapes HTML
- All special characters (`<`, `>`, `&`, `"`, `'`) are converted to HTML entities
- Protection against JavaScript code injection

**Example:**
```
Input: <script>alert('XSS')</script>
Output: &lt;script&gt;alert('XSS')&lt;/script&gt;
```

### ❌ DO NOT USE:

```blade
<!-- DANGEROUS! Never use {!! !!} for user content -->
<p>{!! $message->message !!}</p>
```

### Additional Protection

**Content Security Policy (CSP):**

Add to `.env`:
```env
CSP_ENABLED=true
```

Add middleware to `app/Http/Kernel.php`:
```php
protected $middlewareGroups = [
    'web' => [
        // ...
        \Illuminate\Http\Middleware\AddContentSecurityPolicyHeaders::class,
    ],
];
```

---

## Rate Limiting

### Current State
⚠️ **Rate limiting not implemented**

### Implementation Recommendations

**1. Route-level throttle (for API):**

```php
// routes/web.php
Route::middleware(['auth', 'throttle:60,1'])->group(function () {
    // 60 requests per minute
});
```

**2. Throttle in Livewire component:**

```php
use Illuminate\Support\Facades\RateLimiter;

public function sendMessage(): void
{
    $key = 'send-message:'.$this->chat_session->id;
    
    if (RateLimiter::tooManyAttempts($key, 10)) {
        $seconds = RateLimiter::availableIn($key);
        
        $this->addError('new_message', 
            "Too many messages. Try again in {$seconds} seconds."
        );
        
        return;
    }
    
    RateLimiter::hit($key, 60); // 10 messages per minute
    
    // ... send message
}
```

**3. Global WebSocket rate limiting:**

In `config/broadcasting.php`:
```php
'reverb' => [
    'connections' => [
        'max_request_size' => 10000,
        'max_frame_size' => 10000,
        'max_message_size' => 10000,
    ],
],
```

---

## Monitoring and Logging

### Recommended Events for Logging

**1. Message sending logging:**

```php
// app/Livewire/ChatPanel.php
use Illuminate\Support\Facades\Log;

public function sendMessage(): void
{
    // ... валидация
    
    $message = Message::create([...]);
    
    Log::info('Chat message sent', [
        'user_id' => auth()->id(),
        'chat_session_id' => $this->chat_session->id,
        'message_id' => $message->id,
        'ip' => request()->ip(),
    ]);
    
    // ... broadcast
}
```

**2. Suspicious activity logging:**

```php
// Log multiple failed attempts
if ($this->validate(...) === false) {
    Log::warning('Invalid message attempt', [
        'user_id' => auth()->id(),
        'error' => $this->getErrorBag(),
        'ip' => request()->ip(),
    ]);
}
```

**3. WebSocket connection monitoring:**

```php
// In MessageSent Event
Log::channel('chat')->info('Message broadcasted', [
    'message_id' => $this->message->id,
    'user_id' => $this->message->user_id,
    'channel' => 'chat.'.$this->message->user_id,
]);
```

---

## Improvement Recommendations

### 1. Message Sanitization

```bash
composer require mews/purifier
```

```php
use Mews\Purifier\Facades\Purifier;

$cleanMessage = Purifier::clean($this->new_message, [
    'HTML.Allowed' => '', // Disallow all HTML
]);
```

### 2. Spam Profiling

```php
use Illuminate\Support\Facades\Cache;

public function sendMessage(): void
{
    $key = 'spam-check:'.auth()->id();
    $messages = Cache::get($key, []);
    
    // Check for duplicate messages
    if (in_array($this->new_message, $messages)) {
        $this->addError('new_message', 'Do not send duplicate messages');
        return;
    }
    
    // Save last 5 messages for 5 minutes
    $messages[] = $this->new_message;
    Cache::put($key, array_slice($messages, -5), now()->addMinutes(5));
    
    // ... send
}
```

### 3. User Blocking

**Migration:**
```php
Schema::table('chat_sessions', function (Blueprint $table) {
    $table->boolean('is_blocked')->default(false);
    $table->timestamp('blocked_at')->nullable();
    $table->text('block_reason')->nullable();
});
```

**Component check:**
```php
public function sendMessage(): void
{
    if ($this->chat_session->is_blocked) {
        $this->addError('new_message', 'You cannot send messages');
        return;
    }
    
    // ... send
}
```

### 4. Two-Factor Authorization for Admins

Require additional verification when sending messages from administrator.

### 5. Message Encryption

```php
use Illuminate\Support\Facades\Crypt;

// When saving
$message = Message::create([
    'message' => Crypt::encryptString($this->new_message),
    // ...
]);

// When reading
$decrypted = Crypt::decryptString($message->message);
```

**⚠️ Note:** Database encryption will complicate message search and indexing.

### 6. CSRF Protection

Laravel automatically protects against CSRF attacks for all POST requests.
Livewire components automatically include CSRF tokens.

### 7. SQL Injection Protection

Laravel Eloquent automatically protects against SQL Injection through parameterized queries.

**✅ Safe:**
```php
Message::where('user_id', $userId)->get();
```

**❌ Never use:**
```php
DB::select("SELECT * FROM messages WHERE user_id = $userId");
```

---

## Security Checklist

- [x] Private channel authorization
- [x] Input data validation
- [x] XSS protection via Blade
- [ ] Rate Limiting (recommended to add)
- [ ] Chat event logging
- [ ] HTML sanitization
- [ ] Spam protection
- [ ] User blocking capability
- [ ] Suspicious activity monitoring

---

## Contact and Support

If you discover security issues, contact the project administrator.
