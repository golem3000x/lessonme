<?php

declare(strict_types=1);

use App\Enums\System\SystemKeywordEnum;

return [
    SystemKeywordEnum::PHP->name => 'PHP',
    SystemKeywordEnum::JAVASCRIPT->name => 'JavaScript',
    SystemKeywordEnum::PYTHON->name => 'Python',
    SystemKeywordEnum::LARAVEL->name => 'Laravel',
    SystemKeywordEnum::REACT->name => 'React',

    SystemKeywordEnum::BAKING->name => 'ការដុតនំប័ុង',
    SystemKeywordEnum::GRILLING->name => 'ការអាំង',
    SystemKeywordEnum::SPICES->name => 'គ្រឿងប្រា',
    SystemKeywordEnum::VEGETARIAN->name => 'ម្ហូបបន្លែ',
    SystemKeywordEnum::DESSERTS->name => 'បង្អែម',

    SystemKeywordEnum::HOTELS->name => 'សណ្ឋាគារ',
    SystemKeywordEnum::FLIGHTS->name => 'ជើងអាកាស',
    SystemKeywordEnum::BACKPACKING->name => 'ការដើរកម្សាន្ត',
    SystemKeywordEnum::BEACHES->name => 'ឆ្នេរ',
    SystemKeywordEnum::GUIDES->name => 'មែក្សែរណា',

    SystemKeywordEnum::PAINTING->name => 'ការគូររូប',
    SystemKeywordEnum::SCULPTURE->name => 'ចម្លាក់',
    SystemKeywordEnum::PHOTOGRAPHY->name => 'ការថតរូប',
    SystemKeywordEnum::DESIGN->name => 'ការរចនា',
    SystemKeywordEnum::SKETCHING->name => 'ការគូរស្កិត',
];
