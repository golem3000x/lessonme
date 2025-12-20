# ⚙️ Конфигурация

## Переменные окружения

### Основные

```env
APP_NAME=LessonMe
APP_ENV=local|production
APP_DEBUG=true|false
APP_URL=http://localhost
```

### База данных (PostgreSQL + pgvector)

```env
DB_CONNECTION=pgsql
DB_HOST=pgsql
DB_PORT=5432
DB_DATABASE=laravel
DB_USERNAME=sail
DB_PASSWORD=password
```

**Важно:** Для работы similarity search требуется расширение pgvector:
```sql
CREATE EXTENSION IF NOT EXISTS vector;
```

### OpenAI API

```env
OPENAI_API_KEY=sk-...
OPENAI_ORGANIZATION=org-...  # опционально
```

**Используемые модели:**
- `gpt-4o-mini` - генерация уроков
- `text-embedding-3-small` - создание embeddings

### FPT.AI TTS

```env
FPT_TTS_API_KEY=...
```

**Endpoint:** `https://api.fpt.ai/hmi/tts/v5`

### WebSocket (Laravel Reverb)

```env
BROADCAST_CONNECTION=reverb

REVERB_APP_ID=...
REVERB_APP_KEY=...
REVERB_APP_SECRET=...
REVERB_HOST=localhost
REVERB_PORT=8080
REVERB_SCHEME=http

VITE_REVERB_APP_KEY="${REVERB_APP_KEY}"
VITE_REVERB_HOST="${REVERB_HOST}"
VITE_REVERB_PORT="${REVERB_PORT}"
VITE_REVERB_SCHEME="${REVERB_SCHEME}"
```

### Очереди

```env
QUEUE_CONNECTION=database
```

Для production рекомендуется Redis:
```env
QUEUE_CONNECTION=redis
REDIS_HOST=redis
REDIS_PORT=6379
```

### Файловое хранилище

```env
FILESYSTEM_DISK=local
```

Аудио файлы сохраняются в:
```
storage/app/lessons/{uuid}/audio/vocabulary/
storage/app/lessons/{uuid}/audio/exercises/
```

### Сессии

```env
SESSION_DRIVER=database
SESSION_LIFETIME=120
```

---

## Конфигурационные файлы

### config/openai.php

```php
return [
    'api_key' => env('OPENAI_API_KEY'),
    'organization' => env('OPENAI_ORGANIZATION'),
];
```

### config/broadcasting.php

```php
'reverb' => [
    'driver' => 'reverb',
    'key' => env('REVERB_APP_KEY'),
    'secret' => env('REVERB_APP_SECRET'),
    'app_id' => env('REVERB_APP_ID'),
    'options' => [
        'host' => env('REVERB_HOST'),
        'port' => env('REVERB_PORT', 443),
        'scheme' => env('REVERB_SCHEME', 'https'),
        'useTLS' => env('REVERB_SCHEME', 'https') === 'https',
    ],
    'client_options' => [],
],
```

---

## Константы приложения

### Lesson Model

```php
// app/Models/Lesson/Lesson.php
public const float SIMILARITY_THRESHOLD = 0.9;  // 90% схожесть для дубликатов
```

### CEFR Level Constraints

Определены в `LessonGenerationService`:

| Level | Vocabulary | Grammar | Dialogue Lines | Sentence Length |
|-------|------------|---------|----------------|-----------------|
| A1 | 4 | 1 | 4 | 4-8 words |
| A2 | 6 | 1 | 6 | 6-12 words |
| B1 | 8 | 2 | 8 | 12-18 words |
| B2 | 10 | 3 | 12 | 15-25 words |
| C1 | 12 | 4 | 14 | 20-35 words |
| C2 | 13 | 5 | 16 | 25-40 words |

### Max Tokens по уровням

```php
A1 => 1800
A2 => 2000
B1 => 2500
B2 => 3000
C1 => 3500
C2 => 4000
```

---

## Middleware

### lesson.owner

Проверяет, что текущий пользователь является владельцем урока.

**Регистрация:** `app/Http/Kernel.php`

```php
'lesson.owner' => \App\Http\Middleware\LessonOwnerMiddleware::class,
```

**Использование:**
```php
Route::get('/lessons/{lesson}/history', [LessonController::class, 'history'])
    ->middleware(['auth', 'lesson.owner']);
```

---

## Очереди и Jobs

### Настройка worker

```bash
# Development
php artisan queue:work

# Production (supervisor)
php artisan queue:work --sleep=3 --tries=3 --max-time=3600
```

### Supervisor конфигурация

```ini
[program:laravel-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/html/artisan queue:work --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
numprocs=2
redirect_stderr=true
stdout_logfile=/var/www/html/storage/logs/worker.log
stopwaitsecs=3600
```

---

## Docker (Sail)

### Запуск

```bash
./vendor/bin/sail up -d
```

### Доступные сервисы

| Сервис | Порт | Описание |
|--------|------|----------|
| Laravel | 80 | Основное приложение |
| PostgreSQL | 5432 | База данных |
| Reverb | 8080 | WebSocket сервер |
| Mailpit | 8025 | Email тестирование |

### Выполнение команд

```bash
# Внутри контейнера
./vendor/bin/sail artisan migrate

# Или напрямую
docker exec ai-demo-laravel.test-1 php artisan migrate
```

---

## Логирование

### Каналы

```php
// config/logging.php
'channels' => [
    'stack' => [
        'driver' => 'stack',
        'channels' => ['single', 'daily'],
    ],
    'chat' => [
        'driver' => 'daily',
        'path' => storage_path('logs/chat.log'),
        'level' => 'info',
        'days' => 14,
    ],
],
```

### Использование

```php
use Illuminate\Support\Facades\Log;

// Общий лог
Log::info('Message', ['context' => 'data']);

// Канал чата
Log::channel('chat')->info('Chat message sent', [
    'user_id' => $user->id,
    'message_id' => $message->id,
]);
```

---

## Кэширование

### Очистка кэша

```bash
php artisan cache:clear      # Application cache
php artisan config:clear     # Config cache
php artisan route:clear      # Route cache
php artisan view:clear       # Compiled views
php artisan optimize:clear   # All caches
```

### Production кэширование

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
```
