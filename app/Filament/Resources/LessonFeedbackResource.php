<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Enums\FeedbackRatingEnum;
use App\Filament\Resources\LessonFeedbackResource\Pages;
use App\Models\Lesson\LessonFeedback;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class LessonFeedbackResource extends Resource
{
    protected static ?string $model = LessonFeedback::class;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    protected static ?int $navigationSort = 5;

    public static function getNavigationLabel(): string
    {
        return __('admin.lesson_feedbacks');
    }

    public static function getModelLabel(): string
    {
        return __('admin.lesson_feedback');
    }

    public static function getPluralModelLabel(): string
    {
        return __('admin.lesson_feedbacks');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('lesson_id')
                    ->relationship('lesson', 'id')
                    ->required(),
                Forms\Components\Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required(),
                Forms\Components\TextInput::make('rating')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('comment')
                    ->columnSpanFull(),
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
                    ->url(fn (LessonFeedback $record): string => route('lessons.show', $record->lesson->uuid))
                    ->openUrlInNewTab(),
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
                    ->limit(100)
                    ->searchable()
                    ->toggleable()
                    ->wrap(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('admin.submitted_at'))
                    ->dateTime()
                    ->sortable()
                    ->since(),
            ])
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
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
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
            'index' => Pages\ListLessonFeedback::route('/'),
        ];
    }
}
