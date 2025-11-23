<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Enums\ChatSessionStatusEnum;
use App\Events\MessageSent;
use App\Models\Support\ChatSession;
use App\Models\Support\Message;
use Livewire\Attributes\On;
use Livewire\Component;

class ChatPanel extends Component
{
    public bool $is_open = false;

    public string $new_message = '';

    /** @var array<int, array<string, mixed>> */
    public array $messages = [];

    public ?ChatSession $chat_session = null;

    public function mount(): void
    {
        $this->loadChatSession();
    }

    protected function loadChatSession(): void
    {
        $user = auth()->user();

        if (! $user) {
            return;
        }

        // Получаем или создаем сессию чата для текущего пользователя
        $this->chat_session = ChatSession::firstOrCreate(
            ['user_id' => $user->id],
            ['status' => ChatSessionStatusEnum::OPEN]
        );

        // Загружаем последние 50 сообщений
        $this->messages = $this->chat_session
            ->messages()
            ->with('user')
            ->latest()
            ->limit(50)
            ->get()
            ->reverse()
            ->values()
            ->toArray();
    }

    #[On('chat-toggled')]
    public function toggleChat(bool $is_open): void
    {
        $this->is_open = $is_open;

        if ($is_open) {
            // Загружаем сообщения при открытии
            $this->loadChatSession();

            // Отмечаем все сообщения как прочитанные
            $this->markMessagesAsRead();
        }
    }

    public function sendMessage(): void
    {
        $this->validate([
            'new_message' => 'required|string|max:1000',
        ]);

        if (! $this->chat_session) {
            $this->loadChatSession();
        }

        if (! $this->chat_session) {
            return;
        }

        $message = Message::create([
            'chat_session_id' => $this->chat_session->id,
            'user_id' => auth()->id(),
            'message' => $this->new_message,
            'is_from_admin' => false,
            'is_read' => false,
        ]);

        // Обновляем сессию
        $this->chat_session->update([
            'last_message_at' => now(),
        ]);

        // Загружаем отношения для события
        $message->load('user');

        // Отправляем событие через WebSocket
        broadcast(new MessageSent($message))->toOthers();

        // Добавляем сообщение в массив
        $this->messages[] = $message->toArray();

        // Очищаем поле ввода
        $this->new_message = '';

        // Диспетчим событие для прокрутки к последнему сообщению
        $this->dispatch('message-sent');
    }

    /**
     * @param array<string, mixed> $message_data
     */
    #[On('message-received')]
    public function addMessage(array $message_data): void
    {
        // Добавляем новое сообщение в массив
        /** @var Message|null $message */
        $message = Message::with('user')->find($message_data['id']);

        if ($message) {
            // Проверяем, есть ли уже это сообщение
            $message_ids = array_column($this->messages, 'id');

            if (! in_array($message->id, $message_ids)) {
                $this->messages[] = $message->toArray();

                // Если чат открыт, отмечаем сообщение как прочитанное
                if ($this->is_open) {
                    $message->update(['is_read' => true]);
                }

                $this->dispatch('message-received');
            }
        }
    }

    protected function markMessagesAsRead(): void
    {
        if (! $this->chat_session) {
            return;
        }

        $this->chat_session->messages()
            ->where('is_read', '=', false)
            ->where('is_from_admin', '=', true)
            ->update(['is_read' => true]);

        $this->chat_session->update(['unread_count' => 0]);
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.chat-panel');
    }
}
