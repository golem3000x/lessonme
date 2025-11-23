<?php

declare(strict_types=1);

namespace App\Models\Abstracts\Slug;

use App\Models\Abstracts\BaseModel;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @template TEntity of Slug
 */
abstract class UserSlugVisibility extends BaseModel
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'is_hidden',
    ];

    public $timestamps = false;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_hidden' => 'boolean',
        ];
    }

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
     * Relation to parent entity (Topic, Keyword, etc.)
     * 
     * @return BelongsTo<TEntity, $this>
     */
    abstract public function entity(): BelongsTo;
}
