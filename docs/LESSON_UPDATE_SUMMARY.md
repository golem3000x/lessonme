# Lesson Structure Update

## Changes

### 1. New Lesson Generation Prompt

Updated system prompt in `LessonGenerationService` to generate extended lesson structure with new sections:
- Dialogue (dialogues)
- Exercises (4 types: gap_fill, sentence_ordering, true_false, matching)
- Homework (homework assignment)
- Common Mistakes (common mistakes)
- Culture Notes (cultural notes)

### 2. Updated JSON Response Structure

```json
{
  "topic": "string",
  "lesson_name": "string",
  "lesson_text": "string",
  "vocabulary": [...],
  "grammar_highlights": [...],
  "dialogue": [
    { "speaker": "A", "line": "..." }
  ],
  "exercises": {
    "gap_fill": [{ "sentence": "...", "answer": "..." }],
    "sentence_ordering": [{ "words": [...], "answer": "..." }],
    "true_false": [{ "statement": "...", "answer": "..." }],
    "matching": [{ "word": "...", "match": "..." }]
  },
  "homework": "string",
  "common_mistakes": [
    { "mistake": "...", "correction": "...", "explanation": "..." }
  ],
  "culture_notes": "string",
  "quiz": [...]
}
```

### 3. Updated Models

**BaseLesson.php:**
- Added new fields to `$fillable` and `$casts`
- Updated PHPDoc blocks

**Lesson.php and LessonHistory.php:**
- Updated PHPDoc blocks with new fields

### 4. Updated Services

**LessonGenerationService:**
- New system prompt

**LessonService:**
- Methods `createLesson()` and `regenerateLesson()` now save new fields

**LessonHistoryService:**
- Methods `snapshot()` and `restore()` now work with new fields

**LessonController:**
- Method `store()` saves all new fields

### 5. Updated Views

**resources/views/lessons/show.blade.php:**
Added new sections (in order after Vocabulary, before Quiz):
- 💬 Dialogue
- 📝 Exercises (4 types)
- 📚 Homework
- ⚠️ Common Mistakes
- 🌍 Culture Notes

**resources/views/lessons/history.blade.php:**
- Added statistics for new fields

### 6. Updated Migrations

**lessons table:**
- `dialogue` (json, nullable)
- `exercises` (json, nullable)
- `homework` (text, nullable)
- `common_mistakes` (json, nullable)
- `culture_notes` (text, nullable)

**lesson_histories table:**
- Same fields

### 7. Updated Tests

**LessonRelationTest.php:**
- Added new fields when creating Lesson

**LessonControllerTest.php:**
- Mock SlugService returns new structure

## How to Test

### 1. Run Migrations

```bash
docker compose exec laravel.test php artisan migrate:fresh --seed
```

### 2. Clear Cache

```bash
docker compose exec laravel.test php artisan optimize:clear
```

### 3. Run Tests

```bash
docker compose exec laravel.test php artisan test
```

### 4. Create New Lesson

1. Log into system
2. Create new lesson via form
3. Verify all new sections display:
   - Dialogue
   - Exercises (all 4 types)
   - Homework
   - Common Mistakes
   - Culture Notes

### 5. Check History

1. Open existing lesson
2. Go to history
3. Verify statistics display new fields

## Field Structure

### JSON Fields (array cast):
- `grammar_data`
- `vocabulary`
- `dialogue`
- `exercises`
- `common_mistakes`
- `quiz`

### TEXT Fields (string):
- `homework`
- `culture_notes`

## Compatibility

All existing lessons will continue to work since new fields:
- Nullable (can be null)
- Have fallback values ([] for arrays, null for strings)
- Checked for existence in blade templates

## Potential Issues

1. **LLM may not return all fields** - solved via fallback values
2. **Old lessons don't have new fields** - solved via nullable and blade checks
3. **Exercises may be empty** - solved via `!empty()` check
