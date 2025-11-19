<?php

declare(strict_types=1);

namespace App\Livewire;

use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class NotificationBell extends Component
{
    public int $unread_count = 0;

    public bool $is_open = false;

    public function mount(): void
    {
        $this->updateUnreadCount();
    }

    public function toggleDropdown(): void
    {
        $this->is_open = ! $this->is_open;
        Log::info('Toggle dropdown', ['is_open' => $this->is_open]);
    }

    public function closeDropdown(): void
    {
        $this->is_open = false;
    }

    protected function updateUnreadCount(): void
    {
        $user = Auth::user();
        if (! $user) {
            $this->unread_count = 0;

            return;
        }

        $this->unread_count = $user->unreadNotifications()->count();
    }

    public function markAsRead(string $notification_id): void
    {
        Log::info('NotificationBell::markAsRead called', ['notification_id' => $notification_id]);

        $user = Auth::user();
        if (! $user) {
            return;
        }

        $notification = $user
            ->notifications()
            ->where('id', '=', $notification_id)
            ->first();

        if ($notification && $notification->unread()) {
            $notification->markAsRead();
            $this->updateUnreadCount();
            Log::info('NotificationBell::markAsRead success');
        } else {
            Log::warning('NotificationBell::markAsRead notification not found or already read');
        }
    }

    public function markAllAsRead(): void
    {
        Log::info('NotificationBell::markAllAsRead called');

        $user = Auth::user();
        if (! $user) {
            return;
        }

        $count = $user->unreadNotifications()->count();
        $user->unreadNotifications()->update(['read_at' => now()]);
        $this->updateUnreadCount();
        Log::info('NotificationBell::markAllAsRead success', ['count' => $count]);
    }

    public function deleteNotification(string $notification_id): void
    {
        Log::info('NotificationBell::deleteNotification called', ['notification_id' => $notification_id]);

        $user = Auth::user();
        if (! $user) {
            return;
        }

        $deleted = $user
            ->notifications()
            ->where('id', '=', $notification_id)
            ->delete();

        $this->updateUnreadCount();
        Log::info('NotificationBell::deleteNotification success', ['deleted' => $deleted]);
    }

    public function render(): Factory|View
    {
        $user = Auth::user();
        if (! $user) {
            return view('livewire.notification-bell', [
                'notifications' => collect(),
            ]);
        }

        $notifications = $user
            ->notifications()
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get();

        return view('livewire.notification-bell', [
            'notifications' => $notifications,
        ]);
    }
}
