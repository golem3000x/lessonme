<?php

declare(strict_types=1);

namespace App\Models\Abstracts;

use Illuminate\Database\Eloquent\Model;

abstract class BaseModel extends Model
{
    /** @var list<string> */
    protected $fillable = [];

    /** @var array<int, string> */
    protected array $extra_fillable = [];

    /**
     * @param  array<string, mixed>  $attributes
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->fillable = array_merge($this->fillable, array_values($this->extra_fillable));
    }
}
