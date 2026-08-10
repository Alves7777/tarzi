<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

/**
 * The panel dashboard.
 *
 * Laid out on a twelve column grid so widgets can sit side by side on wide
 * screens and stack on narrow ones.
 */
class Dashboard extends BaseDashboard
{
    public function getTitle(): string
    {
        return __('filament-panels::pages/dashboard.title');
    }

    public function getSubheading(): ?string
    {
        return __('dashboard.greeting', [
            'greeting' => $this->timeOfDayGreeting(),
            'name' => Str::of(Auth::user()?->getAttribute('name') ?? '')->trim()->before(' '),
        ]);
    }

    /**
     * @return int|array<string, int>
     */
    public function getColumns(): int|array
    {
        return [
            'default' => 1,
            'sm' => 2,
            'lg' => 12,
        ];
    }

    private function timeOfDayGreeting(): string
    {
        return match (true) {
            Carbon::now()->hour < 12 => __('dashboard.greetings.morning'),
            Carbon::now()->hour < 18 => __('dashboard.greetings.afternoon'),
            default => __('dashboard.greetings.evening'),
        };
    }
}
