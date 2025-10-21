<?php
declare(strict_types=1);

namespace App\Models;

use App\Enums\TableNameEnum;
use Barryvdh\LaravelIdeHelper\Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @property-read Collection<int, Lesson> $lessons
 * @property-read int|null $lessons_count
 * @method static Builder<static>|Topic newModelQuery()
 * @method static Builder<static>|Topic newQuery()
 * @method static Builder<static>|Topic query()
 * @mixin Eloquent
 */
class Topic extends Model
{
    protected $table = TableNameEnum::TOPICS->value;
    protected $fillable = ['name'];
    public $timestamps = true;

    public function lessons(): BelongsToMany
    {
        return $this->belongsToMany(Lesson::class);
    }
}
