# Функционал чата - Инструкция

## Что реализовано

### 1. ✅ Страница "О проекте"
- **Путь:** `/about`
- **Файлы:**
  - `routes/web.php` - роут
  - `resources/views/about.blade.php` - страница
  - `resources/lang/*/nav.php` - локализация
- **Навигация:** Добавлена ссылка в главное меню

### 2. ✅ Кнопка чата в правом нижнем углу
- **Компонент:** `app/Livewire/ChatButton.php`
- **View:** `resources/views/livewire/chat-button.blade.php`
- **Функции:**
  - Красивая круглая кнопка с градиентом
  - Пульсирующая анимация для привлечения внимания
  - Badge с количеством непрочитанных сообщений
  - Подсказка при наведении
  - Автоматическое появление только для авторизованных пользователей

### 3. ✅ База данных
- **Миграции:**
  - `create_chat_sessions_table.php` - сессии чата
  - `create_messages_table.php` - сообщения
- **Модели:**
  - `app/Models/ChatSession.php`
  - `app/Models/Message.php`
- **Enum:** Добавлены `CHAT_SESSIONS` и `MESSAGES` в `TableNameEnum`

### 4. ✅ WebSocket Broadcasting
- **Event:** `app/Events/MessageSent.php`
- **Channel:** `routes/channels.php` - авторизация канала `chat.{userId}`
- **Laravel Reverb:** Использует существующую настройку

### 5. ✅ Livewire компонент чата
- **Компонент:** `app/Livewire/ChatPanel.php`
- **View:** `resources/views/livewire/chat-panel.blade.php`
- **Функции:**
  - Отображение истории сообщений (последние 50)
  - Отправка сообщений в реальном времени
  - Автопрокрутка к последнему сообщению
  - Отметка сообщений как прочитанных
  - Валидация сообщений (макс. 1000 символов)
  - Красивый UI с анимациями

### 6. ✅ Echo.js интеграция
- **Файл:** `resources/js/echo.js`
- **Функции:**
  - Подписка на приватный канал `chat.{userId}`
  - Прослушивание события `.message.sent`
  - Dispatch Livewire события для обновления UI

---

## Как работает

### Flow отправки сообщения:

1. **Пользователь вводит сообщение** в `ChatPanel`
2. **Livewire отправляет** через `wire:submit="sendMessage"`
3. **Валидация** в `ChatPanel::sendMessage()`
4. **Сохранение в БД** через модель `Message`
5. **Broadcasting** через `MessageSent` event
6. **Laravel Reverb** отправляет в WebSocket
7. **Echo.js получает** событие `.message.sent`
8. **Livewire обновляет UI** через `message-received` dispatch
9. **Автопрокрутка** через Alpine.js

### Архитектура:

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

## Структура файлов

```
app/
├── Enums/
│   └── TableNameEnum.php          # +CHAT_SESSIONS, +MESSAGES
├── Events/
│   └── MessageSent.php            # Broadcasting event
├── Livewire/
│   ├── ChatButton.php             # Кнопка чата
│   └── ChatPanel.php              # Панель чата
├── Models/
│   ├── ChatSession.php            # Модель сессии
│   ├── Message.php                # Модель сообщения
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
    ├── about.blade.php            # Страница "О проекте"
    ├── components/
    │   └── app-layout.blade.php   # +chat button, +chat panel
    └── livewire/
        ├── chat-button.blade.php  # UI кнопки
        └── chat-panel.blade.php   # UI панели чата

routes/
├── channels.php                   # +chat.{userId} authorization
└── web.php                        # +/about route

CHAT_SECURITY.md                   # Документация по безопасности
CHAT_README.md                     # Эта инструкция
```

---

## Запуск и тестирование

### 1. Убедитесь что Laravel Reverb работает

```bash
docker compose exec laravel.test php artisan reverb:start
```

Или проверьте что он запущен в фоне.

### 2. Убедитесь что Queue Worker работает

```bash
docker compose exec laravel.test php artisan queue:work
```

Broadcasting работает через очереди.

### 3. Скомпилируйте фронтенд

```bash
npm run dev
# или
npm run build
```

### 4. Откройте приложение

1. Авторизуйтесь в системе
2. В правом нижнем углу появится кнопка чата с пульсацией
3. Нажмите на кнопку - откроется панель чата
4. Введите сообщение и отправьте

### 5. Тестирование WebSocket

**В консоли браузера должны появиться:**
```
🔥 Chat message received: {id: 1, message: "...", ...}
```

**Если сообщения не приходят:**
- Проверьте что Reverb запущен
- Проверьте `.env` настройки Reverb
- Откройте DevTools → Network → WS и проверьте WebSocket соединение
- Проверьте консоль браузера на ошибки

---

## Настройки .env

Убедитесь что у вас есть эти настройки:

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

## Будущие улучшения

### Приоритетные:
- [ ] **Rate Limiting** - ограничение количества сообщений
- [ ] **Логирование** - сохранение истории отправки сообщений
- [ ] **Админ панель** - просмотр всех чатов администратором
- [ ] **Typing indicator** - индикатор "печатает..."

### Опциональные:
- [ ] Отправка файлов/изображений
- [ ] Эмодзи пикер
- [ ] Markdown поддержка
- [ ] Звуковые уведомления
- [ ] Desktop notifications
- [ ] История чата с пагинацией
- [ ] Поиск по сообщениям
- [ ] Архивация старых чатов
- [ ] Экспорт истории чата

---

## Безопасность

Подробная документация по безопасности в файле `CHAT_SECURITY.md`

**Ключевые моменты:**
- ✅ Авторизация приватных каналов
- ✅ Валидация входных данных
- ✅ XSS защита через Blade
- ⚠️ Rate Limiting - рекомендуется добавить
- ⚠️ Логирование - рекомендуется добавить

---

## FAQ

### Q: Как администратор может отвечать пользователям?
**A:** Сейчас это не реализовано. Нужно создать админ-панель с компонентом для просмотра всех чатов и отправки сообщений с флагом `is_from_admin = true`.

### Q: Как удалить сообщение?
**A:** Функция удаления не реализована. Можно добавить метод в `ChatPanel`:
```php
public function deleteMessage($messageId)
{
    Message::where('id', $messageId)
        ->where('user_id', auth()->id())
        ->delete();
}
```

### Q: Можно ли отключить чат для конкретного пользователя?
**A:** Да, добавьте поле `is_blocked` в таблицу `chat_sessions` и проверяйте его перед отправкой.

### Q: Сколько сообщений загружается при открытии чата?
**A:** Последние 50 сообщений. Можно изменить в `ChatPanel::loadChatSession()`:
```php
->limit(50) // Измените на нужное количество
```

### Q: Работает ли чат в режиме оффлайн?
**A:** Нет, требуется активное WebSocket соединение. Сообщения, отправленные оффлайн, будут потеряны.

---

## Поддержка

При возникновении проблем:
1. Проверьте логи Laravel: `storage/logs/laravel.log`
2. Проверьте консоль браузера на JavaScript ошибки
3. Проверьте что Reverb запущен
4. Проверьте что Queue Worker запущен
5. Проверьте права доступа к файлам

---

**Создано:** 2025-11-16  
**Версия:** 1.0.0  
**Стек:** Laravel 11, Livewire 3.5+, Laravel Reverb, Alpine.js, TailwindCSS
