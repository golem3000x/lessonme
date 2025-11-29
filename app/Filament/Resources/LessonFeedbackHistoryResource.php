<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Enums\FeedbackRatingEnum;
use App\Filament\Resources\LessonFeedbackHistoryResource\Pages;
use App\Models\Lesson\LessonFeedbackHistory;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class LessonFeedbackHistoryResource extends Resource
{
    protected static ?string $model = LessonFeedbackHistory::class;

    protected static ?string $navigationIcon = 'heroicon-o-archive-box';

    protected static ?int $navigationSort = 6;

    public static function getNavigationLabel(): string
    {
        return __('admin.feedback_history');
    }

    public static function getModelLabel(): string
    {
        return __('admin.feedback_history_item');
    }

    public static function getPluralModelLabel(): string
    {
        return __('admin.feedback_history');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('lesson_id')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('user_id')
                    ->required()
                    ->numeric(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('lesson.lesson_name')
                    ->label(__('admin.lesson'))
                    ->searchable()
                    ->sortable()
                    ->limit(50)
                    ->url(fn (LessonFeedbackHistory $record): string => route('lessons.show', $record->lesson->uuid)
                    )
                    ->openUrlInNewTab(),
                Tables\Columns\TextColumn::make('lessonHistory.lesson_name')
                    ->label(__('admin.history_version'))
                    ->searchable()
                    ->limit(40)
                    ->placeholder(__('admin.no_history_link')),
                Tables\Columns\TextColumn::make('user.name')
                    ->label(__('admin.user'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('rating')
                    ->label(__('admin.rating'))
                    ->badge()
                    ->color(fn (FeedbackRatingEnum $state): string => match ($state) {
                        FeedbackRatingEnum::LIKE => 'success',
                        FeedbackRatingEnum::DISLIKE => 'danger',
                    })
                    ->icon(fn (FeedbackRatingEnum $state): string => match ($state) {
                        FeedbackRatingEnum::LIKE => 'heroicon-o-hand-thumb-up',
                        FeedbackRatingEnum::DISLIKE => 'heroicon-o-hand-thumb-down',
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('comment')
                    ->label(__('admin.comment'))
                    ->limit(50)
                    ->searchable()
                    ->toggleable()
                    ->wrap(),
                Tables\Columns\TextColumn::make('original_created_at')
                    ->label(__('admin.original_date'))
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
                Tables\Columns\TextColumn::make('archived_at')
                    ->label(__('admin.archived_at'))
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->since(),
            ])
            ->defaultSort('archived_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('rating')
                    ->label(__('admin.rating'))
                    ->options([
                        FeedbackRatingEnum::LIKE->value => __('admin.like'),
                        FeedbackRatingEnum::DISLIKE->value => __('admin.dislike'),
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListLessonFeedbackHistories::route('/'),
            'view' => Pages\ViewLessonFeedbackHistory::route('/{record}'),
        ];
    }
}
