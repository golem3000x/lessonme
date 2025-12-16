<?php

declare(strict_types=1);

use App\Enums\System\SystemKeywordEnum;

return [
    SystemKeywordEnum::PHP->name => 'PHP',
    SystemKeywordEnum::JAVASCRIPT->name => 'JavaScript',
    SystemKeywordEnum::PYTHON->name => 'Python',
    SystemKeywordEnum::LARAVEL->name => 'Laravel',
    SystemKeywordEnum::REACT->name => 'React',

    SystemKeywordEnum::BAKING->name => 'ການອົບ',
    SystemKeywordEnum::GRILLING->name => 'ການປີ້ງ',
    SystemKeywordEnum::SPICES->name => 'ເຄື່ອງເທດ',
    SystemKeywordEnum::VEGETARIAN->name => 'ອາຫານຜັກ',
    SystemKeywordEnum::DESSERTS->name => 'ຂອງຫວານ',

    SystemKeywordEnum::HOTELS->name => 'ໂຮງແຮມ',
    SystemKeywordEnum::FLIGHTS->name => 'ຖ້ຽວບິນ',
    SystemKeywordEnum::BACKPACKING->name => 'ການທ່ຽວແບບເປ້',
    SystemKeywordEnum::BEACHES->name => 'ຫາດຊາຍ',
    SystemKeywordEnum::GUIDES->name => 'ຄູ່ມື',

    SystemKeywordEnum::PAINTING->name => 'ການແຕ້ມຮູບ',
    SystemKeywordEnum::SCULPTURE->name => 'ການປັ້ນຮູບ',
    SystemKeywordEnum::PHOTOGRAPHY->name => 'ການຖ່າຍຮູບ',
    SystemKeywordEnum::DESIGN->name => 'ການອອກແບບ',
    SystemKeywordEnum::SKETCHING->name => 'ການແຕ້ມຮ່າງ',
];
