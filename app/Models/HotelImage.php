<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HotelImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'hotel_id',
        'image_path',
        'is_primary',
        'title',
        'description',
    ];

    public function hotel()
    {
        return $this->belongsTo(Hotel::class);
    }

    public function getUrlAttribute(): string
    {
        if (empty($this->image_path)) {
            return 'https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&w=800&q=80';
        }

        $url = Room::formatUrl($this->image_path);
        return !empty($url) ? $url : 'https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&w=800&q=80';
    }
}
