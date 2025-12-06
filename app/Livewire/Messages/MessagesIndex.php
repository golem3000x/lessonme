<?php

declare(strict_types=1);

namespace App\Livewire\Messages;

use App\Models\User;
use App\Models\UserMessage;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class MessagesIndex extends Component
{
    /** @var array<int, array<string, mixed>> */
    public array $conversations = [];

    public function mount(): void
    {
        $this->loadConversations();
    }

    public function loadConversations(): void
    {
        /** @var User $current_user */
        $current_user = Auth::user();

        $conversations = UserMessage::query()
            ->where('sender_id', '=', $current_user->id)
            ->orWhere('recipient_id', '=', $current_user->id)
            ->orderByDesc('created_at')
            ->get()
            ->groupBy(function (UserMessage $message) use ($current_user): int {
                return $message->sender_id === $current_user->id
                    ? $message->recipient_id
                    : $message->sender_id;
            })
            ->map(function (Collection $messages, int $user_id) use ($current_user): array {
                $last_message = $messages->first();
                $other_user = User::find($user_id);
                $unread = $messages->where('recipient_id', '=', $current_user->id)
                    ->whereNull('read_at')
                    ->count();

                return [
                    'user_id' => $user_id,
                    'user' => $other_user,
                    'last_message' => $last_message->content,
                    'last_message_at' => $last_message->created_at,
                    'unread_count' => $unread,
                ];
            })
            ->values()
            ->toArray();

        $this->conversations = $conversations;
    }

    public function render(): View
    {
        return view('livewire.messages.messages-index');
    }
}
