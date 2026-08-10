<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToTenant;
use Illuminate\Support\Facades\Auth;

class ActivityLog extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'hotel_id',
        'user_id',
        'admin_id',
        'admin_name',
        'action',
        'previous_status',
        'new_status',
        'description',
        'notes',
        'ip_address',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function hotel()
    {
        return $this->belongsTo(Hotel::class);
    }

    public static function log(string $action, string $description): void
    {
        self::create([
            'user_id' => Auth::id(),
            'action' => $action,
            'description' => $description,
            'ip_address' => request()->ip(),
            'hotel_id' => Auth::check() ? Auth::user()->hotel_id : null,
        ]);
    }

    public static function logAdminAction(Hotel $hotel, string $action, ?string $prevStatus = null, ?string $newStatus = null, ?string $notes = null): void
    {
        $admin = Auth::user();
        self::create([
            'hotel_id' => $hotel->id,
            'user_id' => Auth::id(),
            'admin_id' => $admin?->id,
            'admin_name' => $admin?->name ?? 'System Administrator',
            'action' => $action,
            'previous_status' => $prevStatus,
            'new_status' => $newStatus,
            'description' => "Admin [{$action}] for Hotel {$hotel->name} ({$hotel->hotel_code})",
            'notes' => $notes,
            'ip_address' => request()->ip() ?? '127.0.0.1',
        ]);
    }
}
