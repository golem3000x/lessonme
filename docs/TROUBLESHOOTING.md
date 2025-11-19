# Troubleshooting Guide

## ❌ Ошибка 419 при подключении к WebSocket

### Проблема:
```
POST http://127.0.0.1/broadcast/auth
Status: 419 (CSRF token mismatch)
```

### Причина:
Laravel Reverb требует CSRF токен для аутентификации broadcast соединений. Без токена в мета-тегах и axios конфигурации, все broadcast auth запросы будут возвращать 419 ошибку.

### Решение:

#### 1. Добавить CSRF токен в layout

**resources/views/components/guest-layout.blade.php:**
```blade
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}"> <!-- ✅ Обязательно! -->
    <title>{{ config('app.name') }}</title>
    
    @if(session('temp_guest_user_id'))
        <meta name="user-id" content="{{ session('temp_guest_user_id') }}">
    @elseif(auth()->check())
        <meta name="user-id" content="{{ auth()->id() }}">
    @endif
    <!-- ... -->
</head>
```

#### 2. Настроить axios для CSRF

**resources/js/bootstrap.js:**
```javascript
import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// ✅ Получить CSRF токен из meta тега
const token = document.head.querySelector('meta[name="csrf-token"]');

if (token) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
} else {
    console.error('CSRF token not found: https://laravel.com/docs/csrf#csrf-x-csrf-token');
}
```

#### 3. Пересобрать assets

```bash
docker compose exec laravel.test npm run build
# или для разработки
docker compose exec laravel.test npm run dev
```

#### 4. Очистить кэш

```bash
docker compose exec laravel.test php artisan optimize:clear
```

### Проверка:

1. Откройте DevTools → Network → XHR
2. Найдите запрос к `/broadcast/auth`
3. Проверьте Headers → Request Headers
4. Должен быть заголовок: `X-CSRF-TOKEN: ...`

**До исправления:**
```
Status: 419 CSRF token mismatch
```

**После исправления:**
```
Status: 200 OK
Response: {"auth": "..."}
```

---

## 🔍 Другие частые проблемы

### WebSocket не подключается

**Проверьте:**
1. Запущен ли Laravel Reverb: `docker compose ps`
2. Порт 8080 доступен
3. Переменные окружения в `.env`:
   ```env
   BROADCAST_DRIVER=reverb
   REVERB_APP_ID=...
   REVERB_APP_KEY=...
   REVERB_APP_SECRET=...
   REVERB_HOST=localhost
   REVERB_PORT=8080
   REVERB_SCHEME=http
   ```

### Queue Worker не обрабатывает задачи

**Проверьте:**
1. Запущен ли queue worker: `php artisan queue:work`
2. Подключение к БД корректное
3. Логи: `tail -f storage/logs/laravel.log`

### Уведомления не приходят

**Проверьте:**
1. CSRF токен настроен ✅
2. User ID в meta теге ✅
3. Echo.js слушает правильный канал ✅
4. Laravel Reverb запущен ✅
5. Queue Worker работает ✅

**Console должен показывать:**
```javascript
🔥 Lesson creation success: {...}
DemoLessonCreator received notification: {...}
```

---

## 📚 Полезные команды

### Проверка конфигурации
```bash
# Проверить broadcast драйвер
docker compose exec laravel.test php artisan config:show broadcasting

# Проверить очереди
docker compose exec laravel.test php artisan queue:list

# Проверить scheduler
docker compose exec laravel.test php artisan schedule:list
```

### Отладка
```bash
# Логи Laravel
docker compose exec laravel.test tail -f storage/logs/laravel.log

# Логи Reverb
docker compose logs -f reverb

# Логи Queue Worker
docker compose logs -f queue
```

### Assets
```bash
# Production build
docker compose exec laravel.test npm run build

# Development watch
docker compose exec laravel.test npm run dev

# Очистка
docker compose exec laravel.test npm run clean
```

### Кэш
```bash
# Очистить всё
docker compose exec laravel.test php artisan optimize:clear

# Только конфиг
docker compose exec laravel.test php artisan config:clear

# Только views
docker compose exec laravel.test php artisan view:clear

# Только routes
docker compose exec laravel.test php artisan route:clear
```
