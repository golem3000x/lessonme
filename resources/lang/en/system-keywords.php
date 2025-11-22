<?php

declare(strict_types=1);

use App\Enums\System\SystemKeywordEnum;

return [
    SystemKeywordEnum::PHP->name => 'PHP',
    SystemKeywordEnum::JAVASCRIPT->name => 'JavaScript',
    SystemKeywordEnum::PYTHON->name => 'Python',
    SystemKeywordEnum::LARAVEL->name => 'Laravel',
    SystemKeywordEnum::REACT->name => 'React',

    SystemKeywordEnum::BAKING->name => 'Baking',
    SystemKeywordEnum::GRILLING->name => 'Grilling',
    SystemKeywordEnum::SPICES->name => 'Spices',
    SystemKeywordEnum::VEGETARIAN->name => 'Vegetarian',
    SystemKeywordEnum::DESSERTS->name => 'Desserts',

    SystemKeywordEnum::HOTELS->name => 'Hotels',
    SystemKeywordEnum::FLIGHTS->name => 'Flights',
    SystemKeywordEnum::BACKPACKING->name => 'Backpacking',
    SystemKeywordEnum::BEACHES->name => 'Beaches',
    SystemKeywordEnum::GUIDES->name => 'Guides',

    SystemKeywordEnum::PAINTING->name => 'Painting',
    SystemKeywordEnum::SCULPTURE->name => 'Sculpture',
    SystemKeywordEnum::PHOTOGRAPHY->name => 'Photography',
    SystemKeywordEnum::DESIGN->name => 'Design',
    SystemKeywordEnum::SKETCHING->name => 'Sketching',
];
