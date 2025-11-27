<?php

declare(strict_types=1);

namespace App\Filament\Resources\LessonFeedbackResource\Pages;

use App\Filament\Resources\LessonFeedbackResource;
use Filament\Resources\Pages\ListRecords;

class ListLessonFeedback extends ListRecords
{
    protected static string $resource = LessonFeedbackResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
