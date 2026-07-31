<?php

namespace App\Models;

use App\Domain\Ads\Enums\AdPlacement as AdPlacementEnum;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdPlacement extends Model
{
    protected $fillable = [
        'advertisement_id',
        'display_screen_id',
        'placement',
        'sort_order',
        'starts_at',
        'ends_at',
        'is_active',
        'price_cents',
    ];

    protected function casts(): array
    {
        return [
            'placement' => AdPlacementEnum::class,
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'is_active' => 'boolean',
        ];
    }

    public function advertisement(): BelongsTo
    {
        return $this->belongsTo(Advertisement::class);
    }

    public function displayScreen(): BelongsTo
    {
        return $this->belongsTo(DisplayScreen::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query
            ->where('is_active', true)
            ->where(function (Builder $builder): void {
                $builder->whereNull('starts_at')->orWhere('starts_at', '<=', now());
            })
            ->where(function (Builder $builder): void {
                $builder->whereNull('ends_at')->orWhere('ends_at', '>=', now());
            });
    }
}
