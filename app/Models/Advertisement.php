<?php

namespace App\Models;

use App\Domain\Ads\Enums\AdMediaType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

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
        if (str_starts_with($this->media_path, 'http://') || str_starts_with($this->media_path, 'https://')) {
            return $this->media_path;
        }

        $relative = Storage::disk('public')->url($this->media_path);

        if (str_starts_with($relative, 'http://') || str_starts_with($relative, 'https://')) {
            return $relative;
        }

        return rtrim(config('app.url'), '/').$relative;
    }
}
