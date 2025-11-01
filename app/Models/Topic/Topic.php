<?php

declare(strict_types=1);

namespace App\Models\Topic;

use App\Enums\TableNameEnum;
use App\Models\Keyword\Keyword;
use App\Models\Lesson;
use App\Models\User;
use Barryvdh\LaravelIdeHelper\Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $slug
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, Lesson> $lessons
 * @property-read int|null $lessons_count
 *
 * @method static Builder<static>|Topic newModelQuery()
 * @method static Builder<static>|Topic newQuery()
 * @method static Builder<static>|Topic query()
 * @method static Builder<static>|Topic whereCreatedAt($value)
 * @method static Builder<static>|Topic whereId($value)
 * @method static Builder<static>|Topic whereNameId($value)
 * @method static Builder<static>|Topic whereUpdatedAt($value)
 *
 * @mixin Eloquent
 */
class Topic extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = TableNameEnum::TOPICS->value;

    protected $fillable = [
        'user_id',
        'slug',
        'is_system',
        'is_approved',
    ];

    public $timestamps = true;

    public function lessons(): BelongsToMany
    {
        return $this->belongsToMany(Lesson::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function translations(): HasMany
    {
        return $this->hasMany(TopicTranslation::class);
    }

    public function keywords(): HasMany
    {
        return $this->hasMany(Keyword::class);
    }

    public function visibility(): HasMany
    {
        return $this->hasMany(UserTopicVisibility::class);
    }

    public function translation(?string $lang = null)
    {
        $lang = $lang ?? app()->getLocale();

        return $this->translations
            ->where('language_code', $lang)
            ->first()
            ?? $this->translations->first();
    }

    public function title(?string $lang = null): string
    {
        $translation = $this->translations
            ->firstWhere('language_code', $lang ?? app()->getLocale());

        return $translation?->value ?? '—';
    }

    public function scopeVisibleForUser($query, int $userId)
    {
        return $query
            ->whereNull('deleted_at')
            ->where(function ($q) use ($userId): void {
                // System topics visible unless explicitly hidden by the user
                $q->where(function ($sys) use ($userId): void {
                    $sys->whereNull('user_id')
                        ->whereDoesntHave('visibility', function ($v) use ($userId): void {
                            $v->where('user_id', '=', $userId)
                                ->where('is_hidden', '=', true);
                        });
                })
                    // User-owned topics also respect visibility and soft-deletes
                    ->orWhere(function ($own) use ($userId): void {
                        $own->where('user_id', '=', $userId)
                            ->whereDoesntHave('visibility', function ($v) use ($userId): void {
                                $v->where('user_id', '=', $userId)
                                    ->where('is_hidden', '=', true);
                            })
                            ->where('is_approved', '=', true);
                    });
            });
    }
}
