<?php

declare(strict_types=1);

use App\Enums\SystemKeywordEnum;

return [
    SystemKeywordEnum::PHP->name => 'PHP',
    SystemKeywordEnum::JAVASCRIPT->name => 'JavaScript',
    SystemKeywordEnum::PYTHON->name => 'Python',
    SystemKeywordEnum::LARAVEL->name => 'Laravel',
    SystemKeywordEnum::REACT->name => 'React',

    SystemKeywordEnum::BAKING->name => 'Nướng',
    SystemKeywordEnum::GRILLING->name => 'Nướng BBQ',
    SystemKeywordEnum::SPICES->name => 'Gia vị',
    SystemKeywordEnum::VEGETARIAN->name => 'Chay',
    SystemKeywordEnum::DESSERTS->name => 'Tráng miệng',

    SystemKeywordEnum::HOTELS->name => 'Khách sạn',
    SystemKeywordEnum::FLIGHTS->name => 'Chuyến bay',
    SystemKeywordEnum::BACKPACKING->name => 'Du lịch bụi',
    SystemKeywordEnum::BEACHES->name => 'Bãi biển',
    SystemKeywordEnum::GUIDES->name => 'Hướng dẫn',

    SystemKeywordEnum::PAINTING->name => 'Vẽ tranh',
    SystemKeywordEnum::SCULPTURE->name => 'Điêu khắc',
    SystemKeywordEnum::PHOTOGRAPHY->name => 'Nhiếp ảnh',
    SystemKeywordEnum::DESIGN->name => 'Thiết kế',
    SystemKeywordEnum::SKETCHING->name => 'Phác thảo',
];
