# 🎓 LessonMe

Платформа для изучения языков с AI-генерацией уроков.

## ✨ Возможности

- **AI-генерация уроков** — GPT-4o-mini создаёт персонализированные уроки по уровню CEFR (A1-C2)
- **Интерактивные упражнения** — gap-fill, matching, true/false, sentence ordering
- **TTS аудио** — озвучка словаря и упражнений через FPT.AI
- **Система оценивания** — автоматическая проверка с отслеживанием прогресса
- **Курсы** — объединение уроков в курсы с назначением студентам
- **Учитель-ученик** — система приглашений и совместной работы
- **Real-time уведомления** — WebSocket через Laravel Reverb
- **Демо-режим** — временные гости могут попробовать без регистрации

## 🛠 Технологии

| Категория | Технология |
|-----------|------------|
| Backend | Laravel 11, PHP 8.1+ |
| Frontend | Livewire 3.5+, Alpine.js, TailwindCSS, daisyUI |
| Database | PostgreSQL 17 + pgvector |
| AI | OpenAI GPT-4o-mini, text-embedding-3-small |
| TTS | FPT.AI Text-to-Speech |
| WebSocket | Laravel Reverb |
| Queue | Laravel Queue (database driver) |

## 🚀 Установка

### Требования
- PHP 8.1+
- Composer
- Node.js / NPM
- PostgreSQL с расширением pgvector
- Docker (рекомендуется)

### Docker (Laravel Sail)

```bash
# Клонировать репозиторий
git clone <repo-url>
cd ai-demo

# Установить зависимости
composer install
npm install

# Настроить окружение
cp .env.example .env

# Запустить контейнеры
./vendor/bin/sail up -d

# Миграции и сиды
./vendor/bin/sail artisan migrate --seed

# Собрать assets
./vendor/bin/sail npm run build
```

### Переменные окружения

```env
# OpenAI
OPENAI_API_KEY=sk-...

# FPT.AI TTS
FPT_TTS_API_KEY=...

# WebSocket
BROADCAST_CONNECTION=reverb
REVERB_APP_ID=...
REVERB_APP_KEY=...
REVERB_APP_SECRET=...
```

## 📚 Документация

Подробная документация в папке [`docs/`](./docs/README.md):

- [Архитектура](./docs/ARCHITECTURE.md)
- [Схема БД](./docs/DATABASE.md)
- [API сервисов](./docs/API_SERVICES.md)
- [Конфигурация](./docs/CONFIGURATION.md)

## 🔧 Команды

```bash
# Запуск сервисов
./vendor/bin/sail up -d

# WebSocket сервер
./vendor/bin/sail artisan reverb:start

# Queue worker
./vendor/bin/sail artisan queue:work

# Очистка временных гостей
./vendor/bin/sail artisan app:cleanup-temp-guests

# Тесты
./vendor/bin/sail artisan test
```

## 📁 Структура проекта

```
app/
├── Services/Lesson/     # Сервисы генерации и оценивания уроков
├── Models/              # Eloquent модели
├── Livewire/            # Livewire компоненты
├── Jobs/                # Фоновые задачи
└── Notifications/       # WebSocket уведомления

docs/                    # Документация
resources/views/         # Blade шаблоны
```

## 📄 Лицензия

Проприетарное ПО. Все права защищены.
