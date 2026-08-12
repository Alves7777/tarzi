<?php

namespace App\Filament\Advertiser\Resources\Advertisements\Pages;

use App\Filament\Advertiser\Resources\Advertisements\AdvertisementResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAdvertisements extends ListRecords
{
    protected static string $resource = AdvertisementResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Novo anúncio'),
        ];
    }
}
