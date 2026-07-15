<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MaintenanceTicket extends Model
{
    protected $table = 'maintenance_tickets';

    protected $fillable = [
        'room_id',
        'issue',
        'priority',
        'assigned_to',
        'status',
        'notes',
        'reported_by'
    ];

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function assignee()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function reporter()
    {
        return $this->belongsTo(User::class, 'reported_by');
    }
}
