<?php

declare(strict_types=1);

namespace App\Filament\Resources\UserReportResource\Pages;

use App\Filament\Resources\UserReportResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Facades\Auth;

/**
 * @property \App\Models\User\UserReport $record
 */
class ViewUserReport extends ViewRecord
{
    protected static string $resource = UserReportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('resolve')
                ->label(__('admin.action_resolve'))
                ->icon('heroicon-o-check')
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading(__('admin.resolve_report_title'))
                ->modalDescription(__('admin.resolve_report_description'))
                ->visible(fn (): bool => ! $this->record->is_resolved)
                ->action(function (): void {
                    $this->record->update([
                        'is_resolved' => true,
                        'resolved_at' => now(),
                        'resolved_by' => Auth::id(),
                    ]);

                    Notification::make()
                        ->title(__('admin.report_resolved'))
                        ->success()
                        ->send();

                    $this->refreshFormData(['is_resolved', 'resolved_at', 'resolved_by']);
                }),
        ];
    }
}
