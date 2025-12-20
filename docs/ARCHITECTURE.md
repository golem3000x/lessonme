# 🏗️ Архитектура приложения LessonMe

## Обзор

LessonMe - платформа для изучения языков с AI-генерацией контента. Построена на Laravel 11 с Livewire 3.5+, использует OpenAI API для генерации уроков.

---

## Технологический стек

| Компонент | Технология |
|-----------|------------|
| Backend | Laravel 11, PHP 8.1+ |
| Frontend | Livewire 3.5+, Alpine.js, TailwindCSS, DaisyUI |
| Database | PostgreSQL + pgvector |
| Real-time | Laravel Reverb (WebSocket) |
| AI | OpenAI GPT-4o-mini, Embeddings |
| TTS | FPT.AI Text-to-Speech |
| Queue | Laravel Queue (database driver) |

---

## Структура директорий

```
app/
├── Dto/                    # Data Transfer Objects
│   └── LessonDto.php       # DTO для передачи данных урока
├── Enums/                  # Перечисления
│   ├── Lesson/             # Enums для уроков
│   ├── Notifications/      # Enums для уведомлений
│   └── System/             # Системные enums (языки, уровни, таблицы)
├── Events/                 # События (Broadcasting)
├── Exceptions/             # Кастомные исключения
├── Http/
│   ├── Controllers/        # Контроллеры
│   ├── Middleware/         # Middleware
│   └── Requests/           # Form Requests
├── Jobs/                   # Фоновые задачи
├── Livewire/               # Livewire компоненты
├── Models/                 # Eloquent модели
│   ├── Abstracts/          # Базовые абстрактные модели
│   ├── Course/             # Модели курсов
│   ├── Lesson/             # Модели уроков
│   ├── Topic/              # Модели топиков
│   └── User/               # Модели пользователей
├── Notifications/          # Уведомления
├── Observers/              # Model Observers
├── Providers/              # Service Providers
└── Services/               # Бизнес-логика
    ├── Lesson/             # Сервисы уроков
    └── FptTtsService.php   # TTS сервис
```

---

## Ключевые сервисы

### 1. LessonGenerationService

**Путь:** `app/Services/Lesson/LessonGenerationService.php`

**Назначение:** Генерация уроков через OpenAI API.

**Методы:**
- `generateLesson(LessonDto $dto): array` - Генерация контента урока
- `createEmbedding(LessonDto $dto): array` - Создание векторного embedding

**Особенности:**
- Использует GPT-4o-mini для генерации
- Адаптирует сложность по уровню CEFR (A1-C2)
- Возвращает JSON со структурированным контентом

```php
// Структура генерируемого урока
[
    'lesson_name' => 'Название урока',
    'lesson_text' => 'Текст урока',
    'vocabulary' => [...],
    'grammar_highlights' => [...],
    'dialogue' => [...],
    'exercises' => [
        'gap_fill' => [...],
        'sentence_ordering' => [...],
        'true_false' => [...],
        'matching' => [...],
        'quiz' => [...]
    ],
    'homework' => '...',
    'common_mistakes' => [...],
    'culture_notes' => '...'
]
```

### 2. LessonService

**Путь:** `app/Services/Lesson/LessonService.php`

**Назначение:** Оркестрация создания и обновления уроков.

**Методы:**
- `fillLessonContent(Lesson $lesson, LessonDto $dto): Lesson` - Заполнение урока контентом
- `regenerateLesson(Lesson $lesson, LessonDto $dto): Lesson` - Регенерация урока

**Зависимости:**
- `LessonGenerationService` - генерация контента
- `LessonSimilarityService` - проверка дубликатов
- `LessonHistoryService` - версионирование

### 3. LessonGradingService

**Путь:** `app/Services/Lesson/LessonGradingService.php`

**Назначение:** Система оценивания студентов.

**Методы:**
- `startAttempt()` - Начать попытку
- `completeAttempt()` - Завершить с оценкой
- `assignLesson()` - Назначить урок студенту
- `getBestScore()` / `getLatestScore()` - Получить оценки

### 4. LessonSimilarityService

**Путь:** `app/Services/Lesson/LessonSimilarityService.php`

**Назначение:** Поиск похожих уроков через pgvector.

