# Demo Guest User System

## Проблема

В демо-режиме все гости использовали одного общего пользователя (`guest@system.local`), что приводило к:
- Конфликтам при одновременном создании уроков несколькими гостями
- Возможности видеть уроки других гостей
- Засорению базы данных уроками всех гостей

## Решение

Каждой гостевой сессии создается **временный пользователь**:

### Характеристики временных пользователей:

- **Уникальный email**: `demo_{unique_id}@temp.local`
- **Флаг**: `is_temp_guest = true`
- **Срок жизни**: 24 часа (`temp_guest_expires_at`)
- **Хранение в сессии**: `temp_guest_user_id`

### Автоматическая очистка

Система автоматически удаляет истекших временных гостей и их уроки:

```bash
# Ручной запуск
php artisan guests:cleanup

# Принудительная очистка без подтверждения
php artisan guests:cleanup --force
```

**Автоматический запуск**: Каждый день в 03:00 (настроено в `routes/console.php`)

### Поток работы:

1. **Гость заходит на страницу** → Создается временный user (БЕЗ логина!)
2. **User ID сохраняется в session** → Используется для WebSocket авторизации
3. **Временный user подключается к WebSocket** → Приватный канал для уведомлений
4. **Гость создает урок** → Урок привязывается к временному user
5. **Урок готов** → Уведомление приходит через WebSocket (не polling!)
6. **Гость работает с уроком** → Все данные изолированы от других гостей
7. **Через 24 часа** → Автоматическая очистка удаляет пользователя и его уроки

### 🔒 Безопасность

Временные гости управляются через middleware `EnsureTempGuestUser`:

- ✅ Автоматическое создание временного пользователя
- ✅ Ограниченный доступ только к демо-страницам
- ✅ Флаг `is_temp_guest = true` для идентификации
- ✅ Автоматическое истечение через `temp_guest_expires_at`
- ✅ Нет доступа к личному кабинету, API, admin панели

**Авторизация каналов:**

```php
// routes/channels.php
Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});
```

### Уведомления через WebSocket:

Используются **real-time WebSocket уведомления** с fallback на polling:

- ✅ **Мгновенные обновления** - урок появляется сразу после создания
- ✅ **Laravel Reverb** - встроенный WebSocket сервер
- ✅ **Echo.js** - автоматическое подключение и обработка событий
- ✅ **Fallback polling** - `checkLessonStatus()` с таймаутом 3 минуты

**Технические детали:**
- User ID хранится в session (`temp_guest_user_id`)
- Подключается к приватному каналу: `App.Models.User.{user_id}`
- Слушает события: `lesson.creation.successfully`, `lesson.creation.error`, `lesson.creation.exists`
- Livewire компонент обрабатывает событие `lesson-notification`

### Компоненты:

#### 1. Migration
```
database/migrations/2025_11_19_173600_add_temp_guest_fields_to_users_table.php
```
Добавляет поля `is_temp_guest` и `temp_guest_expires_at`

#### 2. Command
```
app/Console/Commands/CleanupTempGuestUsers.php
```
Команда для очистки истекших временных гостей

#### 3. Scheduler
```
routes/console.php
```
Автоматический запуск очистки каждый день

#### 4. Component Logic
```
app/Livewire/Welcome/DemoLessonCreator.php
```
Метод `ensureTempGuestUser()` создает и управляет временными пользователями

### Преимущества:

✅ Полная изоляция между гостевыми сессиями  
✅ Нет конфликтов при одновременной работе  
✅ Автоматическая очистка старых данных  
✅ Не требует аутентификации  
✅ Foreign key constraints работают корректно  

### Настройка срока жизни:

По умолчанию: **24 часа**

Изменить можно в `DemoLessonCreator.php`:
```php
'temp_guest_expires_at' => now()->addHours(24), // <- здесь
```

### Мониторинг:

Количество активных временных гостей:
```sql
SELECT COUNT(*) FROM users 
WHERE is_temp_guest = true 
AND temp_guest_expires_at > NOW();
```

Количество истекших (готовых к удалению):
```sql
SELECT COUNT(*) FROM users 
WHERE is_temp_guest = true 
AND temp_guest_expires_at < NOW();
```
