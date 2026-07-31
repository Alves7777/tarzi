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
    ) {}

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'media_type' => $this->mediaType,
            'media_url' => $this->mediaUrl,
            'click_url' => $this->clickUrl,
            'duration_seconds' => $this->durationSeconds,
        ];
    }
}
