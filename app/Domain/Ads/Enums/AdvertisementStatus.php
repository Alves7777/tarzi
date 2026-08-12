<?php

namespace App\Domain\Ads\Enums;

enum AdvertisementStatus: string
{
    case Draft = 'draft';
    case Pending = 'pending';
    case Approved = 'approved';
    case Rejected = 'rejected';

    public function label(): string
    {
        return match ($this) {
            self::Draft => __('signage.ad_status.draft'),
            self::Pending => __('signage.ad_status.pending'),
            self::Approved => __('signage.ad_status.approved'),
            self::Rejected => __('signage.ad_status.rejected'),
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Draft => 'gray',
            self::Pending => 'warning',
            self::Approved => 'success',
            self::Rejected => 'danger',
        };
    }

    public function isEditableByAdvertiser(): bool
    {
        return in_array($this, [self::Draft, self::Rejected], true);
    }
}
