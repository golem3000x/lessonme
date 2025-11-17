# Обновление структуры урока

## Изменения

### 1. Новый промпт для генерации урока

Обновлен system prompt в `LessonGenerationService` для генерации расширенной структуры урока с новыми секциями:
- Dialogue (диалоги)
- Exercises (4 типа упражнений: gap_fill, sentence_ordering, true_false, matching)
- Homework (домашнее задание)
- Common Mistakes (типичные ошибки)
- Culture Notes (культурные заметки)

### 2. Обновленная структура JSON ответа

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

### 3. Обновленные модели

**BaseLesson.php:**
- Добавлены новые поля в `$fillable` и `$casts`
- Обновлены PHPDoc блоки

**Lesson.php и LessonHistory.php:**
- Обновлены PHPDoc блоки с новыми полями

### 4. Обновленные сервисы

**LessonGenerationService:**
- Новый system prompt

**LessonService:**
- Методы `createLesson()` и `regenerateLesson()` теперь сохраняют новые поля

**LessonHistoryService:**
- Методы `snapshot()` и `restore()` теперь работают с новыми полями

**LessonController:**
- Метод `store()` сохраняет все новые поля

### 5. Обновленные views

**resources/views/lessons/show.blade.php:**
Добавлены новые секции (в порядке после Vocabulary, перед Quiz):
- 💬 Dialogue
- 📝 Exercises (4 типа)
- 📚 Homework
- ⚠️ Common Mistakes
- 🌍 Culture Notes

**resources/views/lessons/history.blade.php:**
- Добавлена статистика по новым полям

### 6. Обновленные миграции

**lessons table:**
- `dialogue` (json, nullable)
- `exercises` (json, nullable)
- `homework` (text, nullable)
- `common_mistakes` (json, nullable)
- `culture_notes` (text, nullable)

**lesson_histories table:**
- Те же поля

### 7. Обновленные тесты

**LessonRelationTest.php:**
- Добавлены новые поля при создании Lesson

**LessonControllerTest.php:**
- Mock SlugService возвращает новую структуру

## Как протестировать

### 1. Запустите миграции

```bash
docker compose exec laravel.test php artisan migrate:fresh --seed
```

### 2. Очистите кэш

```bash
docker compose exec laravel.test php artisan optimize:clear
```

### 3. Запустите тесты

```bash
docker compose exec laravel.test php artisan test
```

### 4. Создайте новый урок

1. Войдите в систему
2. Создайте новый урок через форму
3. Проверьте, что все новые секции отображаются:
   - Dialogue
   - Exercises (все 4 типа)
   - Homework
   - Common Mistakes
   - Culture Notes

### 5. Проверьте историю

1. Откройте существующий урок
2. Перейдите в историю
3. Проверьте, что статистика отображает новые поля

## Структура полей

### JSON поля (array cast):
- `grammar_data`
- `vocabulary`
- `dialogue`
- `exercises`
- `common_mistakes`
- `quiz`

### TEXT поля (string):
- `homework`
- `culture_notes`

## Совместимость

Все существующие уроки продолжат работать, так как новые поля:
- Nullable (могут быть null)
- Имеют fallback значения ([] для массивов, null для строк)
- Проверяются на существование в blade шаблонах

## Возможные проблемы

1. **LLM может не вернуть все поля** - решено через fallback значения
2. **Старые уроки не имеют новых полей** - решено через nullable и проверки в blade
3. **Exercises могут быть пустыми** - решено через проверку `!empty()`
