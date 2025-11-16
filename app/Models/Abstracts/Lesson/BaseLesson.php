<?php

declare(strict_types=1);

namespace App\Models\Abstracts\Lesson;

use App\Casts\EmbeddingCast;
use App\Enums\LanguageLevelEnum;
use App\Models\Abstracts\BaseModel;
use App\Models\User;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $user_id
 * @property int|null $topic_id
 * @property string $lesson_name
 * @property string $lesson_text
 * @property LanguageLevelEnum|null $language_level
 * @property array<array-key, mixed>|null $grammar_data
 * @property array<array-key, mixed>|null $vocabulary
 * @property array<array-key, mixed>|null $quiz
 * @property string|null $embedding
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read User $user
 */
abstract class BaseLesson extends BaseModel
{
    /** @use HasFactory<UserFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'topic_id',
        'lesson_name',
        'lesson_text',
        'language_level',
        'grammar_data',
        'vocabulary',
        'quiz',
        'embedding',
    ];

    protected $casts = [
        'language_level' => LanguageLevelEnum::class,
        'grammar_data' => 'array',
        'vocabulary' => 'array',
        'quiz' => 'array',
        'embedding' => EmbeddingCast::class,
    ];

    public $timestamps = true;

    /**
     * User relationship
     *
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get level badge color based on language level
     */
    public function getLevelBadgeColorAttribute(): string
    {
        return match ($this->language_level) {
            LanguageLevelEnum::A1 => 'bg-green-100 text-green-700',
            LanguageLevelEnum::A2 => 'bg-emerald-100 text-emerald-700',
            LanguageLevelEnum::B1 => 'bg-blue-100 text-blue-700',
            LanguageLevelEnum::B2 => 'bg-indigo-100 text-indigo-700',
            LanguageLevelEnum::C1 => 'bg-purple-100 text-purple-700',
            LanguageLevelEnum::C2 => 'bg-pink-100 text-pink-700',
            default => 'bg-gray-100 text-gray-700',
        };
    }

    /**
     * Get formatted created date
     */
    public function getFormattedCreatedAtAttribute(): string
    {
        return $this->created_at?->format('d M Y, H:i') ?? '';
    }
}
