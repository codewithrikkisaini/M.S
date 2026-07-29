<?php

namespace App\Services;

use App\Models\Notification;

class NotificationService
{
    /**
     * Send notification to all Super Admins.
     */
    public static function notifySuperAdmins(string $title, string $message, ?string $link = null, string $type = 'hotel_registered'): Notification
    {
        return Notification::create([
            'role_slug' => 'superadmin',
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'link' => $link,
            'is_read' => false,
        ]);
    }

    /**
     * Send hotel-wide notification to all users under a specific hotel.
     */
    public static function notifyHotel(int $hotelId, string $title, string $message, ?string $link = null, string $type = 'room_booked'): Notification
    {
        return Notification::create([
            'hotel_id' => $hotelId,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'link' => $link,
            'is_read' => false,
        ]);
    }

    /**
     * Send notification to a specific user.
     */
    public static function notifyUser(int $userId, string $title, string $message, ?string $link = null, string $type = 'hotel_approved'): Notification
    {
        return Notification::create([
            'user_id' => $userId,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'link' => $link,
            'is_read' => false,
        ]);
    }
}
