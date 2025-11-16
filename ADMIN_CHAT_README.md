# Админ-панель для управления чатами

## Что реализовано

### ✅ Filament Admin Panel
- **Путь:** `/admin`
- **Стек:** Filament 3.2 + Laravel 11 + Livewire 3
- **Аутентификация:** Только для пользователей с `is_admin = true`

### ✅ Роль администратора
- **Миграция:** `2025_11_16_095140_add_is_admin_to_users_table.php`
- **Модель:** `User` реализует `FilamentUser` интерфейс
- **Поле:** `is_admin` (boolean, default: false)

### ✅ Управление чатами
**Resource:** `ChatSessionResource`

**Возможности:**
- 📋 Список всех чатов с фильтрацией
- 🔴 Badge с количеством непрочитанных чатов
- 👁️ Просмотр истории сообщений
- ✉️ Отправка ответов пользователям
- 🔒 Закрытие/открытие чатов
- 🔍 Поиск по имени пользователя
- 📊 Сортировка по дате последнего сообщения

**Таблица чатов:**
- Пользователь (имя + email)
- Последнее сообщение (дата и время)
- Непрочитанные (badge)
- Статус (открыт/закрыт)

**Фильтры:**
- По статусу (открыт/закрыт)
- С непрочитанными сообщениями

### ✅ WebSocket Broadcasting
**Канал админов:** `admin-chat`

**Авторизация:** `routes/channels.php`
```php
Broadcast::channel('admin-chat', function ($user) {
    return $user->is_admin;
});
```

**Flow сообщений:**
1. Пользователь отправляет сообщение
2. `MessageSent` event → WebSocket
3. Сообщение приходит в канал `chat.{userId}` (пользователю)
4. Сообщение приходит в канал `admin-chat` (всем админам)
5. Админ видит уведомление в Filament

**Когда админ отвечает:**
1. Админ отправляет сообщение с `is_from_admin = true`
2. `MessageSent` event → WebSocket
3. Сообщение приходит только в `chat.{userId}` (пользователю)
4. Пользователь видит ответ в реальном времени

---

## Структура файлов

```
app/
├── Events/
│   └── MessageSent.php                              # MODIFIED - broadcasting для админов
├── Filament/
│   └── Resources/
│       ├── ChatSessionResource.php                  # NEW - ресурс для чатов
│       └── ChatSessionResource/
│           └── Pages/
│               ├── ListChatSessions.php             # NEW - список чатов
│               └── ViewChatSession.php              # NEW - просмотр + ответ
├── Models/
│   └── User.php                                     # MODIFIED - FilamentUser интерфейс
└── Providers/
    └── Filament/
        └── AdminPanelProvider.php                   # NEW - Filament конфиг

database/
└── migrations/
    └── 2025_11_16_095140_add_is_admin_to_users_table.php  # NEW

resources/
└── views/
    └── filament/
        └── resources/
            └── chat-session/
                └── send-message-form.blade.php      # NEW - форма ответа

routes/
└── channels.php                                     # MODIFIED - admin-chat канал
```

---

## Запуск

### 1. Выполните миграцию

```bash
docker compose exec laravel.test php artisan migrate
```

### 2. Создайте админ-пользователя

**Вариант А: Через Tinker**
```bash
docker compose exec laravel.test php artisan tinker
```

```php
$user = User::find(1); // Или создайте нового
$user->is_admin = true;
$user->save();
```

**Вариант Б: SQL**
```sql
UPDATE users SET is_admin = 1 WHERE email = 'admin@example.com';
```

**Вариант В: Seeder**
Создайте `database/seeders/AdminSeeder.php`:

