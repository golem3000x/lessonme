# 🔌 API Сервисов

## Lesson Services

### LessonGenerationService

Генерация уроков через OpenAI API.

#### generateLesson

```php
public function generateLesson(LessonDto $lesson_dto): array
```

**Параметры:**
| Параметр | Тип | Описание |
|----------|-----|----------|
| `$lesson_dto` | `LessonDto` | DTO с параметрами урока |

**Возвращает:** `array` - Структурированные данные урока

**Пример:**
```php
$dto = new LessonDto(
    topic: 'Ordering food at a restaurant',
    level: LanguageLevelEnum::B1,
    native_lang: 'vi',
    learning_lang: 'en',
    keywords: ['menu', 'waiter', 'bill']
);

$generator = app(LessonGenerationService::class);
$data = $generator->generateLesson($dto);
```

**Структура ответа:**
```php
[
    'lesson_name' => string,     // Название (на native_lang)
    'lesson_text' => string,     // Текст урока (на native_lang)
    'vocabulary' => [
        ['word' => string, 'translation' => string, 'example' => string],
        // ...
    ],
    'grammar_highlights' => [
        [
            'phrase' => string,
            'label' => string,
            'explanation' => string,
            'formula' => string,
            'example' => string,
            'breakdown' => string
        ],
        // ...
    ],
    'dialogue' => [
        ['speaker' => 'A'|'B', 'line' => string],
        // ...
    ],
    'exercises' => [
        'gap_fill' => [['sentence' => string, 'answer' => string]],
        'sentence_ordering' => [['words' => array, 'answer' => string]],
        'true_false' => [['statement' => string, 'answer' => 'True'|'False']],
        'matching' => [['word' => string, 'match' => string]],
        'quiz' => [['question' => string, 'options' => array, 'answer' => string]]
    ],
    'homework' => string,
    'common_mistakes' => [
        ['mistake' => string, 'correction' => string, 'explanation' => string]
    ],
    'culture_notes' => string
]
```

#### createEmbedding

```php
public function createEmbedding(LessonDto $lesson_dto): array
```

**Параметры:**
| Параметр | Тип | Описание |
|----------|-----|----------|
| `$lesson_dto` | `LessonDto` | DTO с параметрами урока |

**Возвращает:** `array<float>` - Вектор embedding (1536 измерений)

---

### LessonService

Оркестрация создания уроков.

#### fillLessonContent

```php
public function fillLessonContent(Lesson $lesson, LessonDto $dto): Lesson
```

**Исключения:** `LessonExistsException` - если найден похожий урок

**Пример:**
```php
$service = app(LessonService::class);

try {
    $lesson = $service->fillLessonContent($lesson, $dto);
} catch (LessonExistsException $e) {
    $similar = $e->getSimilarLesson();
    // Обработка дубликата
}
```

#### regenerateLesson

```php
public function regenerateLesson(Lesson $lesson, LessonDto $dto): Lesson
```

**Важно:** Автоматически создаёт snapshot в history перед регенерацией.

---

### LessonGradingService

Система оценивания.

#### startAttempt

```php
public function startAttempt(
    Lesson $lesson,
    User $user,
    ?LessonAssignment $assignment = null
): LessonAttempt
```

**Возвращает:** Новый объект `LessonAttempt`

**Пример:**
```php
$grading = app(LessonGradingService::class);
$attempt = $grading->startAttempt($lesson, auth()->user());
```

#### completeAttempt

```php
public function completeAttempt(
    LessonAttempt $attempt,
    array $user_answers,
    array $scores,
    int $total_score,
    int $max_score
): LessonScore
```

**Параметры:**
| Параметр | Тип | Описание |
|----------|-----|----------|
| `$attempt` | `LessonAttempt` | Активная попытка |
| `$user_answers` | `array` | Ответы пользователя по типам |
| `$scores` | `array` | Баллы по типам упражнений |
| `$total_score` | `int` | Общий балл |
| `$max_score` | `int` | Максимально возможный балл |

