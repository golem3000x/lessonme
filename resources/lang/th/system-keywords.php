<?php

declare(strict_types=1);

use App\Enums\System\SystemKeywordEnum;

return [
    SystemKeywordEnum::PHP->name => 'PHP',
    SystemKeywordEnum::JAVASCRIPT->name => 'JavaScript',
    SystemKeywordEnum::PYTHON->name => 'Python',
    SystemKeywordEnum::LARAVEL->name => 'Laravel',
    SystemKeywordEnum::REACT->name => 'React',

    SystemKeywordEnum::BAKING->name => 'การอบขนม',
    SystemKeywordEnum::GRILLING->name => 'การย่าง',
    SystemKeywordEnum::SPICES->name => 'เครื่องเทศ',
    SystemKeywordEnum::VEGETARIAN->name => 'อาหารมังสวิรัติ',
    SystemKeywordEnum::DESSERTS->name => 'ขนมหวาน',

    SystemKeywordEnum::HOTELS->name => 'โรงแรม',
    SystemKeywordEnum::FLIGHTS->name => 'เที่ยวบิน',
    SystemKeywordEnum::BACKPACKING->name => 'การเดินทางแบบแบ็คแพ็ค',
    SystemKeywordEnum::BEACHES->name => 'ชายหาด',
    SystemKeywordEnum::GUIDES->name => 'คู่มือ',

    SystemKeywordEnum::PAINTING->name => 'การวาดภาพ',
    SystemKeywordEnum::SCULPTURE->name => 'ประติมากรรม',
    SystemKeywordEnum::PHOTOGRAPHY->name => 'การถ่ายภาพ',
    SystemKeywordEnum::DESIGN->name => 'การออกแบบ',
    SystemKeywordEnum::SKETCHING->name => 'การสเก็ตช์',
];
