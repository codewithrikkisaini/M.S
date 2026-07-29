<?php

use Livewire\Component;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;

new class extends Component
{
    public function markAsRead($id): void
    {
        $user = Auth::user();
        if (!$user) return;

        $notification = Notification::forUser($user)->find($id);
        if ($notification) {
            $notification->update(['is_read' => true]);
        }
    }

    public function markAllAsRead(): void
    {
        $user = Auth::user();
        if (!$user) return;

        Notification::forUser($user)->unread()->update(['is_read' => true]);
    }

    public function render(): mixed
    {
        $user = Auth::user();
        $notifications = collect();
        $unreadCount = 0;

        if ($user) {
            $notifications = Notification::forUser($user)
                ->latest()
                ->take(10)
                ->get();

            $unreadCount = Notification::forUser($user)
                ->unread()
                ->count();
        }

        return $this->view(compact('notifications', 'unreadCount'));
    }
};
