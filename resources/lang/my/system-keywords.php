<?php

declare(strict_types=1);

use App\Enums\System\SystemKeywordEnum;

return [
    SystemKeywordEnum::PHP->name => 'PHP',
    SystemKeywordEnum::JAVASCRIPT->name => 'JavaScript',
    SystemKeywordEnum::PYTHON->name => 'Python',
    SystemKeywordEnum::LARAVEL->name => 'Laravel',
    SystemKeywordEnum::REACT->name => 'React',

    SystemKeywordEnum::BAKING->name => 'မုန့်ဖုတ်ခြင်း',
    SystemKeywordEnum::GRILLING->name => 'ကင်ခြင်း',
    SystemKeywordEnum::SPICES->name => 'အမွှေးအကြိုင်များ',
    SystemKeywordEnum::VEGETARIAN->name => 'သက်သတ်လွတ်',
    SystemKeywordEnum::DESSERTS->name => 'အချိုပွဲများ',

    SystemKeywordEnum::HOTELS->name => 'ဟိုတယ်များ',
    SystemKeywordEnum::FLIGHTS->name => 'လေယာဉ်ခရီးစဉ်များ',
    SystemKeywordEnum::BACKPACKING->name => 'ကျောပိုးအိတ်ခရီးသွားခြင်း',
    SystemKeywordEnum::BEACHES->name => 'ကမ်းခြေများ',
    SystemKeywordEnum::GUIDES->name => 'လမ်းညွှန်များ',

    SystemKeywordEnum::PAINTING->name => 'ပန်းချီဆွဲခြင်း',
    SystemKeywordEnum::SCULPTURE->name => 'ပန်းပုထုခြင်း',
    SystemKeywordEnum::PHOTOGRAPHY->name => 'ဓာတ်ပုံရိုက်ခြင်း',
    SystemKeywordEnum::DESIGN->name => 'ဒီဇိုင်း',
    SystemKeywordEnum::SKETCHING->name => 'ပုံကြမ်းဆွဲခြင်း',
];
