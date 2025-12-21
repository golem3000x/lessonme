# ⚙️ Configuration

## Environment Variables

### Basic

```env
APP_NAME=LessonMe
APP_ENV=local|production
APP_DEBUG=true|false
APP_URL=http://localhost
```

### Database (PostgreSQL + pgvector)

```env
DB_CONNECTION=pgsql
DB_HOST=pgsql
DB_PORT=5432
DB_DATABASE=laravel
DB_USERNAME=sail
DB_PASSWORD=password
```

**Important:** pgvector extension required for similarity search:
```sql
CREATE EXTENSION IF NOT EXISTS vector;
```

### OpenAI API

```env
OPENAI_API_KEY=sk-...
OPENAI_ORGANIZATION=org-...  # опционально
```

**Models used:**
- `gpt-4o-mini` - lesson generation
- `text-embedding-3-small` - creating embeddings

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

### Queues

```env
QUEUE_CONNECTION=database
```

For production, Redis is recommended:
```env
QUEUE_CONNECTION=redis
REDIS_HOST=redis
REDIS_PORT=6379
```

### File Storage

```env
FILESYSTEM_DISK=local
```

Audio files are saved in:
```
storage/app/lessons/{uuid}/audio/vocabulary/
storage/app/lessons/{uuid}/audio/exercises/
```

### Sessions

```env
SESSION_DRIVER=database
SESSION_LIFETIME=120
```

---

## Configuration Files

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

## Application Constants

### Lesson Model

```php
// app/Models/Lesson/Lesson.php
public const float SIMILARITY_THRESHOLD = 0.9;  // 90% similarity for duplicates
```

### CEFR Level Constraints

Defined in `LessonGenerationService`:

| Level | Vocabulary | Grammar | Dialogue Lines | Sentence Length |
|-------|------------|---------|----------------|-----------------|
| A1 | 4 | 1 | 4 | 4-8 words |
| A2 | 6 | 1 | 6 | 6-12 words |
| B1 | 8 | 2 | 8 | 12-18 words |
| B2 | 10 | 3 | 12 | 15-25 words |
| C1 | 12 | 4 | 14 | 20-35 words |
| C2 | 13 | 5 | 16 | 25-40 words |

### Max Tokens by Level

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

Checks that the current user is the lesson owner.

**Registration:** `app/Http/Kernel.php`

```php
'lesson.owner' => \App\Http\Middleware\LessonOwnerMiddleware::class,
```

**Usage:**
```php
Route::get('/lessons/{lesson}/history', [LessonController::class, 'history'])
    ->middleware(['auth', 'lesson.owner']);
```

---

## Queues and Jobs

### Worker Setup

```bash
# Development
php artisan queue:work

# Production (supervisor)
php artisan queue:work --sleep=3 --tries=3 --max-time=3600
```

### Supervisor Configuration

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

### Start

```bash
./vendor/bin/sail up -d
```

### Available Services

| Service | Port | Description |
|---------|------|-------------|
| Laravel | 80 | Main application |
| PostgreSQL | 5432 | Database |
| Reverb | 8080 | WebSocket server |
| Mailpit | 8025 | Email testing |

### Running Commands

```bash
# Inside container
./vendor/bin/sail artisan migrate

# Or directly
docker exec ai-demo-laravel.test-1 php artisan migrate
```

---

## Logging

### Channels

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

### Usage

```php
use Illuminate\Support\Facades\Log;

// General log
Log::info('Message', ['context' => 'data']);

// Chat channel
Log::channel('chat')->info('Chat message sent', [
    'user_id' => $user->id,
    'message_id' => $message->id,
]);
```

---

## Caching

### Clear Cache

```bash
php artisan cache:clear      # Application cache
php artisan config:clear     # Config cache
php artisan route:clear      # Route cache
php artisan view:clear       # Compiled views
php artisan optimize:clear   # All caches
```

### Production Caching

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
```
