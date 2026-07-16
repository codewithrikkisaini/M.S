<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToTenant;

class Housekeeping extends Model
{
    use BelongsToTenant;

    protected $table = 'housekeeping';
    protected $fillable = ['room_id', 'status', 'updated_by', 'notes', 'hotel_id'];

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
