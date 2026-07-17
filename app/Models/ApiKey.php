<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToTenant;

class ApiKey extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'hotel_id',
        'name',
        'key',
        'status',
        'last_used_at',
    ];

    protected $casts = [
        'last_used_at' => 'datetime',
    ];
}
