<?php

declare(strict_types=1);

namespace App\Models\Lesson;

use App\Enums\LanguageLevelEnum;
use App\Enums\TableNameEnum;
use App\Models\Abstracts\Lesson\BaseLesson;
use App\Models\Topic\Topic;
use Barryvdh\LaravelIdeHelper\Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $user_id
 * @property int $topic_id
 * @property string $lesson_name
 * @property LanguageLevelEnum $language_level
 * @property string $lesson_text
 * @property array<array-key, mixed>|null $grammar_data
 * @property array<array-key, mixed>|null $vocabulary
 * @property array<array-key, mixed>|null $quiz
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Topic|null $topic
 * @method static Builder<static>|Lesson newModelQuery()
 * @method static Builder<static>|Lesson newQuery()
 * @method static Builder<static>|Lesson query()
 * @method static Builder<static>|Lesson whereCreatedAt($value)
 * @method static Builder<static>|Lesson whereGrammarData($value)
 * @method static Builder<static>|Lesson whereId($value)
 * @method static Builder<static>|Lesson whereLessonText($value)
 * @method static Builder<static>|Lesson whereQuiz($value)
 * @method static Builder<static>|Lesson whereTopicId($value)
 * @method static Builder<static>|Lesson whereUpdatedAt($value)
 * @method static Builder<static>|Lesson whereUserId($value)
 * @method static Builder<static>|Lesson whereVocabulary($value)
 * @mixin Eloquent
 * @method static Builder<static>|Lesson whereLessonName($value)
 * @property string|null $embedding
 * @method static Builder<static>|Lesson whereEmbedding($value)
 * @property-read string $formatted_created_at
 * @property-read string $level_badge_color
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Lesson\LessonHistory> $histories
 * @property-read int|null $histories_count
 * @property-read \App\Models\User $user
 * @method static Builder<static>|Lesson whereLanguageLevel($value)
 * @mixin \Eloquent
 */
class Lesson extends BaseLesson
{
    protected $table = TableNameEnum::LESSONS->value;

    public const float SIMILARITY_THRESHOLD = 0.9;

    // Fillable and casts inherited from BaseLesson

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
}
