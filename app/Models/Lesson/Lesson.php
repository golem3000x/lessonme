<?php

declare(strict_types=1);

namespace App\Models\Lesson;

use App\Enums\LanguageLevelEnum;
use App\Enums\TableNameEnum;
use App\Models\Abstracts\Lesson\BaseLesson;
use App\Models\Topic\Topic;
use Barryvdh\LaravelIdeHelper\Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $uuid
 * @property int $user_id
 * @property int $topic_id
 * @property string $lesson_name
 * @property LanguageLevelEnum $language_level
 * @property string $lesson_text
 * @property array<array-key, mixed>|null $grammar_data
 * @property array<array-key, mixed>|null $vocabulary
 * @property array<array-key, mixed>|null $dialogue
 * @property array<array-key, mixed>|null $exercises
 * @property string|null $homework
 * @property array<array-key, mixed>|null $common_mistakes
 * @property string|null $culture_notes
 * @property array<array-key, mixed>|null $quiz
 * @property string|null $embedding
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read string $formatted_created_at
 * @property-read string $level_badge_color
 * @property-read Topic|null $topic
 * @property-read Collection<int, LessonHistory> $histories
 * @property-read int|null $histories_count
 * @property-read Collection<int, LessonAssignment> $assignments
 * @property-read int|null $assignments_count
 * @property-read Collection<int, LessonAttempt> $attempts
 * @property-read int|null $attempts_count
 * @property-read Collection<int, LessonScore> $scores
 * @property-read int|null $scores_count
 * @property-read \App\Models\User $user
 *
 * @method static Builder<static>|Lesson newModelQuery()
 * @method static Builder<static>|Lesson newQuery()
 * @method static Builder<static>|Lesson query()
 * @method static Builder<static>|Lesson whereCreatedAt($value)
 * @method static Builder<static>|Lesson whereGrammarData($value)
 * @method static Builder<static>|Lesson whereVocabulary($value)
 * @method static Builder<static>|Lesson whereDialogue($value)
 * @method static Builder<static>|Lesson whereExercises($value)
 * @method static Builder<static>|Lesson whereHomework($value)
 * @method static Builder<static>|Lesson whereCommonMistakes($value)
 * @method static Builder<static>|Lesson whereCultureNotes($value)
 * @method static Builder<static>|Lesson whereQuiz($value)
 * @method static Builder<static>|Lesson whereId($value)
 * @method static Builder<static>|Lesson whereLessonName($value)
 * @method static Builder<static>|Lesson whereLessonText($value)
 * @method static Builder<static>|Lesson whereLanguageLevel($value)
 * @method static Builder<static>|Lesson whereTopicId($value)
 * @method static Builder<static>|Lesson whereUserId($value)
 * @method static Builder<static>|Lesson whereEmbedding($value)
 * @method static Builder<static>|Lesson whereUpdatedAt($value)
 *
 * @mixin Eloquent
 * @mixin \Eloquent
 */
class Lesson extends BaseLesson
{
    protected $table = TableNameEnum::LESSONS->value;

    public const float SIMILARITY_THRESHOLD = 0.9;

    /**
     * @return BelongsTo<Topic, $this>
     */
    public function topic(): BelongsTo
    {
        return $this->belongsTo(Topic::class, 'topic_id');
    }

    public function histories(): HasMany
    {
        return $this->hasMany(LessonHistory::class);
    }

    /**
     * @return HasMany<LessonAssignment>
     */
    public function assignments(): HasMany
    {
        return $this->hasMany(LessonAssignment::class);
    }

    /**
     * @return HasMany<LessonAttempt>
     */
    public function attempts(): HasMany
    {
        return $this->hasMany(LessonAttempt::class);
    }

    /**
     * @return HasMany<LessonScore>
     */
    public function scores(): HasMany
    {
        return $this->hasMany(LessonScore::class);
    }
}