```php
<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
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

Запустите:
```bash
docker compose exec laravel.test php artisan db:seed --class=AdminSeeder
```

### 3. Очистите кеш

```bash
docker compose exec laravel.test php artisan optimize:clear
```

### 4. Откройте админ-панель

Перейдите на: **http://your-domain.com/admin**

Войдите с учетными данными администратора.

---

## Использование

### Список чатов

1. Откройте `/admin/chat-sessions`
2. Увидите все чаты с пользователями
3. Badge показывает количество непрочитанных чатов
4. Используйте фильтры для поиска

### Ответ на сообщение

1. Нажмите "Открыть" на нужном чате
2. Просмотрите историю сообщений
3. Введите ответ в форму внизу
4. Нажмите "Отправить"
5. Сообщение отправится через WebSocket пользователю

### Закрытие чата

1. Откройте чат
2. Нажмите "Закрыть чат" в верхнем правом углу
3. Чат закроется, форма ответа скроется
4. Можно переоткрыть кнопкой "Открыть чат"

---

## WebSocket в реальном времени

### Для работы real-time обновлений

**1. Запустите Laravel Reverb:**
```bash
docker compose exec laravel.test php artisan reverb:start
```

**2. Запустите Queue Worker:**
```bash
docker compose exec laravel.test php artisan queue:work
```

**3. Запустите Vite:**
```bash
npm run dev
```

### Как это работает:

**Когда пользователь пишет:**
1. Сообщение сохраняется в БД
2. Отправляется в канал `chat.{userId}` (пользователю)
3. Отправляется в канал `admin-chat` (всем админам)
4. Badge в меню админки обновляется автоматически
5. Список чатов обновляется

**Когда админ отвечает:**
1. Сообщение с `is_from_admin = true`
2. Отправляется только пользователю
3. Пользователь видит ответ мгновенно
4. Счетчик непрочитанных у пользователя увеличивается

---

## Безопасность

### Авторизация

✅ **Только администраторы** могут получить доступ к `/admin`
```php
public function canAccessPanel(Panel $panel): bool
{
    return $this->is_admin;
}
```

✅ **WebSocket канал защищен:**
```php
Broadcast::channel('admin-chat', function ($user) {
    return $user->is_admin; // Только админы
});
```

### Валидация

✅ Сообщения валидируются (max: 1000 символов)
✅ XSS защита через Blade (автоматическое экранирование)
✅ CSRF защита (Laravel + Livewire)

---

## Возможные улучшения

### Приоритетные:
- [ ] **Real-time обновления** страницы просмотра чата через Echo.js
- [ ] **Desktop notifications** для новых сообщений
- [ ] **Звуковое уведомление** при новом сообщении
- [ ] **Typing indicator** - "Пользователь печатает..."
- [ ] **Rate limiting** для защиты от спама

### Опциональные:
- [ ] Шаблоны быстрых ответов
- [ ] Статистика (среднее время ответа, количество обработанных чатов)
- [ ] Экспорт истории чата
- [ ] Поиск по содержимому сообщений
- [ ] Теги для категоризации чатов
- [ ] Назначение чатов конкретным админам
- [ ] История действий (кто и когда отвечал)

---

## FAQ

### Q: Как добавить нескольких администраторов?
**A:** Установите `is_admin = true` для нужных пользователей:
```php
User::whereIn('email', ['admin1@example.com', 'admin2@example.com'])
    ->update(['is_admin' => true]);
```

### Q: Как удалить пользователю права админа?
**A:**
```php
User::where('email', 'user@example.com')->update(['is_admin' => false]);
```

### Q: Получают ли все админы уведомления о новых сообщениях?
**A:** Да, все админы подключенные к WebSocket получают сообщения в канале `admin-chat`.

### Q: Можно ли настроить дизайн админки?
**A:** Да, в `app/Providers/Filament/AdminPanelProvider.php` можно изменить цвета, добавить темную тему и т.д.

### Q: Работает ли чат без WebSocket?
**A:** Да, сообщения сохраняются в БД. Но real-time обновления требуют WebSocket (Reverb).

---

## Troubleshooting

### Не могу войти в админку
- Проверьте `is_admin = true` в БД
- Очистите кеш: `php artisan optimize:clear`
- Проверьте что используете правильный email/password

### Не приходят real-time уведомления
- Запустите Reverb: `php artisan reverb:start`
- Запустите Queue Worker: `php artisan queue:work`
- Проверьте настройки `.env` (BROADCAST_CONNECTION=reverb)
- Откройте DevTools → Console - проверьте ошибки WebSocket

### Badge не обновляется
- Обновите страницу
- Проверьте что `unread_count` корректно обновляется в БД
- Очистите кеш

---

**Создано:** 2025-11-16  
**Версия:** 1.0.0  
**Стек:** Laravel 11 + Filament 3.2 + Livewire 3 + Laravel Reverb + Tailwind CSS
