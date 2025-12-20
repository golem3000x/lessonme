# Безопасность чата

## Оглавление
1. [Авторизация каналов](#авторизация-каналов)
2. [Валидация данных](#валидация-данных)
3. [XSS защита](#xss-защита)
4. [Rate Limiting](#rate-limiting)
5. [Мониторинг и логирование](#мониторинг-и-логирование)
6. [Рекомендации по улучшению](#рекомендации-по-улучшению)

---

## Авторизация каналов

### Текущая реализация

**Файл:** `routes/channels.php`

```php
Broadcast::channel('chat.{userId}', function ($user, $userId) {
    return (int) $user->id === (int) $userId;
});
```

**Как работает:**
- Каждый пользователь может подписаться только на свой собственный канал чата
- Laravel автоматически проверяет, что `$user->id` совпадает с `$userId` в имени канала
- Неавторизованные пользователи не могут подключиться к приватным каналам

**Защита:**
- ✅ Пользователь не может прослушивать чужие каналы
- ✅ Администратор может отправлять сообщения любому пользователю
- ✅ WebSocket соединения защищены Laravel Sanctum/Session

---

## Валидация данных

### Валидация отправки сообщений

**Файл:** `app/Livewire/ChatPanel.php`

```php
public function sendMessage(): void
{
    $this->validate([
        'new_message' => 'required|string|max:1000',
    ]);
    
    // ... создание сообщения
}
```

**Что проверяется:**
- ✅ Сообщение не пустое (`required`)
- ✅ Сообщение является строкой (`string`)
- ✅ Максимальная длина 1000 символов (`max:1000`)

### Рекомендации по улучшению валидации

**1. Добавить дополнительные правила:**

```php
$this->validate([
    'new_message' => [
        'required',
        'string',
        'max:1000',
        'min:1',
        // Запретить только пробелы
        'regex:/^(?!\s*$).+/',
    ],
]);
```

**2. Фильтрация нежелательного контента:**

```php
use Illuminate\Support\Str;

// Удаление лишних пробелов
$this->new_message = trim($this->new_message);
$this->new_message = preg_replace('/\s+/', ' ', $this->new_message);

// Фильтрация HTML-тегов (если не нужен HTML)
$this->new_message = strip_tags($this->new_message);
```

---

## XSS защита

### Автоматическая защита Blade

**Файл:** `resources/views/livewire/chat-panel.blade.php`

```blade
<p class="text-sm break-words">{{ $message->message }}</p>
```

**Как работает:**
- Blade синтаксис `{{ }}` автоматически экранирует HTML
- Все специальные символы (`<`, `>`, `&`, `"`, `'`) преобразуются в HTML entities
- Защита от внедрения JavaScript кода

**Пример:**
```
Ввод: <script>alert('XSS')</script>
Вывод: &lt;script&gt;alert('XSS')&lt;/script&gt;
```

### ❌ НЕ ИСПОЛЬЗОВАТЬ:

```blade
<!-- ОПАСНО! Никогда не используйте {!! !!} для пользовательского контента -->
<p>{!! $message->message !!}</p>
```

### Дополнительная защита

**Content Security Policy (CSP):**

Добавить в `.env`:
```env
CSP_ENABLED=true
```

Добавить middleware в `app/Http/Kernel.php`:
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

### Текущее состояние
⚠️ **Rate limiting не реализован**

### Рекомендации по реализации

**1. Throttle на уровне маршрутов (для API):**

```php
// routes/web.php
Route::middleware(['auth', 'throttle:60,1'])->group(function () {
    // 60 запросов в минуту
});
```

**2. Throttle в Livewire компоненте:**

```php
use Illuminate\Support\Facades\RateLimiter;

public function sendMessage(): void
{
    $key = 'send-message:'.$this->chat_session->id;
    
    if (RateLimiter::tooManyAttempts($key, 10)) {
        $seconds = RateLimiter::availableIn($key);
        
        $this->addError('new_message', 
            "Слишком много сообщений. Попробуйте через {$seconds} секунд."
        );
        
        return;
    }
    
    RateLimiter::hit($key, 60); // 10 сообщений в минуту
    
    // ... отправка сообщения
}
```

**3. Глобальный rate limiting на WebSocket:**

В `config/broadcasting.php`:
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

## Мониторинг и логирование

### Рекомендуемые события для логирования

**1. Логирование отправки сообщений:**

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

**2. Логирование подозрительной активности:**

```php
// Логировать множественные неудачные попытки
if ($this->validate(...) === false) {
    Log::warning('Invalid message attempt', [
        'user_id' => auth()->id(),
        'error' => $this->getErrorBag(),
        'ip' => request()->ip(),
    ]);
}
```

**3. Мониторинг WebSocket соединений:**

```php
// В Event MessageSent
Log::channel('chat')->info('Message broadcasted', [
    'message_id' => $this->message->id,
    'user_id' => $this->message->user_id,
    'channel' => 'chat.'.$this->message->user_id,
]);
```

---

## Рекомендации по улучшению

### 1. Санитизация сообщений

```bash
composer require mews/purifier
```

```php
use Mews\Purifier\Facades\Purifier;

$cleanMessage = Purifier::clean($this->new_message, [
    'HTML.Allowed' => '', // Запретить весь HTML
]);
```

### 2. Профилирование спама

```php
use Illuminate\Support\Facades\Cache;

public function sendMessage(): void
{
    $key = 'spam-check:'.auth()->id();
    $messages = Cache::get($key, []);
    
    // Проверка на дублирование сообщений
    if (in_array($this->new_message, $messages)) {
        $this->addError('new_message', 'Не отправляйте одинаковые сообщения');
        return;
    }
    
    // Сохранить последние 5 сообщений на 5 минут
    $messages[] = $this->new_message;
    Cache::put($key, array_slice($messages, -5), now()->addMinutes(5));
    
    // ... отправка
}
```

### 3. Блокировка пользователей

**Миграция:**
```php
Schema::table('chat_sessions', function (Blueprint $table) {
    $table->boolean('is_blocked')->default(false);
    $table->timestamp('blocked_at')->nullable();
    $table->text('block_reason')->nullable();
});
```

**Проверка в компоненте:**
```php
public function sendMessage(): void
{
    if ($this->chat_session->is_blocked) {
        $this->addError('new_message', 'Вы не можете отправлять сообщения');
        return;
    }
    
    // ... отправка
}
```

### 4. Двухфакторная авторизация для админов

При отправке сообщений от администратора требовать дополнительную проверку.

### 5. Шифрование сообщений

```php
use Illuminate\Support\Facades\Crypt;

// При сохранении
$message = Message::create([
    'message' => Crypt::encryptString($this->new_message),
    // ...
]);

// При чтении
$decrypted = Crypt::decryptString($message->message);
```

**⚠️ Внимание:** Шифрование в БД усложнит поиск и индексацию сообщений.

### 6. CSRF защита

Laravel автоматически защищает от CSRF атак для всех POST запросов.
Livewire компоненты автоматически включают CSRF токены.

### 7. SQL Injection защита

Laravel Eloquent автоматически защищает от SQL Injection через параметризованные запросы.

**✅ Безопасно:**
```php
Message::where('user_id', $userId)->get();
```

**❌ Никогда не используйте:**
```php
DB::select("SELECT * FROM messages WHERE user_id = $userId");
```

---

## Чеклист безопасности

- [x] Авторизация приватных каналов
- [x] Валидация входных данных
- [x] XSS защита через Blade
- [ ] Rate Limiting (рекомендуется добавить)
- [ ] Логирование событий чата
- [ ] Санитизация HTML
- [ ] Защита от спама
- [ ] Возможность блокировки пользователей
- [ ] Мониторинг подозрительной активности

---

## Контакты и поддержка

При обнаружении проблем безопасности обращайтесь к администратору проекта.
