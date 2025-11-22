<?php

declare(strict_types=1);

namespace App\Models\Lesson;

use App\Enums\System\TableNameEnum;
use App\Models\Abstracts\Lesson\BaseLesson;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property string $uuid
 * @property int $lesson_id
 * @property int $user_id
 * @property int $topic_id
 * @property string $lesson_name
 * @property string $lesson_text
 * @property \App\Enums\System\LanguageLevelEnum $language_level
 * @property array<array-key, mixed>|null $grammar_data
 * @property array<array-key, mixed>|null $vocabulary
 * @property array<array-key, mixed>|null $dialogue
 * @property array<array-key, mixed>|null $exercises
 * @property string|null $homework
 * @property array<array-key, mixed>|null $common_mistakes
 * @property string|null $culture_notes
 * @property string|null $quiz
 * @property array $embedding
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read string $formatted_created_at
 * @property-read string $level_badge_color
 * @property-read \App\Models\Lesson\Lesson $lesson
 * @property-read \App\Models\User $user
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LessonHistory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LessonHistory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LessonHistory query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LessonHistory whereCommonMistakes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LessonHistory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LessonHistory whereCultureNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LessonHistory whereDialogue($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LessonHistory whereEmbedding($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LessonHistory whereExercises($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LessonHistory whereGrammarData($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LessonHistory whereHomework($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LessonHistory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LessonHistory whereLanguageLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LessonHistory whereLessonId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LessonHistory whereLessonName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LessonHistory whereLessonText($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LessonHistory whereQuiz($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LessonHistory whereTopicId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LessonHistory whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LessonHistory whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LessonHistory whereUuid($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LessonHistory whereVocabulary($value)
 *
 * @mixin \Eloquent
 */
class LessonHistory extends BaseLesson
{
    protected $table = TableNameEnum::LESSON_HISTORIES->value;

    protected array $extra_fillable = [
        'lesson_id',
    ];

    // Other fillable and casts inherited from BaseLesson

    public function lesson(): BelongsTo
    {
        return $this->belongsTo(Lesson::class);
    }

    // user() method inherited from BaseLesson
}
