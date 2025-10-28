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
            ->where(function ($q) use ($userId): void {
                $q->whereNull('user_id')
                    ->whereDoesntHave('visibility', function ($v) use ($userId): void {
                        // Exclude only keywords explicitly hidden by the user
                        $v->where('user_id', '=', $userId)
                            ->where('is_hidden', '=', true);
                    });
            })
            ->orWhere(function ($q) use ($userId): void {
                $q->where('user_id', '=', $userId)
                    ->whereNull('deleted_at');
            });
    }
}
