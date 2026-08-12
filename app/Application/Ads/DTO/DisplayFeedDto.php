<?php

namespace App\Application\Ads\DTO;

final readonly class DisplayFeedDto
{
    /** @param list<AdItemDto> $mainCarousel */
    public function __construct(
        public string $screenUuid,
        public string $screenName,
        public int $carouselSeconds,
        public array $mainCarousel,
        public ?AdItemDto $sidebar1,
        public ?AdItemDto $sidebar2,
        public ?AdItemDto $sidebar3,
        public ?AdItemDto $footer1,
        public ?AdItemDto $footer2,
        public string $currentTime,
        public string $timezone,
        public ?float $usdBrl,
        public ?float $eurBrl,
        public ?string $qrUrl,
        public ?string $qrLabel,
        public ?string $qrCaption,
        public int $adsBeforeVideo,
        public int $videoSegmentSeconds,
        public string $screenFormat,
        public int $screenWidthPx,
        public int $screenHeightPx,
    ) {}

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return [
            'screen' => [
                'uuid' => $this->screenUuid,
                'name' => $this->screenName,
                'carousel_seconds' => $this->carouselSeconds,
                'format' => $this->screenFormat,
                'width_px' => $this->screenWidthPx,
                'height_px' => $this->screenHeightPx,
            ],
            'playback' => [
                'ads_before_video' => $this->adsBeforeVideo,
                'video_segment_seconds' => $this->videoSegmentSeconds,
                'carousel_seconds' => $this->carouselSeconds,
            ],
            'layout' => [
                'main_carousel' => array_map(fn (AdItemDto $item) => $item->toArray(), $this->mainCarousel),
                'sidebar_1' => $this->sidebar1?->toArray(),
                'sidebar_2' => $this->sidebar2?->toArray(),
                'sidebar_3' => $this->sidebar3?->toArray(),
                'footer_1' => $this->footer1?->toArray(),
                'footer_2' => $this->footer2?->toArray(),
            ],
            'widgets' => [
                'current_time' => $this->currentTime,
                'timezone' => $this->timezone,
                'usd_brl' => $this->usdBrl,
                'eur_brl' => $this->eurBrl,
                'qr_url' => $this->qrUrl,
                'qr_label' => $this->qrLabel,
                'qr_caption' => $this->qrCaption,
            ],
        ];
    }
}
