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
        'case_number',
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
        'release_reason',
        'release_notes',
        'released_by',
        'released_at',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'removed_at' => 'datetime',
        'released_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (GuestBlacklist $blacklist) {
            if (empty($blacklist->case_number)) {
                $blacklist->case_number = static::generateCaseNumber($blacklist->hotel_id);
            }
        });
    }

    public static function generateCaseNumber(?int $hotelId = null): string
    {
        $year = date('Y');
        $prefix = 'BL-' . $year . '-';
        $lastRecord = static::where('case_number', 'like', $prefix . '%')->latest('id')->first();
        if ($lastRecord && preg_match('/(\d+)$/', $lastRecord->case_number, $matches)) {
            $nextNumber = (int) $matches[1] + 1;
        } else {
            $nextNumber = 1;
        }
        return $prefix . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
    }

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

    public function releaser()
    {
        return $this->belongsTo(User::class, 'released_by');
    }

    public function documents()
    {
        return $this->hasMany(GuestBlacklistDocument::class, 'guest_blacklist_id');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeReleased($query)
    {
        return $query->where('status', 'released');
    }

    public function scopeRemoved($query)
    {
        return $query->where('status', 'released');
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isReleased(): bool
    {
        return $this->status === 'released';
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'active' => 'Active',
            'released' => 'Released',
            'removed' => 'Released',
            default => ucfirst($this->status),
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'active' => 'red',
            'released' => 'green',
            'removed' => 'green',
            default => 'slate',
        };
    }
}
