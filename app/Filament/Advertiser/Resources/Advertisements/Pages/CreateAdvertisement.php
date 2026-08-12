<?php

namespace App\Filament\Advertiser\Resources\Advertisements\Pages;

use App\Domain\Ads\Enums\AdvertisementStatus;
use App\Filament\Advertiser\Resources\Advertisements\AdvertisementResource;
use App\Filament\Resources\Advertisements\Pages\CreateAdvertisement as BaseCreateAdvertisement;
use Filament\Resources\Pages\CreateRecord;

class CreateAdvertisement extends CreateRecord
{
    protected static string $resource = AdvertisementResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $state = $this->form->getRawState();
        $data['youtube_url'] = $state['youtube_url'] ?? null;
        $data['advertiser_id'] = auth()->user()?->advertiser_id;
        $data['status'] = AdvertisementStatus::Draft->value;
        $data['is_active'] = false;

        return BaseCreateAdvertisement::normalizeMediaPath($data);
    }

    protected function afterCreate(): void
    {
        BaseCreateAdvertisement::createInitialPlacement($this->record, $this->form->getRawState());
    }
}
