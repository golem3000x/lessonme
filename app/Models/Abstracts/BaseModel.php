<?php

declare(strict_types=1);

namespace App\Models\Abstracts;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

abstract class BaseModel extends Model
{
    use HasFactory;

    protected $fillable = [];

    protected array $extra_fillable = [];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->fillable = array_merge($this->fillable, $this->extra_fillable);
    }
}
