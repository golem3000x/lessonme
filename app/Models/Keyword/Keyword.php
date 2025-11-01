<?php

declare(strict_types=1);

namespace App\Models\Keyword;

use App\Enums\TableNameEnum;
use App\Models\Topic\Topic;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Keyword extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = TableNameEnum::KEYWORDS->value;

    protected $fillable = [
        'user_id',
        'topic_id',
        'slug',
        'is_system',
        'is_hidden',
        'is_approved'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function translations(): HasMany
    {
        return $this->hasMany(KeywordTranslation::class);
    }

    public function topic(): BelongsTo
    {
        return $this->belongsTo(Topic::class);
    }

    public function visibility(): HasMany
    {
        return $this->hasMany(UserKeywordVisibility::class);
    }

    public function translation(?string $lang = null)
    {
        $lang = $lang ?? app()->getLocale();

        return $this->translations
            ->where('language_code', $lang)
            ->first()
            ?? $this->translations->first();
    }

    public function value(?string $lang = null): string
    {
        return $this->translation($lang)?->value ?? '—';
    }

    public function scopeVisibleForUser($query, $userId)
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
