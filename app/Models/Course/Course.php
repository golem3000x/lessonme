<?php

declare(strict_types=1);

namespace App\Models\Course;

use App\Enums\System\LanguageLevelEnum;
use App\Enums\System\SystemLanguageEnum;
use App\Enums\System\TableNameEnum;
use App\Models\Lesson\Lesson;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $uuid
 * @property int $user_id
 * @property string $title
 * @property string|null $description
 * @property LanguageLevelEnum|null $language_level
 * @property SystemLanguageEnum $lesson_lang
 * @property string|null $cover_image
 * @property bool $is_published
 * @property int $passing_score
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property-read User $user
 * @property-read Collection<int, Lesson> $lessons
 * @property-read Collection<int, CourseProgress> $progress
 * @property-read int|null $lessons_count
 */
class Course extends Model
{
    use HasUuids;

    protected $table = TableNameEnum::COURSES->value;

    protected $fillable = [
        'uuid',
        'user_id',
        'title',
        'description',
        'language_level',
        'lesson_lang',
        'cover_image',
        'is_published',
        'passing_score',
    ];

    protected function casts(): array
    {
        return [
            'language_level' => LanguageLevelEnum::class,
            'lesson_lang' => SystemLanguageEnum::class,
            'is_published' => 'boolean',
            'passing_score' => 'integer',
        ];
    }

    /**
     * @return array<int, string>
     */
    public function uniqueIds(): array
    {
        return ['uuid'];
    }

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsToMany<Lesson, $this>
     */
    public function lessons(): BelongsToMany
    {
        return $this->belongsToMany(Lesson::class, TableNameEnum::COURSE_LESSONS->value)
            ->withPivot(['order', 'is_required'])
            ->withTimestamps()
            ->orderByPivot('order');
    }

    /**
     * @return HasMany<CourseProgress, $this>
     */
    public function progress(): HasMany
    {
        return $this->hasMany(CourseProgress::class);
    }

    /**
     * Check if a user has completed all required lessons.
     */
    public function isCompletedByUser(int $user_id): bool
    {
        $required_lesson_ids = $this->lessons()
            ->wherePivot('is_required', true)
            ->pluck('lessons.id');

        if ($required_lesson_ids->isEmpty()) {
            return false;
        }

        $passed_count = $this->progress()
            ->where('user_id', $user_id)
            ->where('is_passed', true)
            ->whereIn('lesson_id', $required_lesson_ids)
            ->count();

        return $passed_count >= $required_lesson_ids->count();
    }

    /**
     * Get the next unlocked lesson for a user.
     */
    public function getNextLessonForUser(int $user_id): ?Lesson
    {
        $lessons = $this->lessons()->wherePivot('is_required', true)->get();

        foreach ($lessons as $lesson) {
            $progress = $this->progress()
                ->where('user_id', $user_id)
                ->where('lesson_id', $lesson->id)
                ->where('is_passed', true)
                ->first();

            if (! $progress) {
                return $lesson;
            }
        }

        return null;
    }

    /**
     * Check if a lesson is unlocked for a user (previous lessons passed).
     */
    public function isLessonUnlockedForUser(int $lesson_id, int $user_id): bool
    {
        $lessons = $this->lessons()->wherePivot('is_required', true)->get();

        foreach ($lessons as $lesson) {
            if ($lesson->id === $lesson_id) {
                return true;
            }

            $progress = $this->progress()
                ->where('user_id', $user_id)
                ->where('lesson_id', $lesson->id)
                ->where('is_passed', true)
                ->first();

            if (! $progress) {
                return false;
            }
        }

        return false;
    }
}
