<?php

declare(strict_types=1);

namespace App\Models\Lesson;

use App\Enums\System\LanguageLevelEnum;
use App\Enums\System\TableNameEnum;
use App\Models\Abstracts\Lesson\BaseLesson;
use App\Models\Topic\Topic;
use Database\Factories\Lesson\LessonFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $uuid
 * @property int $user_id
 * @property int|null $topic_id
 * @property string $lesson_name
 * @property string|null $lesson_text
 * @property LanguageLevelEnum $language_level
 * @property array<array-key, mixed>|null $grammar_data
 * @property array<array-key, mixed>|null $vocabulary
 * @property array<array-key, mixed>|null $dialogue
 * @property array<array-key, mixed>|null $exercises
 * @property string|null $homework
 * @property array<array-key, mixed>|null $common_mistakes
 * @property string|null $culture_notes
 * @property array|null $embedding
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, \App\Models\Lesson\LessonAssignment> $assignments
 * @property-read int|null $assignments_count
 * @property-read Collection<int, \App\Models\Lesson\LessonAttempt> $attempts
 * @property-read int|null $attempts_count
 * @property-read string $formatted_created_at
 * @property-read string $level_badge_color
 * @property-read Collection<int, \App\Models\Lesson\LessonHistory> $histories
 * @property-read int|null $histories_count
 * @property-read Collection<int, \App\Models\Lesson\LessonScore> $scores
 * @property-read int|null $scores_count
 * @property-read Topic|null $topic
 * @property-read \App\Models\User $user
 *
 * @method static LessonFactory factory($count = null, $state = [])
 * @method static Builder<static>|Lesson newModelQuery()
 * @method static Builder<static>|Lesson newQuery()
 * @method static Builder<static>|Lesson query()
 * @method static Builder<static>|Lesson whereCommonMistakes($value)
 * @method static Builder<static>|Lesson whereCreatedAt($value)
 * @method static Builder<static>|Lesson whereCultureNotes($value)
 * @method static Builder<static>|Lesson whereDialogue($value)
 * @method static Builder<static>|Lesson whereEmbedding($value)
 * @method static Builder<static>|Lesson whereExercises($value)
 * @method static Builder<static>|Lesson whereGrammarData($value)
 * @method static Builder<static>|Lesson whereHomework($value)
 * @method static Builder<static>|Lesson whereId($value)
 * @method static Builder<static>|Lesson whereLanguageLevel($value)
 * @method static Builder<static>|Lesson whereLessonName($value)
 * @method static Builder<static>|Lesson whereLessonText($value)
 * @method static Builder<static>|Lesson whereTopicId($value)
 * @method static Builder<static>|Lesson whereUpdatedAt($value)
 * @method static Builder<static>|Lesson whereUserId($value)
 * @method static Builder<static>|Lesson whereUuid($value)
 * @method static Builder<static>|Lesson whereVocabulary($value)
 *
 * @mixin \Eloquent
 */
class Lesson extends BaseLesson
{
    protected $table = TableNameEnum::LESSONS->value;

    public const float SIMILARITY_THRESHOLD = 0.9;

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory(): LessonFactory
    {
        return LessonFactory::new();
    }

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
