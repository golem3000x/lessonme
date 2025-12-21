# 🏗️ LessonMe Application Architecture

## Overview

LessonMe - language learning platform with AI-generated content. Built on Laravel 11 with Livewire 3.5+, uses OpenAI API for lesson generation.

---

## Technology Stack

| Component | Technology |
|-----------|------------|
| Backend | Laravel 11, PHP 8.1+ |
| Frontend | Livewire 3.5+, Alpine.js, TailwindCSS, DaisyUI |
| Database | PostgreSQL + pgvector |
| Real-time | Laravel Reverb (WebSocket) |
| AI | OpenAI GPT-4o-mini, Embeddings |
| TTS | FPT.AI Text-to-Speech |
| Queue | Laravel Queue (database driver) |

---

## Directory Structure

```
app/
├── Dto/                    # Data Transfer Objects
│   └── LessonDto.php       # DTO for lesson data transfer
├── Enums/                  # Enumerations
│   ├── Lesson/             # Lesson enums
│   ├── Notifications/      # Notification enums
│   └── System/             # System enums (languages, levels, tables)
├── Events/                 # Events (Broadcasting)
├── Exceptions/             # Custom exceptions
├── Http/
│   ├── Controllers/        # Controllers
│   ├── Middleware/         # Middleware
│   └── Requests/           # Form Requests
├── Jobs/                   # Background jobs
├── Livewire/               # Livewire components
├── Models/                 # Eloquent models
│   ├── Abstracts/          # Base abstract models
│   ├── Course/             # Course models
│   ├── Lesson/             # Lesson models
│   ├── Topic/              # Topic models
│   └── User/               # User models
├── Notifications/          # Notifications
├── Observers/              # Model Observers
├── Providers/              # Service Providers
└── Services/               # Business logic
    ├── Lesson/             # Lesson services
    └── FptTtsService.php   # TTS service
```

---

## Key Services

### 1. LessonGenerationService

**Path:** `app/Services/Lesson/LessonGenerationService.php`

**Purpose:** Lesson generation via OpenAI API.

**Methods:**
- `generateLesson(LessonDto $dto): array` - Generate lesson content
- `createEmbedding(LessonDto $dto): array` - Create vector embedding

**Features:**
- Uses GPT-4o-mini for generation
- Adapts complexity by CEFR level (A1-C2)
- Returns JSON with structured content

```php
// Generated lesson structure
[
    'lesson_name' => 'Lesson title',
    'lesson_text' => 'Lesson text',
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

**Path:** `app/Services/Lesson/LessonService.php`

**Purpose:** Lesson creation and update orchestration.

**Methods:**
- `fillLessonContent(Lesson $lesson, LessonDto $dto): Lesson` - Fill lesson with content
- `regenerateLesson(Lesson $lesson, LessonDto $dto): Lesson` - Regenerate lesson

**Dependencies:**
- `LessonGenerationService` - content generation
- `LessonSimilarityService` - duplicate checking
- `LessonHistoryService` - versioning

### 3. LessonGradingService

**Path:** `app/Services/Lesson/LessonGradingService.php`

**Purpose:** Student grading system.

**Methods:**
- `startAttempt()` - Start attempt
- `completeAttempt()` - Complete with grade
- `assignLesson()` - Assign lesson to student
- `getBestScore()` / `getLatestScore()` - Get scores

### 4. LessonSimilarityService

**Path:** `app/Services/Lesson/LessonSimilarityService.php`

**Purpose:** Similar lesson search via pgvector.

**How it works:**
- Uses cosine similarity via `<=>` operator
- Similarity threshold: 0.9 (90%)
- Prevents duplicate creation

### 5. LessonAudioService

**Path:** `app/Services/Lesson/LessonAudioService.php`

**Purpose:** Audio generation via TTS.

**Generates audio for:**
- Vocabulary (words)
- Gap-fill exercises

---

## Data Models

### Lesson

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

### Lesson Relationships

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

### Grading System

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

## Patterns and Practices

### 1. DTO (Data Transfer Object)

`LessonDto` used for data transfer between layers:

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

Strictly typed values via PHP 8.1 Enums:

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

Business logic moved to services:
- Controllers only route
- Services contain logic
- Models - only data and relationships

### 4. Event Broadcasting

WebSocket events via Laravel Reverb:

```php
// Send event
broadcast(new MessageSent($message))->toOthers();

// Subscribe in Livewire
Echo.private('chat.{userId}')
    .listen('MessageSent', (e) => { ... });
```

### 5. Jobs for Heavy Operations

Lesson generation runs in background:

```php
// Dispatch job
GenerateLessonJob::dispatch($lesson, $dto);

// Process in queue
public function handle(): void
{
    $this->lessonService->fillLessonContent($this->lesson, $this->dto);
}
```

---

## Security

### Authorization
- Policies and Middleware used
- `lesson.owner` middleware checks owner

### Validation
- Form Requests for input data
- Livewire validation in components

### XSS Protection
- Blade `{{ }}` automatically escapes
- Don't use `{!! !!}` for user data

### SQL Injection
- Eloquent ORM with parameterized queries
- Explicit operators in WHERE (`=`, `!=`)

---

## Testing

```bash
# All tests
php artisan test

# Unit tests
php artisan test --testsuite=Unit

# Feature tests
php artisan test --testsuite=Feature

# Specific test
php artisan test tests/Unit/Services/LessonServiceTest.php
```

---

## Useful Commands

```bash
# Clear cache
php artisan cache:clear
php artisan config:clear
php artisan view:clear

# Migrations
php artisan migrate
php artisan migrate:fresh --seed

# Queues
php artisan queue:work

# WebSocket server
php artisan reverb:start

# IDE Helper
php artisan ide-helper:models
```
