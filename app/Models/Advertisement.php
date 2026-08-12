<?php

namespace App\Models;

use App\Domain\Ads\Enums\AdMediaType;
use App\Domain\Ads\Enums\AdvertisementStatus;
use App\Support\AdvertisementMedia;
use App\Support\YoutubeUrl;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Advertisement extends Model
{
    protected $fillable = [
        'advertiser_id',
        'title',
        'media_type',
        'media_path',
        'click_url',
        'duration_seconds',
        'video_total_seconds',
        'is_active',
        'status',
        'submitted_at',
        'reviewed_at',
        'reviewed_by',
        'rejection_reason',
    ];

    protected function casts(): array
    {
        return [
            'media_type' => AdMediaType::class,
            'status' => AdvertisementStatus::class,
            'is_active' => 'boolean',
            'submitted_at' => 'datetime',
            'reviewed_at' => 'datetime',
        ];
    }

    public function advertiser(): BelongsTo
    {
        return $this->belongsTo(Advertiser::class);
    }

    public function placements(): HasMany
    {
        return $this->hasMany(AdPlacement::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function isPublishable(): bool
    {
        return $this->is_active && $this->status === AdvertisementStatus::Approved;
    }

    public function mediaUrl(): string
    {
        if ($this->media_type === AdMediaType::Youtube) {
            $videoId = YoutubeUrl::extractVideoId($this->media_path)
                ?? YoutubeUrl::extractVideoId($this->click_url);

            return $videoId ?? '';
        }

        if (blank($this->media_path)) {
            return '';
        }

        return AdvertisementMedia::url($this->media_path);
    }
}
