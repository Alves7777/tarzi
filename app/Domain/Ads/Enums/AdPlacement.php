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
        return match ($this) {
            self::MainCarousel => 'Carrossel principal',
            self::Sidebar1 => 'Lateral 1 (inferior)',
            self::Sidebar2 => 'Lateral 2 (meio)',
            self::Sidebar3 => 'Lateral 3 (superior)',
            self::Footer1 => 'Rodapé 1',
            self::Footer2 => 'Rodapé 2',
        };
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
