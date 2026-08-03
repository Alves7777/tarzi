<?php

namespace App\Filament\Resources\Advertisements\Pages;

use App\Domain\Ads\Enums\AdMediaType;
use App\Domain\Ads\Enums\AdPlacement as AdPlacementEnum;
use App\Filament\Resources\Advertisements\AdvertisementResource;
use App\Models\AdPlacement;
use App\Support\YoutubeUrl;
use Filament\Resources\Pages\CreateRecord;

class CreateAdvertisement extends CreateRecord
{
    protected static string $resource = AdvertisementResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $state = $this->form->getRawState();
        $data['youtube_url'] = $state['youtube_url'] ?? null;

        return self::normalizeMediaPath($data);
    }

    protected function afterCreate(): void
    {
        $state = $this->form->getRawState();

        $placement = $state['placement_slot'] ?? AdPlacementEnum::MainCarousel->value;
        if ($placement instanceof AdPlacementEnum) {
            $placement = $placement->value;
        }

        AdPlacement::query()->create([
            'advertisement_id' => $this->record->id,
            'display_screen_id' => $state['display_screen_id'] ?? null,
            'placement' => $placement,
            'sort_order' => (int) ($state['placement_sort'] ?? 0),
            'is_active' => true,
            'price_cents' => 0,
        ]);
    }

    /** @param array<string, mixed> $data */
    public static function normalizeMediaPath(array $data): array
    {
        $path = $data['media_path'] ?? null;

        if (is_array($path)) {
            $path = array_values(array_filter(
                $path,
                fn ($value) => filled($value) && ! str_starts_with((string) $value, '/tmp/')
            ))[0] ?? null;
        }

        if (is_string($path) && str_starts_with($path, '/tmp/')) {
            $path = null;
        }

        $data['media_path'] = $path;

        $mediaType = $data['media_type'] ?? null;

        if ($mediaType instanceof AdMediaType) {
            $mediaType = $mediaType->value;
        }

        $youtubeSource = $data['youtube_url'] ?? $data['click_url'] ?? null;

        if ($mediaType === AdMediaType::Youtube->value || $mediaType === AdMediaType::Video->value) {
            $videoId = YoutubeUrl::extractVideoId($youtubeSource);

            if ($videoId !== null && blank($data['media_path'])) {
                $data['media_type'] = AdMediaType::Youtube->value;
                $data['media_path'] = $videoId;
            }
        }

        unset($data['youtube_url']);

        return $data;
    }
}
