# 🔌 Services API

## Lesson Services

### LessonGenerationService

Lesson generation via OpenAI API.

#### generateLesson

```php
public function generateLesson(LessonDto $lesson_dto): array
```

**Parameters:**
| Parameter | Type | Description |
|-----------|------|-------------|
| `$lesson_dto` | `LessonDto` | DTO with lesson parameters |

**Returns:** `array` - Structured lesson data

**Example:**
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

**Response Structure:**
```php
[
    'lesson_name' => string,     // Name (in native_lang)
    'lesson_text' => string,     // Lesson text (in native_lang)
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

**Parameters:**
| Parameter | Type | Description |
|-----------|------|-------------|
| `$lesson_dto` | `LessonDto` | DTO with lesson parameters |

**Returns:** `array<float>` - Embedding vector (1536 dimensions)

---

### LessonService

Lesson creation orchestration.

#### fillLessonContent

```php
public function fillLessonContent(Lesson $lesson, LessonDto $dto): Lesson
```

**Exceptions:** `LessonExistsException` - if similar lesson found

**Example:**
```php
$service = app(LessonService::class);

try {
    $lesson = $service->fillLessonContent($lesson, $dto);
} catch (LessonExistsException $e) {
    $similar = $e->getSimilarLesson();
    // Handle duplicate
}
```

#### regenerateLesson

```php
public function regenerateLesson(Lesson $lesson, LessonDto $dto): Lesson
```

**Important:** Automatically creates snapshot in history before regeneration.

---

### LessonGradingService

Grading system.

#### startAttempt

```php
public function startAttempt(
    Lesson $lesson,
    User $user,
    ?LessonAssignment $assignment = null
): LessonAttempt
```

**Returns:** New `LessonAttempt` object

**Example:**
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

**Parameters:**
| Parameter | Type | Description |
|-----------|------|-------------|
| `$attempt` | `LessonAttempt` | Active attempt |
| `$user_answers` | `array` | User answers by type |
| `$scores` | `array` | Scores by exercise type |
| `$total_score` | `int` | Total score |
| `$max_score` | `int` | Maximum possible score |

**Example:**
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

**Example:**
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

TTS audio generation.

#### generateAudioForLesson

```php
public function generateAudioForLesson(Lesson $lesson): void
```

Generates audio for vocabulary and gap_fill exercises.

#### deleteAudioFiles

```php
public function deleteAudioFiles(Lesson $lesson): void
```

Deletes all lesson audio files (called automatically on lesson deletion).

---

### LessonHistoryService

Lesson versioning.

#### snapshot

```php
public function snapshot(Lesson $lesson): LessonHistory
```

Creates snapshot of current lesson state.

#### restore

```php
public function restore(LessonHistory $history): Lesson
```

Restores lesson from snapshot. **Important:** Creates snapshot of current state before restoration.

---

### LessonSimilarityService

Duplicate search via pgvector.

#### findSimilar

```php
public function findSimilar(int $user_id, array $embedding, LessonDto $dto): ?Lesson
```

**Parameters:**
| Parameter | Type | Description |
|-----------|------|-------------|
| `$user_id` | `int` | User ID |
| `$embedding` | `array<float>` | Embedding vector |
| `$dto` | `LessonDto` | DTO for additional filters |

**Returns:** `Lesson|null` - Similar lesson or null

**Algorithm:**
1. Searches lessons of same user
2. With same language level
3. With similarity score >= 0.9

---

## FptTtsService

TTS via FPT.AI API.

#### generateAudio

```php
public function generateAudio(string $text, string $voice): ?string
```

**Parameters:**
| Parameter | Type | Description |
|-----------|------|-------------|
| `$text` | `string` | Text to vocalize |
| `$voice` | `string` | Voice ID (language dependent) |

**Returns:** `string|null` - Audio file URL or null on error

**Supported Languages:**
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

**Creation Example:**
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
