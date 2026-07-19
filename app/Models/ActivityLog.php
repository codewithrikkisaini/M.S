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
        'action',
        'description',
        'ip_address',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
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
}
