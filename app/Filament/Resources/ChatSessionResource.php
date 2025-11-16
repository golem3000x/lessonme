<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\ChatSessionResource\Pages;
use App\Models\ChatSession;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ChatSessionResource extends Resource
{
    protected static ?string $model = ChatSession::class;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    protected static ?int $navigationSort = 1;

    public static function getNavigationLabel(): string
    {
        return __('admin.chats');
    }

    public static function getModelLabel(): string
    {
        return __('admin.chat');
    }

    public static function getPluralModelLabel(): string
    {
        return __('admin.chats');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required(),
                Forms\Components\DateTimePicker::make('last_message_at'),
                Forms\Components\TextInput::make('unread_count')
                    ->required()
                    ->numeric()
                    ->default(0),
                Forms\Components\TextInput::make('status')
                    ->required()
                    ->maxLength(255)
                    ->default('open'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label(__('admin.user'))
                    ->searchable()
                    ->sortable()
                    ->description(fn (ChatSession $record): string => $record->user->email),
                Tables\Columns\TextColumn::make('last_message_at')
                    ->label(__('admin.last_message'))
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->default(__('admin.no_messages')),
                Tables\Columns\TextColumn::make('unread_count')
                    ->label(__('admin.unread'))
                    ->badge()
                    ->color(fn (int $state): string => $state > 0 ? 'danger' : 'gray')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label(__('admin.status'))
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'open' => 'success',
                        'closed' => 'gray',
                        default => 'warning',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'open' => __('admin.status_open'),
                        'closed' => __('admin.status_closed'),
                        default => $state,
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('admin.created'))
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('last_message_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label(__('admin.filter_status'))
                    ->options([
                        'open' => __('admin.status_open'),
                        'closed' => __('admin.status_closed'),
                    ]),
                Tables\Filters\Filter::make('has_unread')
                    ->label(__('admin.filter_unread'))
                    ->query(fn (Builder $query): Builder => $query->where('unread_count', '>', 0)),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label(__('admin.action_view')),
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
            'index' => Pages\ListChatSessions::route('/'),
            'view' => Pages\ViewChatSession::route('/{record}'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        $unread_count = ChatSession::where('unread_count', '>', 0)->count();

        return $unread_count > 0 ? (string) $unread_count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'danger';
    }
}
