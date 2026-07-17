<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToTenant;

class EmailTemplate extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'hotel_id',
        'type',
        'subject',
        'body',
        'variables',
    ];
}
