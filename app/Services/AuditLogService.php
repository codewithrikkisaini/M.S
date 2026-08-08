<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\Hotel;

class AuditLogService
{
    /**
     * Record administrative action
     */
    public function log(Hotel $hotel, string $action, ?string $prevStatus = null, ?string $newStatus = null, ?string $notes = null): void
    {
        ActivityLog::logAdminAction($hotel, $action, $prevStatus, $newStatus, $notes);
    }
}
