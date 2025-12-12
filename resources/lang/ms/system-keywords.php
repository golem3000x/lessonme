<?php

declare(strict_types=1);

use App\Enums\System\SystemKeywordEnum;

return [
    SystemKeywordEnum::PHP->name => 'PHP',
    SystemKeywordEnum::JAVASCRIPT->name => 'JavaScript',
    SystemKeywordEnum::PYTHON->name => 'Python',
    SystemKeywordEnum::LARAVEL->name => 'Laravel',
    SystemKeywordEnum::REACT->name => 'React',

    SystemKeywordEnum::BAKING->name => 'Membakar',
    SystemKeywordEnum::GRILLING->name => 'Memanggang',
    SystemKeywordEnum::SPICES->name => 'Rempah',
    SystemKeywordEnum::VEGETARIAN->name => 'Vegetarian',
    SystemKeywordEnum::DESSERTS->name => 'Pencuci Mulut',

    SystemKeywordEnum::HOTELS->name => 'Hotel',
    SystemKeywordEnum::FLIGHTS->name => 'Penerbangan',
    SystemKeywordEnum::BACKPACKING->name => 'Backpacking',
    SystemKeywordEnum::BEACHES->name => 'Pantai',
    SystemKeywordEnum::GUIDES->name => 'Panduan',

    SystemKeywordEnum::PAINTING->name => 'Lukisan',
    SystemKeywordEnum::SCULPTURE->name => 'Arca',
    SystemKeywordEnum::PHOTOGRAPHY->name => 'Fotografi',
    SystemKeywordEnum::DESIGN->name => 'Reka Bentuk',
    SystemKeywordEnum::SKETCHING->name => 'Lakaran',
];
