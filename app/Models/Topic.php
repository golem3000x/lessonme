<?php
declare(strict_types=1);

namespace App\Models;

use App\Enums\AvailableTopicEnum;
use App\Enums\TableNameEnum;
use Barryvdh\LaravelIdeHelper\Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @property-read Collection<int, Lesson> $lessons
 * @property-read int|null $lessons_count
 * @property-read int $name_id
 * @method static Builder<static>|Topic newModelQuery()
 * @method static Builder<static>|Topic newQuery()
 * @method static Builder<static>|Topic query()
 * @mixin Eloquent
 */
class Topic extends Model
{
    protected $table = TableNameEnum::TOPICS->value;
    protected $fillable = ['name_id'];
    public $timestamps = true;

    public function lessons(): BelongsToMany
    {
        return $this->belongsToMany(Lesson::class);
    }

    public function getNameAttribute(): string
    {
        return AvailableTopicEnum::from($this->name_id)->name;
    }
}
