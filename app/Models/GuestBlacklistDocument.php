<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToTenant;

class GuestBlacklistDocument extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'guest_blacklist_id',
        'hotel_id',
        'original_filename',
        'stored_filename',
        'disk',
        'storage_path',
        'mime_type',
        'file_size',
        'uploaded_by',
    ];

    protected $casts = [
        'file_size' => 'integer',
    ];

    protected static function booted(): void
    {
        static::creating(function (GuestBlacklistDocument $doc) {
            if (empty($doc->hotel_id) && auth()->check() && auth()->user()->hotel_id) {
                $doc->hotel_id = auth()->user()->hotel_id;
            }
            if (empty($doc->uploaded_by) && auth()->check()) {
                $doc->uploaded_by = auth()->id();
            }
        });
    }

    public function blacklist()
    {
        return $this->belongsTo(GuestBlacklist::class, 'guest_blacklist_id');
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function getFullStoragePath(): string
    {
        return $this->storage_path . '/' . $this->stored_filename;
    }

    public function getFileSizeFormattedAttribute(): string
    {
        $bytes = $this->file_size;
        if ($bytes >= 1048576) {
            return round($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return round($bytes / 1024, 2) . ' KB';
        }
        return $bytes . ' B';
    }
}
