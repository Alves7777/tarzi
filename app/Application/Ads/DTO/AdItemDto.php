<?php

namespace App\Application\Ads\DTO;

final readonly class AdItemDto
{
    public function __construct(
        public int $id,
        public string $title,
        public string $mediaType,
        public string $mediaUrl,
        public ?string $clickUrl,
        public int $durationSeconds,
        public ?int $videoStartSeconds = null,
        public ?int $videoSegmentSeconds = null,
    ) {}

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return array_filter([
            'id' => $this->id,
            'title' => $this->title,
            'media_type' => $this->mediaType,
            'media_url' => $this->mediaUrl,
            'click_url' => $this->clickUrl,
            'duration_seconds' => $this->durationSeconds,
            'video_start_seconds' => $this->videoStartSeconds,
            'video_segment_seconds' => $this->videoSegmentSeconds,
        ], fn (mixed $value): bool => $value !== null);
    }
}
