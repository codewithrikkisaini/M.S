<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToTenant;

class DocumentAuditLog extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'hotel_document_id',
        'hotel_id',
        'user_id',
        'action',
        'old_status',
        'new_status',
        'comment',
        'metadata',
        'ip_address',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    // ─── Relationships ───────────────────────────────────────────────

    public function document()
    {
        return $this->belongsTo(HotelDocument::class, 'hotel_document_id');
    }

    public function hotel()
    {
        return $this->belongsTo(Hotel::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // ─── Static Helpers ──────────────────────────────────────────────

    public static function log(
        HotelDocument $document,
        string $action,
        ?string $oldStatus = null,
        ?string $newStatus = null,
        ?string $comment = null,
        ?array $metadata = null
    ): self {
        return static::create([
            'hotel_document_id' => $document->id,
            'hotel_id' => $document->hotel_id,
            'user_id' => auth()->id(),
            'action' => $action,
            'old_status' => $oldStatus,
            'new_status' => $newStatus,
            'comment' => $comment,
            'metadata' => $metadata,
            'ip_address' => request()->ip(),
        ]);
    }
}
