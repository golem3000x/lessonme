<?php

declare(strict_types=1);

namespace App\Models\Lesson;

use App\Casts\EmbeddingCast;
use App\Enums\TableNameEnum;
use App\Models\Topic\Topic;
use App\Models\User;
use Barryvdh\LaravelIdeHelper\Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $user_id
 * @property int $topic_id
 * @property string $lesson_name
 * @property string $lesson_text
 * @property array<array-key, mixed>|null $grammar_data
 * @property array<array-key, mixed>|null $vocabulary
 * @property array<array-key, mixed>|null $quiz
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Topic|null $topicRelation
 * @property-read User $user
 *
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
 *
 * @mixin Eloquent
 *
 * @method static Builder<static>|Lesson whereLessonName($value)
 *
 * @property string|null $embedding
 *
 * @method static Builder<static>|Lesson whereEmbedding($value)
 */
class Lesson extends Model
{
    protected $table = TableNameEnum::LESSONS->value;

    public const float SIMILARITY_THRESHOLD = 0.9;

    protected $fillable = [
        'user_id',
        'topic_id',
        'lesson_name',
        'lesson_text',
        'grammar_data',
        'vocabulary',
        'quiz',
        'embedding',
    ];

    protected $casts = [
        'grammar_data' => 'array',
        'vocabulary' => 'array',
        'quiz' => 'array',
        'embedding' => EmbeddingCast::class,
    ];

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsTo<Topic, $this>
     */
    public function topicRelation(): BelongsTo
    {
        return $this->belongsTo(Topic::class, 'topic_id');
    }

    public function histories(): HasMany
    {
        return $this->hasMany(LessonHistory::class);
    }
}
