<?php

declare(strict_types=1);

namespace App\Livewire\Chat;

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

        // Get or create chat session for current user
        $this->chat_session = ChatSession::firstOrCreate(
            ['user_id' => $user->id],
            ['status' => ChatSessionStatusEnum::OPEN]
        );

        // Load last 50 messages
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
            // Load messages when opening
            $this->loadChatSession();

            // Mark all messages as read
            $this->markMessagesAsRead();
        }
    }

    public function closeChat(): void
    {
        $this->is_open = false;
        $this->dispatch('chat-toggled', is_open: false);
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

        // Update session
        $this->chat_session->update([
            'last_message_at' => now(),
        ]);

        // Load relationships for event
        $message->load('user');

        // Send event via WebSocket
        broadcast(new MessageSent($message))->toOthers();

        // Add message to array
        $this->messages[] = $message->toArray();

        // Clear input field
        $this->new_message = '';

        // Dispatch event to scroll to last message
        $this->dispatch('message-sent');
    }

    /**
     * @param  array<string, mixed>  $message_data
     */
    #[On('message-received')]
    public function addMessage(array $message_data): void
    {
        // Add new message to array
        /** @var Message|null $message */
        $message = Message::with('user')->find($message_data['id']);

        if ($message) {
            // Check if message already exists
            $message_ids = array_column($this->messages, 'id');

            if (! in_array($message->id, $message_ids)) {
                $this->messages[] = $message->toArray();

                // If chat is open, mark message as read
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
        return view('livewire.chat.chat-panel');
    }
}
