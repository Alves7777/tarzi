<?php

namespace App\Models;

use App\Domain\Ads\Enums\AdMediaType;
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
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'media_type' => AdMediaType::class,
            'is_active' => 'boolean',
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

        if (str_starts_with($this->media_path, 'http://') || str_starts_with($this->media_path, 'https://')) {
            return $this->media_path;
        }

        $relative = ltrim($this->media_path, '/');

        return rtrim(config('app.url'), '/').'/api/v1/media/'.$relative;
    }
}
