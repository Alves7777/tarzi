<?php

namespace App\Domain\Ads\Enums;

enum AdPlacement: string
{
    case MainCarousel = 'main_carousel';
    case Sidebar1 = 'sidebar_1';
    case Sidebar2 = 'sidebar_2';
    case Sidebar3 = 'sidebar_3';
    case Footer1 = 'footer_1';
    case Footer2 = 'footer_2';

    public function label(): string
    {
        return (string) __("signage.slots.{$this->value}.label");
    }

    public function description(): string
    {
        return (string) __("signage.slots.{$this->value}.description");
    }

    /** @return array{width: int, height: int}|null */
    public function recommendedSize(ScreenFormat $format): ?array
    {
        $spec = config("signage.slots.{$this->value}.recommended.{$format->value}");

        if (! is_array($spec)) {
            return null;
        }

        return [
            'width' => (int) ($spec['width'] ?? 0),
            'height' => (int) ($spec['height'] ?? 0),
        ];
    }

    /** @return list<self> */
    public static function slotPlacements(): array
    {
        return [
            self::Sidebar1,
            self::Sidebar2,
            self::Sidebar3,
            self::Footer1,
            self::Footer2,
        ];
    }
}