**Пример:**
```php
$score = $grading->completeAttempt(
    attempt: $attempt,
    user_answers: [
        'gap_fill' => ['answer1', 'answer2'],
        'matching' => ['match1', 'match2'],
    ],
    scores: ['gap_fill' => 2, 'matching' => 1],
    total_score: 3,
    max_score: 5
);

echo $score->score_percentage; // 60.00
```

#### assignLesson

```php
public function assignLesson(
    Lesson $lesson,
    User $teacher,
    User $student,
    ?\DateTimeInterface $due_date = null
): LessonAssignment
```

**Пример:**
```php
$assignment = $grading->assignLesson(
    lesson: $lesson,
    teacher: $teacher,
    student: $student,
    due_date: now()->addWeek()
);
```

---

### LessonAudioService

Генерация TTS аудио.

#### generateAudioForLesson

```php
public function generateAudioForLesson(Lesson $lesson): void
```

Генерирует аудио для vocabulary и gap_fill упражнений.

#### deleteAudioFiles

```php
public function deleteAudioFiles(Lesson $lesson): void
```

Удаляет все аудио файлы урока (вызывается автоматически при удалении урока).

---

### LessonHistoryService

Версионирование уроков.

#### snapshot

```php
public function snapshot(Lesson $lesson): LessonHistory
```

Создаёт snapshot текущего состояния урока.

#### restore

```php
public function restore(LessonHistory $history): Lesson
```

Восстанавливает урок из snapshot. **Важно:** Перед восстановлением создаёт snapshot текущего состояния.

---

### LessonSimilarityService

Поиск дубликатов через pgvector.

#### findSimilar

```php
public function findSimilar(int $user_id, array $embedding, LessonDto $dto): ?Lesson
```

**Параметры:**
| Параметр | Тип | Описание |
|----------|-----|----------|
| `$user_id` | `int` | ID пользователя |
| `$embedding` | `array<float>` | Вектор embedding |
| `$dto` | `LessonDto` | DTO для дополнительных фильтров |

**Возвращает:** `Lesson|null` - Похожий урок или null

**Алгоритм:**
1. Ищет уроки того же пользователя
2. С тем же уровнем языка
3. С similarity score >= 0.9

---

## FptTtsService

TTS через FPT.AI API.

#### generateAudio

```php
public function generateAudio(string $text, string $voice): ?string
```

**Параметры:**
| Параметр | Тип | Описание |
|----------|-----|----------|
| `$text` | `string` | Текст для озвучки |
| `$voice` | `string` | ID голоса (зависит от языка) |

**Возвращает:** `string|null` - URL аудио файла или null при ошибке

**Поддерживаемые языки:**
- Vietnamese: `banmai`, `leminh`
- English: `en-US-Standard-A`

---

## DTOs

### LessonDto

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

    public function levelValue(): string;
    public function getKeywordsAsString(): string;
}
```

**Пример создания:**
```php
$dto = new LessonDto(
    topic: 'Travel vocabulary',
    level: LanguageLevelEnum::A2,
    native_lang: SystemLanguageEnum::VIETNAMESE->value,
    learning_lang: SystemLanguageEnum::ENGLISH->value,
    keywords: ['airport', 'hotel', 'taxi']
);
```

---

## Enums

### LanguageLevelEnum

```php
enum LanguageLevelEnum: string
{
    case A1 = 'A1';  // Beginner
    case A2 = 'A2';  // Elementary
    case B1 = 'B1';  // Intermediate
    case B2 = 'B2';  // Upper-Intermediate
    case C1 = 'C1';  // Advanced
    case C2 = 'C2';  // Proficient
    
    public function description(): string;
}
```

### SystemLanguageEnum

```php
enum SystemLanguageEnum: string
{
    case ENGLISH = 'en';
    case VIETNAMESE = 'vi';
    // ...
}
```

### AssignmentStatusEnum

```php
enum AssignmentStatusEnum: string
{
    case PENDING = 'pending';
    case IN_PROGRESS = 'in_progress';
    case COMPLETED = 'completed';
    case OVERDUE = 'overdue';
}
```
