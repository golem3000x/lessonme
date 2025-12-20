<?php

declare(strict_types=1);

namespace App\Models\Abstracts\Slug;

use App\Events\SlugEntityChanged;
use App\Models\Abstracts\BaseModel;
use App\Models\User\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @template TTranslation of SlugTranslation
 * @template TVisibility of UserSlugVisibility
 *
 * @property int $id
 * @property int|null $user_id
 * @property string $slug
 * @property bool $is_system
 * @property bool $is_approved
 */
abstract class Slug extends BaseModel
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'slug',
        'is_system',
        'is_approved',
    ];

    /** @var array<string, class-string> */
    protected $dispatchesEvents = [
        'created' => SlugEntityChanged::class,
        'updated' => SlugEntityChanged::class,
    ];

    public $timestamps = true;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_system' => 'boolean',
            'is_approved' => 'boolean',
        ];
    }

    /**
     * Relation to translations table
     *
     * @return HasMany<TTranslation, $this>
     */
    abstract public function translations(): HasMany;

    /**
     * Relation to visibility table
     *
     * @return HasMany<TVisibility, $this>
     */
    abstract public function visibility(): HasMany;

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
     * Get translation for specific language or fallback to first available
     */
    public function translation(?string $lang = null): ?SlugTranslation
    {
        $lang = $lang ?? app()->getLocale();

        /** @var SlugTranslation|null $translation */
        $translation = $this->translations
            ->where('language_code', '=', $lang)
            ->first();

        if ($translation) {
            return $translation;
        }

        /** @var SlugTranslation|null */
        return $this->translations->first();
    }

    /**
     * Get translated value for specific language
     */
    public function translatedValue(?string $lang = null): string
    {
        $lang = $lang ?? app()->getLocale();

        // Try to get translation for requested language
        /** @var SlugTranslation|null $translation */
        $translation = $this->translations
            ->firstWhere('language_code', '=', $lang);

        if ($translation) {
            return $translation->value;
        }

        // Fallback to first available translation
        /** @var SlugTranslation|null $firstTranslation */
        $firstTranslation = $this->translations->first();

        if ($firstTranslation) {
            return $firstTranslation->value;
        }

        // Last resort: return slug
        return $this->slug;
    }

    /**
     * Scope to get entities visible for specific user
     *
     * @param  Builder<static>  $query
     * @return Builder<static>
     */
    public function scopeVisibleForUser(Builder $query, int $userId): Builder
    {
        return $query
            ->where(function (Builder $q) use ($userId): void {
                $q->where(function (Builder $sys) use ($userId): void {
                    $sys->whereNull('user_id')
                        ->whereDoesntHave('visibility', function (Builder $v) use ($userId): void {
                            $v->where('user_id', '=', $userId)
                                ->where('is_hidden', '=', true);
                        });
                })->orWhere(function (Builder $own) use ($userId): void {
                    $own->where('user_id', '=', $userId)
                        ->whereDoesntHave('visibility', function (Builder $v) use ($userId): void {
                            $v->where('user_id', '=', $userId)
                                ->where('is_hidden', '=', true);
                        })
                        ->where('is_approved', '=', true);
                });
            });
    }
}
