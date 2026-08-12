<?php

namespace App\Models;

use App\Domain\Ads\Enums\ScreenFormat;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class DisplayScreen extends Model
{
    protected $fillable = [
        'uuid',
        'name',
        'location',
        'format',
        'width_px',
        'height_px',
        'is_active',
        'carousel_seconds',
        'ads_before_video',
        'video_segment_seconds',
        'qr_url',
        'qr_label',
        'qr_caption',
    ];

    protected function casts(): array
    {
        return [
            'format' => ScreenFormat::class,
            'is_active' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (DisplayScreen $screen): void {
            if (empty($screen->uuid)) {
                $screen->uuid = (string) Str::uuid();
            }

            if ($screen->format instanceof ScreenFormat) {
                $screen->width_px ??= $screen->format->defaultWidth();
                $screen->height_px ??= $screen->format->defaultHeight();
            }
        });
    }

    public function placements(): HasMany
    {
        return $this->hasMany(AdPlacement::class);
    }
}
