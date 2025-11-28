<?php

declare(strict_types=1);

namespace App\Filament\Resources\LessonFeedbackHistoryResource\Pages;

use App\Filament\Resources\LessonFeedbackHistoryResource;
use Filament\Resources\Pages\ListRecords;

class ListLessonFeedbackHistories extends ListRecords
{
    protected static string $resource = LessonFeedbackHistoryResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
