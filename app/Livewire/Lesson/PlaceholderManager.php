<?php

declare(strict_types=1);

namespace App\Livewire\Lesson;

use Livewire\Attributes\On;
use Livewire\Component;

/**
 * Global placeholder manager - always loaded in layout
 *
 * SIMPLIFIED RESPONSIBILITY: Clean creating_placeholders when user is not on /lessons page
 *
 * WHY NEEDED:
 * - LessonIndex cleans placeholders by detecting actual lessons in the list
 * - But if user is NOT on /lessons, creating_placeholders won't be cleaned
 * - This component ensures creating_placeholders are cleaned via WebSocket
 *
 * NOTE: regenerating_placeholders are NOT cleaned here - LessonIndex handles them
 * because we know exact lesson_id and can check if it exists in the list
 */
class PlaceholderManager extends Component
{
    /**
     * Clean creating placeholders when lesson creation completes
     * Only for NEW lessons, not regenerations
     *
     * @param  int|null  $lesson_database_id  - Database ID of created/regenerated lesson
     * @param  bool  $force_cleanup  - Force cleanup (for EXISTS/ERROR cases)
     */
    #[On('lesson-created')]
    public function lessonCreated(?int $lesson_database_id = null, bool $force_cleanup = false): void
    {
        $creating = session('lesson_placeholders_creating', []);
        $regenerating = session('lesson_placeholders_regenerating', []);

        // Only clean creating_placeholders if this is NOT a regeneration
        if ($lesson_database_id === null || ! isset($regenerating[$lesson_database_id])) {
            // NEW CREATION: Remove first placeholder from the list (FIFO)
            if (! empty($creating)) {
                array_shift($creating);
                session(['lesson_placeholders_creating' => $creating]);
            }
        }

        // Note: regenerating_placeholders are cleaned by LessonIndex when it sees the lesson
    }

    /**
     * No UI - this component is invisible
     */
    public function render(): string
    {
        return '<div></div>';
    }
}
