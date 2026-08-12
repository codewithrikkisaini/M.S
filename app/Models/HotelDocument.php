<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToTenant;
use App\Enums\DocumentStatus;

class HotelDocument extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'hotel_id',
        'document_type',
        'document_name',
        'description',
        'original_filename',
        'stored_filename',
        'disk',
        'storage_path',
        'mime_type',
        'file_size',
        'status',
        'version',
        'is_current',
        'uploaded_by',
        'reviewed_by',
        'uploaded_at',
        'reviewed_at',
        'rejection_reason',
        'admin_comment',
    ];

    protected $casts = [
        'status' => DocumentStatus::class,
        'is_current' => 'boolean',
        'version' => 'integer',
        'file_size' => 'integer',
        'uploaded_at' => 'datetime',
        'reviewed_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (HotelDocument $doc) {
            if (empty($doc->hotel_id) && auth()->check() && auth()->user()->hotel_id) {
                $doc->hotel_id = auth()->user()->hotel_id;
            }
            if (empty($doc->uploaded_by) && auth()->check()) {
                $doc->uploaded_by = auth()->id();
            }
            if (empty($doc->uploaded_at)) {
                $doc->uploaded_at = now();
            }
        });
    }

    // ─── Relationships ───────────────────────────────────────────────

    public function hotel()
    {
        return $this->belongsTo(Hotel::class);
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function auditLogs()
    {
        return $this->hasMany(DocumentAuditLog::class, 'hotel_document_id');
    }

    // ─── Scopes ──────────────────────────────────────────────────────

    public function scopeCurrent($query)
    {
        return $query->where('is_current', true);
    }

    public function scopeOfType($query, string $type)
    {
        return $query->where('document_type', $type);
    }

    public function scopePending($query)
    {
        return $query->where('status', DocumentStatus::Pending);
    }

    public function scopeForHotel($query, int $hotelId)
    {
        return $query->where('hotel_id', $hotelId);
    }

    // ─── Accessors ───────────────────────────────────────────────────

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

    public function getIsPdfAttribute(): bool
    {
        return $this->mime_type === 'application/pdf';
    }

    public function getIsImageAttribute(): bool
    {
        return in_array($this->mime_type, ['image/jpeg', 'image/png', 'image/jpg']);
    }

    public function getCanBeReplacedAttribute(): bool
    {
        return $this->status->canBeReplaced();
    }

    // ─── Helpers ─────────────────────────────────────────────────────

    public function getFullStoragePath(): string
    {
        return $this->storage_path . '/' . $this->stored_filename;
    }
}
