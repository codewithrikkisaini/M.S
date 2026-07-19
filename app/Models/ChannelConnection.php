<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToTenant;

class ChannelConnection extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'hotel_id',
        'channel_name',
        'status',
        'sync_status',
        'last_sync_at',
    ];

    protected $casts = [
        'last_sync_at' => 'datetime',
    ];
}
