<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class DisplayScreen extends Model
{
    protected $fillable = [
        'uuid',
        'name',
        'location',
        'is_active',
        'carousel_seconds',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (DisplayScreen $screen): void {
            if (empty($screen->uuid)) {
                $screen->uuid = (string) Str::uuid();
            }
        });
    }

    public function placements(): HasMany
    {
        return $this->hasMany(AdPlacement::class);
    }
}
