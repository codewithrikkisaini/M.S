<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'hotel_id',
        'role_slug',
        'type',
        'title',
        'message',
        'link',
        'is_read',
    ];

    protected $casts = [
        'is_read' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function hotel()
    {
        return $this->belongsTo(Hotel::class);
    }

    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    public function scopeForUser($query, $user)
    {
        if (!$user) {
            return $query->whereRaw('1 = 0');
        }

        $userRoleSlug = $user->role ? $user->role->slug : null;

        return $query->where(function ($q) use ($user, $userRoleSlug) {
            // Targeted to specific user ID
            $q->where('user_id', $user->id);

            // Targeted by Role Slug (e.g. 'superadmin')
            if ($userRoleSlug) {
                $q->orWhere('role_slug', $userRoleSlug);
            }

            // Targeted by Hotel ID (for all hotel staff/admins)
            if ($user->hotel_id) {
                $q->orWhere(function ($subQ) use ($user) {
                    $subQ->where('hotel_id', $user->hotel_id)
                         ->whereNull('user_id')
                         ->whereNull('role_slug');
                });
            }
        });
    }
}
