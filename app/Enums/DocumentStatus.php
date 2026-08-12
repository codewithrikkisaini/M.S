<?php

namespace App\Enums;

enum DocumentStatus: string
{
    case Pending = 'pending';
    case UnderReview = 'under_review';
    case Approved = 'approved';
    case Rejected = 'rejected';
    case ReplacementRequired = 'replacement_required';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Pending Review',
            self::UnderReview => 'Under Review',
            self::Approved => 'Approved',
            self::Rejected => 'Rejected',
            self::ReplacementRequired => 'Replacement Required',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Pending => 'bg-amber-50 text-amber-700 border-amber-200',
            self::UnderReview => 'bg-blue-50 text-blue-700 border-blue-200',
            self::Approved => 'bg-emerald-50 text-emerald-700 border-emerald-200',
            self::Rejected => 'bg-rose-50 text-rose-700 border-rose-200',
            self::ReplacementRequired => 'bg-orange-50 text-orange-700 border-orange-200',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::Pending => 'fa-clock',
            self::UnderReview => 'fa-eye',
            self::Approved => 'fa-check-circle',
            self::Rejected => 'fa-times-circle',
            self::ReplacementRequired => 'fa-exclamation-triangle',
        };
    }

    public function canBeReplaced(): bool
    {
        return in_array($this, [self::Rejected, self::ReplacementRequired]);
    }
}
