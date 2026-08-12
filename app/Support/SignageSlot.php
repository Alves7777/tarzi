<?php

namespace App\Support;

use App\Domain\Ads\Enums\AdPlacement;
use App\Domain\Ads\Enums\ScreenFormat;

final class SignageSlot
{
    public static function sizeHint(AdPlacement $slot, ScreenFormat $format): string
    {
        $size = $slot->recommendedSize($format);

        if ($size === null || $size['width'] === 0) {
            return __('signage.size_hint_generic');
        }

        return __('signage.size_hint', [
            'slot' => $slot->label(),
            'width' => $size['width'],
            'height' => $size['height'],
            'format' => $format->label(),
        ]);
    }

    /** @return array{width: int, height: int} */
    public static function screenSize(ScreenFormat $format, ?int $width = null, ?int $height = null): array
    {
        return [
            'width' => $width ?? $format->defaultWidth(),
            'height' => $height ?? $format->defaultHeight(),
        ];
    }
}
