<?php

declare(strict_types=1);

namespace App\Enums\Notifications;

enum LessonAssignmentStatusEnum: string
{
    case ASSIGNED = 'assigned';
    case SENT = 'sent';
    case UNASSIGNED = 'unassigned';
    case REMOVED = 'removed';
    case COMPLETED = 'completed';
    case OVERDUE = 'overdue';
}
