<?php

namespace App\Domain\Ads\Enums;

enum AdMediaType: string
{
    case Image = 'image';
    case Video = 'video';

    public function label(): string
    {
        return match ($this) {
            self::Image => 'Imagem',
            self::Video => 'Video',
        };
    }
}
