<?php

declare(strict_types=1);

namespace App\Enums;

enum SystemKeywordEnum: string
{
    // Programming
    case PHP = 'PHP';
    case JAVASCRIPT = 'JavaScript';
    case PYTHON = 'Python';
    case LARAVEL = 'Laravel';
    case REACT = 'React';

    // Cooking
    case BAKING = 'Baking';
    case GRILLING = 'Grilling';
    case SPICES = 'Spices';
    case VEGETARIAN = 'Vegetarian';
    case DESSERTS = 'Desserts';

    // Travel
    case HOTELS = 'Hotels';
    case FLIGHTS = 'Flights';
    case BACKPACKING = 'Backpacking';
    case BEACHES = 'Beaches';
    case GUIDES = 'Guides';

    // Art
    case PAINTING = 'Painting';
    case SCULPTURE = 'Sculpture';
    case PHOTOGRAPHY = 'Photography';
    case DESIGN = 'Design';
    case SKETCHING = 'Sketching';

    public function topic(): SystemTopicEnum
    {
        return match ($this) {
            self::PHP, self::JAVASCRIPT, self::PYTHON, self::LARAVEL, self::REACT => SystemTopicEnum::PROGRAMMING,
            self::BAKING, self::GRILLING, self::SPICES, self::VEGETARIAN, self::DESSERTS => SystemTopicEnum::COOKING,
            self::HOTELS, self::FLIGHTS, self::BACKPACKING, self::BEACHES, self::GUIDES => SystemTopicEnum::TRAVEL,
            self::PAINTING, self::SCULPTURE, self::PHOTOGRAPHY, self::DESIGN, self::SKETCHING => SystemTopicEnum::ART,
        };
    }
}
