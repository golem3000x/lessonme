# 📚 Документация проекта LessonMe

Платформа для изучения языков с AI-генерацией уроков.

## Структура документации

### 🏗️ Архитектура
- [ARCHITECTURE.md](./ARCHITECTURE.md) - Общая архитектура приложения
- [DATABASE.md](./DATABASE.md) - Схема базы данных (ER-диаграмма, таблицы)
- [API_SERVICES.md](./API_SERVICES.md) - API сервисов и DTOs
- [CONFIGURATION.md](./CONFIGURATION.md) - Конфигурация и переменные окружения

### 📖 Функциональность
- [GRADING_SYSTEM_IMPLEMENTATION.md](./GRADING_SYSTEM_IMPLEMENTATION.md) - Система оценивания и отслеживания прогресса
- [STUDENT_DASHBOARD_SUMMARY.md](./STUDENT_DASHBOARD_SUMMARY.md) - Дашборд студента
- [DEMO_GUEST_SYSTEM.md](./DEMO_GUEST_SYSTEM.md) - Демо-система для гостей
- [LESSON_UPDATE_SUMMARY.md](./LESSON_UPDATE_SUMMARY.md) - Обновления уроков
- [LANGUAGE_SELECTION_FEATURE.md](./LANGUAGE_SELECTION_FEATURE.md) - Выбор языка
- [PASSWORD_RESET_SETUP.md](./PASSWORD_RESET_SETUP.md) - Сброс пароля

### 💬 Чат
- [CHAT_README.md](./CHAT_README.md) - Чат пользователей
- [ADMIN_CHAT_README.md](./ADMIN_CHAT_README.md) - Админ чат

### 🔒 Безопасность
- [CHAT_SECURITY.md](./CHAT_SECURITY.md) - Безопасность чата и WebSocket

### 🔧 Техническое
- [WEBSOCKET_MIGRATION.md](./WEBSOCKET_MIGRATION.md) - Миграция на WebSocket (Reverb)
- [AUDIO_FILES_DELETION.md](./AUDIO_FILES_DELETION.md) - Управление аудио файлами
- [TROUBLESHOOTING.md](./TROUBLESHOOTING.md) - Решение проблем

### 🧪 Тестирование
- [TESTING_DEMO_GUESTS.md](./TESTING_DEMO_GUESTS.md) - Тестирование демо-гостей

### 🎨 UI Компоненты
- [MODAL_COMPONENTS.md](./MODAL_COMPONENTS.md) - Модальные окна
- [SUPPORT_PAGE_QR_CODE.md](./SUPPORT_PAGE_QR_CODE.md) - QR-код на странице поддержки

### 📋 Чеклисты
- [CHECKLIST.md](./CHECKLIST.md) - Чеклист обновления структуры урока

---

## Быстрый старт

### Требования
- PHP 8.1+
- Laravel 11
- PostgreSQL с pgvector
- Node.js / NPM
- Docker (опционально)

### Установка
```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate
npm run build
```

### Запуск в Docker
```bash
./vendor/bin/sail up -d
```

---

## Основные модули

| Модуль | Описание | Путь |
|--------|----------|------|
| **Lessons** | Генерация и управление уроками | `app/Services/Lesson/` |
| **Courses** | Курсы и назначения | `app/Models/Course/` |
| **Grading** | Оценивание и прогресс | `app/Services/Lesson/LessonGradingService.php` |
| **Audio** | TTS аудио генерация | `app/Services/Lesson/LessonAudioService.php` |
| **Chat** | WebSocket чат | `app/Livewire/ChatPanel.php` |

---

## Контакты

При обнаружении проблем создайте issue или обратитесь к администратору.
