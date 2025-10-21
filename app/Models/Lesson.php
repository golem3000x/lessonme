<?php
declare(strict_types=1);

namespace App\Models;

use App\Enums\TableNameEnum;
use Barryvdh\LaravelIdeHelper\Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $user_id
 * @property string|null $topic
 * @property string|null $lesson_text
 * @property array<array-key, mixed>|null $grammar_data
 * @property array<array-key, mixed>|null $vocabulary
 * @property array<array-key, mixed>|null $quiz
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read User $user
 * @method static Builder<static>|Lesson newModelQuery()
 * @method static Builder<static>|Lesson newQuery()
 * @method static Builder<static>|Lesson query()
 * @method static Builder<static>|Lesson whereCreatedAt($value)
 * @method static Builder<static>|Lesson whereGrammarData($value)
 * @method static Builder<static>|Lesson whereId($value)
 * @method static Builder<static>|Lesson whereLessonText($value)
 * @method static Builder<static>|Lesson whereQuiz($value)
 * @method static Builder<static>|Lesson whereTopic($value)
 * @method static Builder<static>|Lesson whereUpdatedAt($value)
 * @method static Builder<static>|Lesson whereUserId($value)
 * @method static Builder<static>|Lesson whereVocabulary($value)
 * @mixin Eloquent
 */
class Lesson extends Model
{
    protected $table = TableNameEnum::LESSONS->value;
    protected $fillable = [
        'user_id',
        'topic',
        'lesson_text',
        'grammar_data',
        'vocabulary',
        'quiz'
    ];
    protected $casts = [
        'grammar_data' => 'array',
        'vocabulary' => 'array',
        'quiz' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
