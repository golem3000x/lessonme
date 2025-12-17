<?php

declare(strict_types=1);

namespace App\Models\Lesson;

use App\Enums\Lesson\LessonAudioStatusEnum;
use App\Enums\System\LanguageLevelEnum;
use App\Enums\System\TableNameEnum;
use App\Models\Abstracts\Lesson\BaseLesson;
use App\Models\Course\Course;
use App\Models\Topic\Topic;
use App\Services\Lesson\LessonAudioService;
use Database\Factories\Lesson\LessonFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
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
 * @property \App\Enums\System\SystemLanguageEnum $lesson_lang
 * @property \App\Enums\Lesson\LessonCreationTypeEnum $creation_type
 * @property bool $is_draft
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
 * @property-read Collection<int, \App\Models\Lesson\LessonAudioFile> $audioFiles
 * @property-read int|null $audio_files_count
 * @property-read Collection<int, Course> $courses
 * @property-read int|null $courses_count
 * @property-read Collection<int, \App\Models\Lesson\LessonFeedback> $feedbacks
 * @property-read int|null $feedbacks_count
 * @property-read string $creation_type_badge_color
 * @property-read string $formatted_created_at
 * @property-read string $level_badge_color
 * @property-read Collection<int, \App\Models\Lesson\LessonHistory> $histories
 * @property-read int|null $histories_count
 * @property-read Collection<int, \App\Models\Lesson\LessonScore> $scores
 * @property-read int|null $scores_count
 * @property-read Topic|null $topic
 * @property-read \App\Models\User $user
 *
 * @method static \Database\Factories\Lesson\LessonFactory factory($count = null, $state = [])
 * @method static Builder<static>|Lesson newModelQuery()
 * @method static Builder<static>|Lesson newQuery()
 * @method static Builder<static>|Lesson query()
 * @method static Builder<static>|Lesson whereCommonMistakes($value)
 * @method static Builder<static>|Lesson whereCreatedAt($value)
 * @method static Builder<static>|Lesson whereCreationType($value)
 * @method static Builder<static>|Lesson whereCultureNotes($value)
 * @method static Builder<static>|Lesson whereDialogue($value)
 * @method static Builder<static>|Lesson whereEmbedding($value)
 * @method static Builder<static>|Lesson whereExercises($value)
 * @method static Builder<static>|Lesson whereGrammarData($value)
 * @method static Builder<static>|Lesson whereHomework($value)
 * @method static Builder<static>|Lesson whereId($value)
 * @method static Builder<static>|Lesson whereIsDraft($value)
 * @method static Builder<static>|Lesson whereLanguageLevel($value)
 * @method static Builder<static>|Lesson whereLessonLang($value)
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
     * Boot the model and register event listeners
     */
    protected static function boot(): void
    {
        parent::boot();

        // Automatically delete audio files when lesson is deleted
        static::deleting(function (Lesson $lesson): void {
            $audio_service = app(LessonAudioService::class);
            $audio_service->deleteAudioFiles($lesson);
        });
    }

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

    /**
     * @return HasMany<LessonHistory, $this>
     */
    public function histories(): HasMany
    {
        return $this->hasMany(LessonHistory::class);
    }

    /**
     * @return HasMany<LessonAssignment, $this>
     */
    public function assignments(): HasMany
    {
        return $this->hasMany(LessonAssignment::class);
    }

    /**
     * @return HasMany<LessonAttempt, $this>
     */
    public function attempts(): HasMany
    {
        return $this->hasMany(LessonAttempt::class);
    }

    /**
     * @return HasMany<LessonScore, $this>
     */
    public function scores(): HasMany
    {
        return $this->hasMany(LessonScore::class);
    }

    /**
     * @return HasMany<LessonAudioFile, $this>
     */
    public function audioFiles(): HasMany
    {
        return $this->hasMany(LessonAudioFile::class);
    }

    /**
     * @return HasMany<LessonFeedback, $this>
     */
    public function feedbacks(): HasMany
    {
        return $this->hasMany(LessonFeedback::class);
    }

    /**
     * @return BelongsToMany<Course, $this>
     */
    public function courses(): BelongsToMany
    {
        return $this->belongsToMany(Course::class, TableNameEnum::COURSE_LESSONS->value)
            ->withPivot(['order', 'is_required'])
            ->withTimestamps();
    }

    public function getVocabularyAudio(string $word): ?LessonAudioFile
    {
        return $this->audioFiles()
            ->where('audio_type', '=', 'vocabulary')
            ->where('audio_key', '=', $word)
            ->where('status', '=', LessonAudioStatusEnum::READY->value)
            ->first();
    }

    public function getGapFillAudio(int $index): ?LessonAudioFile
    {
        return $this->audioFiles()
            ->where('audio_type', '=', 'gap_fill')
            ->where('audio_key', '=', (string) $index)
            ->where('status', '=', LessonAudioStatusEnum::READY->value)
            ->first();
    }
}
