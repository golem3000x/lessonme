# Migration от Polling к WebSocket для Demo Guests

## Что было изменено

### ❌ Старый подход (Polling):

```blade
<!-- Опрос каждые 5 секунд -->
<div wire:poll.5s="checkLessonStatus">
```

**Проблемы:**
- Постоянные HTTP запросы каждые 5 секунд
- Задержка до 5 секунд перед отображением урока
- Нагрузка на сервер
- Неэффективное использование ресурсов

### ✅ Новый подход (WebSocket):

**Технологии:**
- Laravel Reverb (WebSocket сервер)
- Echo.js (клиент WebSocket)
- Livewire Events
- Laravel Notifications + Broadcasting

**Преимущества:**
- ⚡ Мгновенные уведомления в реальном времени
- 📉 Снижение нагрузки на сервер (нет polling)
- 🔔 Использование существующей системы уведомлений
- 🎯 Изоляция между разными гостевыми сессиями

## Ключевые изменения

### 1. Авторизация каналов

**routes/channels.php:**

```php
Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});
```

**ВАЖНО:** Для работы WebSocket уведомлений с временными гостями используется middleware `EnsureTempGuestUser`, который автоматически логинит временного пользователя для доступа к приватным каналам.

**Защита:**
- ✅ Временные гости имеют ограниченный доступ (только к демо-страницам)
- ✅ Автоматическая очистка истекших гостей
- ✅ Проверка `is_temp_guest` и `temp_guest_expires_at`

### 2. Мета-теги для Echo.js

**resources/views/components/guest-layout.blade.php:**
```blade
<meta name="csrf-token" content="{{ csrf_token() }}">

@if(session('temp_guest_user_id'))
    <meta name="user-id" content="{{ session('temp_guest_user_id') }}">
@elseif(auth()->check())
    <meta name="user-id" content="{{ auth()->id() }}">
@endif
```

**Зачем:** 
- `csrf-token` - **обязателен** для broadcast аутентификации (иначе 419 ошибка)
- `user-id` - Echo.js использует для подключения к приватному каналу
- Приоритет session для temp guests, затем auth для обычных пользователей

### 3. Axios конфигурация для CSRF

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

**Зачем:** Axios отправляет CSRF токен при broadcast auth запросах

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

**Зачем:** Обрабатывает уведомления от Echo.js.

### 5. Fallback polling

Для надёжности сохранён метод `checkLessonStatus()` как fallback на случай проблем с WebSocket:
- Таймаут: 3 минуты
- Проверяет наличие урока в БД если WebSocket не доставил уведомление

## Поток работы

```
1. Гость открывает /welcome
   ↓
2. DemoLessonCreator::mount() создает temp user (БЕЗ логина!)
   ↓
3. temp_guest_user_id сохраняется в session
   ↓
4. Echo.js подключается к App.Models.User.{user_id} (из session)
   ↓
5. Гость создает урок
   ↓
6. CreateLessonJob выполняется
   ↓
7. Job отправляет LessonCreationNotification
   ↓
8. Laravel Reverb транслирует в WebSocket
   ↓
9. Echo.js получает событие .lesson.creation.successfully
   ↓
10. Echo.js вызывает Livewire.dispatch('lesson-notification')
    ↓
11. DemoLessonCreator::handleLessonNotification() обрабатывает
    ↓
12. UI обновляется мгновенно ⚡
```

## 🔒 Безопасность

### Middleware EnsureTempGuestUser

Временные гости управляются через middleware `EnsureTempGuestUser`:

```php
// app/Http/Middleware/EnsureTempGuestUser.php
// Автоматически создаёт и авторизует временного пользователя
```

**Защита:**
- ✅ Временные гости имеют флаг `is_temp_guest = true`
- ✅ Срок действия через `temp_guest_expires_at`
- ✅ Ограниченный доступ только к демо-страницам
- ✅ Автоматическая очистка истекших гостей

## Уже существующая инфраструктура

Вся инфраструктура для WebSocket **уже была настроена**:

### ✅ Backend:
- `AbstractLessonNotification` - базовый класс уведомлений
- `LessonCreationNotification` - уведомление создания урока
- `CreateLessonJob` - отправляет уведомления
- `routes/channels.php` - авторизация broadcast каналов

### ✅ Frontend:
- `resources/js/echo.js` - уже слушает события `lesson.creation.*`
- `Echo.js` настроен и работает
- Laravel Reverb запущен

## Что НЕ изменилось

- ✅ Система временных пользователей работает как прежде
- ✅ Очистка expired guests работает
- ✅ Изоляция между сессиями сохранена
- ✅ Все существующие уведомления продолжают работать

## Тестирование

### 1. Проверить WebSocket подключение

Откройте DevTools → Network → WS:
- Должно быть активное WebSocket подключение
- URL: `ws://localhost:8080/app/{APP_KEY}`

### 2. Проверить уведомления

Создайте урок и проверьте Console:
```
🔥 Lesson creation success: {...}
DemoLessonCreator received notification: {...}
```

### 3. Проверить отсутствие polling

Network → XHR:
- НЕ должно быть повторяющихся запросов каждые 5 секунд

## Требования

### Сервисы должны быть запущены:

```bash
# Laravel Reverb (WebSocket сервер)
docker compose exec laravel.test php artisan reverb:start

# Queue Worker (обработка уведомлений)
docker compose exec laravel.test php artisan queue:work
```

### Конфигурация (.env):

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

### Уведомления не приходят?

1. Проверьте что Reverb запущен: `docker compose ps`
2. Проверьте что Queue Worker работает
3. Проверьте мета-тег `user-id` в HTML
4. Проверьте Console на ошибки WebSocket
5. Проверьте что пользователь залогинен: `auth()->check()`

### WebSocket не подключается?

1. Проверьте `REVERB_*` переменные в `.env`
2. Проверьте что порт 8080 доступен
3. Очистите кэш: `php artisan optimize:clear`

## Преимущества миграции

| Метрика | Polling | WebSocket |
|---------|---------|-----------|
| Задержка | До 5 сек | ~100ms |
| Нагрузка | Постоянные запросы | Одно соединение |
| Масштабируемость | Плохая | Отличная |
| UX | Задержки | Мгновенно |
| Ресурсы сервера | Высокие | Низкие |

## Заключение

Миграция на WebSocket:
- ✅ Использует существующую инфраструктуру
- ✅ Минимальные изменения кода
- ✅ Значительно улучшает UX
- ✅ Снижает нагрузку на сервер
- ✅ Сохраняет все преимущества системы временных гостей
