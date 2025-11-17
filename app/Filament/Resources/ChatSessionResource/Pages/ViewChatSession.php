<?php

declare(strict_types=1);

namespace App\Filament\Resources\ChatSessionResource\Pages;

use App\Enums\ChatSessionStatusEnum;
use App\Events\MessageSent;
use App\Filament\Resources\ChatSessionResource;
use App\Models\Support\Message;
use Filament\Actions;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Enums\FontWeight;
use Illuminate\Contracts\Support\Htmlable;

class ViewChatSession extends ViewRecord
{
    protected static string $resource = ChatSessionResource::class;

    public string $new_message = '';

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('close_chat')
                ->label(__('admin.action_close_chat'))
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->requiresConfirmation()
                ->visible(fn () => $this->record->status === ChatSessionStatusEnum::OPEN)
                ->action(function (): void {
                    $this->record->update(['status' => ChatSessionStatusEnum::CLOSED]);
                    $this->redirect(ChatSessionResource::getUrl('index'));
                }),
            Actions\Action::make('reopen_chat')
                ->label(__('admin.action_reopen_chat'))
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->visible(fn () => $this->record->status === ChatSessionStatusEnum::CLOSED)
                ->action(function (): void {
                    $this->record->update(['status' => ChatSessionStatusEnum::OPEN]);
                }),
        ];
    }

    public function getTitle(): string|Htmlable
    {
        return __('admin.chat_with', ['name' => $this->record->user->name]);
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make(__('admin.user_info'))
                    ->schema([
                        Infolists\Components\TextEntry::make('user.name')
                            ->label(__('admin.name')),
                        Infolists\Components\TextEntry::make('user.email')
                            ->label(__('admin.email')),
                        Infolists\Components\TextEntry::make('status')
                            ->label(__('admin.chat_status'))
                            ->badge()
                            ->color(fn (ChatSessionStatusEnum $state): string => match ($state) {
                                ChatSessionStatusEnum::OPEN => 'success',
                                ChatSessionStatusEnum::CLOSED => 'gray',
                            })
                            ->formatStateUsing(fn (ChatSessionStatusEnum $state): string => match ($state) {
                                ChatSessionStatusEnum::OPEN => __('admin.status_open'),
                                ChatSessionStatusEnum::CLOSED => __('admin.status_closed'),
                            }),
                        Infolists\Components\TextEntry::make('created_at')
                            ->label(__('admin.created_at'))
                            ->dateTime('d.m.Y H:i'),
                        Infolists\Components\TextEntry::make('last_message_at')
                            ->label(__('admin.last_message_at'))
                            ->dateTime('d.m.Y H:i')
                            ->default(__('admin.no_messages')),
                    ])
                    ->columns(3),

                Infolists\Components\Section::make(__('admin.message_history'))
                    ->schema([
                        Infolists\Components\RepeatableEntry::make('messages')
                            ->label('')
                            ->schema([
                                Infolists\Components\TextEntry::make('message')
                                    ->label('')
                                    ->color(fn (Message $record) => $record->is_from_admin ? 'info' : 'success')
                                    ->weight(FontWeight::Medium)
                                    ->suffix(fn (Message $record) => ' — '.$record->created_at->format('d.m.Y H:i'))
                                    ->icon(fn (Message $record) => $record->is_from_admin ? 'heroicon-o-user-circle' : 'heroicon-o-chat-bubble-left')
                                    ->iconColor(fn (Message $record) => $record->is_from_admin ? 'info' : 'success'),
                            ])
                            ->contained(false),
                    ]),

                Infolists\Components\Section::make(__('admin.send_message'))
                    ->schema([
                        Infolists\Components\ViewEntry::make('send_message_form')
                            ->view('filament.resources.chat-session.send-message-form'),
                    ])
                    ->visible(fn () => $this->record->status === ChatSessionStatusEnum::OPEN),
            ]);
    }

    public function sendMessage(): void
    {
        $this->validate([
            'new_message' => 'required|string|max:1000',
        ]);

        $message = Message::create([
            'chat_session_id' => $this->record->id,
            'user_id' => auth()->id(),
            'message' => $this->new_message,
            'is_from_admin' => true,
            'is_read' => false,
        ]);

        // Обновляем сессию
        $this->record->update([
            'last_message_at' => now(),
            'unread_count' => 0, // Сбрасываем счетчик непрочитанных от пользователя
        ]);

        // Загружаем отношения для события
        $message->load('user');

        // Отправляем событие через WebSocket пользователю
        broadcast(new MessageSent($message))->toOthers();

        // Очищаем поле ввода
        $this->new_message = '';

        // Обновляем страницу для отображения нового сообщения
        $this->refreshFormData(['messages']);
    }
}
