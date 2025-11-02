<?php

declare(strict_types=1);

namespace App\Models\Abstracts\Slug;

use App\Models\Abstracts\BaseModel;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

abstract class SlugEntity extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'slug',
        'is_system',
        'is_approved',
    ];

    public $timestamps = true;

    /**
     * Relation to translations table
     */
    abstract public function translations(): HasMany;

    /**
     * Relation to visibility table
     */
    abstract public function visibility(): HasMany;

    /**
     * User relationship
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get translation for specific language or fallback to first available
     */
    public function translation(?string $lang = null)
    {
        $lang = $lang ?? app()->getLocale();

        return $this->translations
            ->where('language_code', $lang)
            ->first()
            ?? $this->translations->first();
    }

    /**
     * Get translated value for specific language
     */
    public function translatedValue(?string $lang = null): string
    {
        $translation = $this->translations
            ->firstWhere('language_code', $lang ?? app()->getLocale());

        return $translation?->value ?? '—';
    }

    /**
     * Scope to get entities visible for specific user
     */
    public function scopeVisibleForUser(Builder $query, int $userId): Builder
    {
        return $query
            ->where(function (Builder $q) use ($userId): void {
                $q->where(function (Builder $sys) use ($userId): void {
                    $sys->whereNull('user_id')
                        ->whereDoesntHave('visibility', function (Builder $v) use ($userId): void {
                            $v->where('user_id', $userId)
                                ->where('is_hidden', true);
                        });
                })->orWhere(function (Builder $own) use ($userId): void {
                    $own->where('user_id', $userId)
                        ->whereDoesntHave('visibility', function (Builder $v) use ($userId): void {
                            $v->where('user_id', $userId)
                                ->where('is_hidden', true);
                        })
                        ->where('is_approved', true);
                });
            });
    }
}
