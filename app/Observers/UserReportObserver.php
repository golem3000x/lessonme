<?php

declare(strict_types=1);

namespace App\Observers;

use App\Enums\System\UserRoleEnum;
use App\Models\User\User;
use App\Models\User\UserReport;
use Filament\Notifications\Notification;

class UserReportObserver
{
    public function created(UserReport $report): void
    {
        $this->notifyAdmins($report);
    }

    protected function notifyAdmins(UserReport $report): void
    {
        $admins = User::whereHas('roles', function ($query): void {
            $query->where('name', UserRoleEnum::ADMIN->value);
        })->get();

        foreach ($admins as $admin) {
            Notification::make()
                ->title(__('admin.new_report_notification'))
                ->body(__('admin.new_report_body', [
                    'reporter' => $report->reporter->full_name,
                    'reported' => $report->reportedUser->full_name,
                    'reason' => $report->reason->label(),
                ]))
                ->warning()
                ->sendToDatabase($admin);
        }
    }
}