**Как работает:**
- Использует cosine similarity через оператор `<=>`
- Порог схожести: 0.9 (90%)
- Предотвращает создание дубликатов

### 5. LessonAudioService

**Путь:** `app/Services/Lesson/LessonAudioService.php`

**Назначение:** Генерация аудио через TTS.

**Генерирует аудио для:**
- Vocabulary (слова)
- Gap-fill упражнения

---

## Модели данных

### Lesson (Урок)

```
lessons
├── id (PK)
├── uuid (UNIQUE)
├── user_id (FK → users)
├── topic_id (FK → topics)
├── lesson_name
├── lesson_text
├── language_level (ENUM: A1-C2)
├── lesson_lang (ENUM)
├── grammar_data (JSON)
├── vocabulary (JSON)
├── dialogue (JSON)
├── exercises (JSON)
├── homework (TEXT)
├── common_mistakes (JSON)
├── culture_notes (TEXT)
├── embedding (vector)
├── is_draft (BOOLEAN)
└── timestamps
```

### Связи уроков

```
Lesson
├── belongsTo User
├── belongsTo Topic
├── hasMany LessonHistory
├── hasMany LessonAttempt
├── hasMany LessonScore
├── hasMany LessonAssignment
├── hasMany LessonAudioFile
├── hasMany LessonFeedback
└── belongsToMany Course
```

### Система оценивания

```
lesson_attempts
├── id
├── lesson_id
├── user_id
├── assignment_id (nullable)
├── attempt_number
├── exercises_snapshot (JSON)
├── user_answers (JSON)
├── started_at
├── completed_at
└── is_completed

lesson_scores
├── id
├── attempt_id
├── lesson_id
├── user_id
├── total_questions
├── correct_answers
├── score_percentage
├── auto_graded
└── graded_at
```

---

## Паттерны и практики

### 1. DTO (Data Transfer Object)

Используется `LessonDto` для передачи данных между слоями:

```php
class LessonDto
{
    public function __construct(
        public string $topic,
        public LanguageLevelEnum $level,
        public string $native_lang,
        public string $learning_lang,
        public array $keywords
    ) {}
}
```

### 2. Enums

Строго типизированные значения через PHP 8.1 Enums:

```php
enum LanguageLevelEnum: string
{
    case A1 = 'A1';
    case A2 = 'A2';
    // ...
}

enum TableNameEnum: string
{
    case LESSONS = 'lessons';
    // ...
}
```

### 3. Service Layer

Бизнес-логика вынесена в сервисы:
- Контроллеры только маршрутизируют
- Сервисы содержат логику
- Модели - только данные и связи

### 4. Event Broadcasting

WebSocket события через Laravel Reverb:

```php
// Отправка события
broadcast(new MessageSent($message))->toOthers();

// Подписка в Livewire
Echo.private('chat.{userId}')
    .listen('MessageSent', (e) => { ... });
```

### 5. Jobs для тяжёлых операций

Генерация уроков выполняется в фоне:

```php
// Диспатчинг задачи
GenerateLessonJob::dispatch($lesson, $dto);

// Обработка в очереди
public function handle(): void
{
    $this->lessonService->fillLessonContent($this->lesson, $this->dto);
}
```

---

## Безопасность

### Авторизация
- Используются Policies и Middleware
- `lesson.owner` middleware проверяет владельца

### Валидация
- Form Requests для входных данных
- Livewire валидация в компонентах

### XSS защита
- Blade `{{ }}` автоматически экранирует
- Не использовать `{!! !!}` для пользовательских данных

### SQL Injection
- Eloquent ORM с параметризованными запросами
- Явные операторы в WHERE (`=`, `!=`)

---

## Тестирование

```bash
# Все тесты
php artisan test

# Unit тесты
php artisan test --testsuite=Unit

# Feature тесты
php artisan test --testsuite=Feature

# Конкретный тест
php artisan test tests/Unit/Services/LessonServiceTest.php
```

---

## Полезные команды

```bash
# Очистка кэша
php artisan cache:clear
php artisan config:clear
php artisan view:clear

# Миграции
php artisan migrate
php artisan migrate:fresh --seed

# Очереди
php artisan queue:work

# WebSocket сервер
php artisan reverb:start

# IDE Helper
php artisan ide-helper:models
```
