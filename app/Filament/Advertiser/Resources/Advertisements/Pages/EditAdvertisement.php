<?php

namespace App\Filament\Advertiser\Resources\Advertisements\Pages;

use App\Filament\Advertiser\Resources\Advertisements\AdvertisementResource;
use Filament\Resources\Pages\EditRecord;

class EditAdvertisement extends EditRecord
{
    protected static string $resource = AdvertisementResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        return \App\Filament\Resources\Advertisements\Pages\CreateAdvertisement::normalizeMediaPath(
            array_merge($data, ['youtube_url' => $this->form->getRawState()['youtube_url'] ?? null]),
        );
    }
}
