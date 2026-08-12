<?php

namespace App\Domain\Ads\Enums;

enum ScreenFormat: string
{
    case Landscape169 = 'landscape_16_9';
    case Portrait916 = 'portrait_9_16';
    case Square11 = 'square_1_1';
    case Tablet43 = 'tablet_4_3';
    case ElevatorPortrait = 'elevator_portrait';

    public function label(): string
    {
        return match ($this) {
            self::Landscape169 => __('signage.screen_formats.landscape_16_9.label'),
            self::Portrait916 => __('signage.screen_formats.portrait_9_16.label'),
            self::Square11 => __('signage.screen_formats.square_1_1.label'),
            self::Tablet43 => __('signage.screen_formats.tablet_4_3.label'),
            self::ElevatorPortrait => __('signage.screen_formats.elevator_portrait.label'),
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::Landscape169 => __('signage.screen_formats.landscape_16_9.description'),
            self::Portrait916 => __('signage.screen_formats.portrait_9_16.description'),
            self::Square11 => __('signage.screen_formats.square_1_1.description'),
            self::Tablet43 => __('signage.screen_formats.tablet_4_3.description'),
            self::ElevatorPortrait => __('signage.screen_formats.elevator_portrait.description'),
        };
    }

    public function defaultWidth(): int
    {
        return match ($this) {
            self::Landscape169 => 1920,
            self::Portrait916 => 1080,
            self::Square11 => 1080,
            self::Tablet43 => 1024,
            self::ElevatorPortrait => 1080,
        };
    }

    public function defaultHeight(): int
    {
        return match ($this) {
            self::Landscape169 => 1080,
            self::Portrait916 => 1920,
            self::Square11 => 1080,
            self::Tablet43 => 768,
            self::ElevatorPortrait => 1920,
        };
    }
}
