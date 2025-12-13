<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Enums\System\ReportReasonEnum;
use App\Filament\Resources\UserReportResource\Pages;
use App\Models\User;
use App\Models\UserReport;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class UserReportResource extends Resource
{
    protected static ?string $model = UserReport::class;

    protected static ?string $navigationIcon = 'heroicon-o-flag';

    protected static ?int $navigationSort = 2;

    public static function getNavigationLabel(): string
    {
        return __('admin.reports');
    }

    public static function getModelLabel(): string
    {
        return __('admin.report');
    }

    public static function getPluralModelLabel(): string
    {
        return __('admin.reports');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('admin.report_details'))
                    ->schema([
                        Forms\Components\Select::make('reporter_id')
                            ->label(__('admin.reporter'))
                            ->relationship('reporter', 'email')
                            ->disabled(),
                        Forms\Components\Select::make('reported_user_id')
                            ->label(__('admin.reported_user'))
                            ->relationship('reportedUser', 'email')
                            ->disabled(),
                        Forms\Components\Select::make('reason')
                            ->label(__('admin.reason'))
                            ->options(ReportReasonEnum::options())
                            ->disabled(),
                        Forms\Components\Textarea::make('description')
                            ->label(__('admin.description'))
                            ->disabled()
                            ->columnSpanFull(),
                    ])->columns(2),
                Forms\Components\Section::make(__('admin.resolution'))
                    ->schema([
                        Forms\Components\Toggle::make('is_resolved')
                            ->label(__('admin.is_resolved'))
                            ->disabled(),
                        Forms\Components\DateTimePicker::make('resolved_at')
                            ->label(__('admin.resolved_at'))
                            ->disabled(),
                        Forms\Components\Select::make('resolved_by')
                            ->label(__('admin.resolved_by'))
                            ->relationship('resolvedBy', 'email')
                            ->disabled(),
                    ])->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                Tables\Columns\TextColumn::make('reporter.email')
                    ->label(__('admin.reporter'))
                    ->searchable()
                    ->sortable()
                    ->description(fn (UserReport $record): string => $record->reporter->full_name),
                Tables\Columns\TextColumn::make('reportedUser.email')
                    ->label(__('admin.reported_user'))
                    ->searchable()
                    ->sortable()
                    ->description(fn (UserReport $record): string => $record->reportedUser->full_name),
                Tables\Columns\TextColumn::make('reason')
                    ->label(__('admin.reason'))
                    ->badge()
                    ->formatStateUsing(fn (ReportReasonEnum $state): string => $state->label())
                    ->color(fn (ReportReasonEnum $state): string => match ($state) {
                        ReportReasonEnum::SPAM => 'warning',
                        ReportReasonEnum::HARASSMENT => 'danger',
                        ReportReasonEnum::SCAM => 'danger',
                        ReportReasonEnum::INAPPROPRIATE_CONTENT => 'warning',
                        ReportReasonEnum::FAKE_PROFILE => 'warning',
                        ReportReasonEnum::OFFENSIVE_LANGUAGE => 'warning',
                        ReportReasonEnum::UNPROFESSIONAL_BEHAVIOR => 'warning',
                        ReportReasonEnum::OTHER => 'gray',
                    }),
                Tables\Columns\IconColumn::make('is_resolved')
                    ->label(__('admin.resolved'))
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('admin.created'))
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('reason')
                    ->label(__('admin.reason'))
                    ->options(ReportReasonEnum::options()),
                Tables\Filters\TernaryFilter::make('is_resolved')
                    ->label(__('admin.filter_resolved'))
                    ->placeholder(__('admin.filter_all'))
                    ->trueLabel(__('admin.filter_resolved_yes'))
                    ->falseLabel(__('admin.filter_resolved_no')),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label(__('admin.action_view')),
                Tables\Actions\Action::make('resolve')
                    ->label(__('admin.action_resolve'))
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading(__('admin.resolve_report_title'))
                    ->modalDescription(__('admin.resolve_report_description'))
                    ->visible(fn (UserReport $record): bool => ! $record->is_resolved)
                    ->action(function (UserReport $record): void {
                        $record->update([
                            'is_resolved' => true,
                            'resolved_at' => now(),
                            'resolved_by' => Auth::id(),
                        ]);

                        Notification::make()
                            ->title(__('admin.report_resolved'))
                            ->success()
                            ->send();
                    }),
                Tables\Actions\Action::make('ban_user')
                    ->label(__('admin.action_ban_user'))
                    ->icon('heroicon-o-no-symbol')
                    ->color('danger')
                    ->form([
                        Forms\Components\Textarea::make('ban_reason')
                            ->label(__('admin.ban_reason'))
                            ->required()
                            ->maxLength(500),
                        Forms\Components\DateTimePicker::make('ban_expires_at')
                            ->label(__('admin.ban_expires_at'))
                            ->hint(__('admin.ban_expires_hint'))
                            ->nullable(),
                    ])
                    ->requiresConfirmation()
                    ->modalHeading(__('admin.ban_user_title'))
                    ->modalDescription(__('admin.ban_user_description'))
                    ->visible(fn (UserReport $record): bool => ! $record->is_resolved && ! $record->reportedUser->isBanned())
                    ->action(function (UserReport $record, array $data): void {
                        $record->reportedUser->ban(
                            reason: $data['ban_reason'],
                            expires_at: $data['ban_expires_at'] ? \Carbon\Carbon::parse($data['ban_expires_at']) : null,
                            banned_by: Auth::id()
                        );

                        $record->update([
                            'is_resolved' => true,
                            'resolved_at' => now(),
                            'resolved_by' => Auth::id(),
                        ]);

                        Notification::make()
                            ->title(__('admin.user_banned'))
                            ->success()
                            ->send();
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('resolve_selected')
                        ->label(__('admin.action_resolve_selected'))
                        ->icon('heroicon-o-check')
                        ->requiresConfirmation()
                        ->action(function ($records): void {
                            $records->each(function (UserReport $record): void {
                                if (! $record->is_resolved) {
                                    $record->update([
                                        'is_resolved' => true,
                                        'resolved_at' => now(),
                                        'resolved_by' => Auth::id(),
                                    ]);
                                }
                            });

                            Notification::make()
                                ->title(__('admin.reports_resolved'))
                                ->success()
                                ->send();
                        }),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUserReports::route('/'),
            'view' => Pages\ViewUserReport::route('/{record}'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        $unresolved_count = UserReport::where('is_resolved', false)->count();

        return $unresolved_count > 0 ? (string) $unresolved_count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'danger';
    }
}
