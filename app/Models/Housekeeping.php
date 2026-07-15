<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Housekeeping extends Model
{
    protected $table = 'housekeeping';
    protected $fillable = ['room_id', 'status', 'updated_by', 'notes'];

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
