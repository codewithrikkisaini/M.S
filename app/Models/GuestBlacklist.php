<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToTenant;

class GuestBlacklist extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'hotel_id',
        'guest_id',
        'first_name',
        'last_name',
        'id_type',
        'id_number',
        'date_of_birth',
        'reason',
        'status',
        'blacklisted_by',
        'removed_by',
        'removed_at',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'removed_at' => 'datetime',
    ];

    public function guest()
    {
        return $this->belongsTo(Guest::class);
    }

    public function blacklister()
    {
        return $this->belongsTo(User::class, 'blacklisted_by');
    }

    public function remover()
    {
        return $this->belongsTo(User::class, 'removed_by');
    }

    public function documents()
    {
        return $this->hasMany(GuestBlacklistDocument::class, 'guest_blacklist_id');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeRemoved($query)
    {
        return $query->where('status', 'removed');
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }
}
