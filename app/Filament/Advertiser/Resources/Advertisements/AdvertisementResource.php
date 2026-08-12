<?php

namespace App\Filament\Advertiser\Resources\Advertisements;

use App\Filament\Advertiser\Resources\Advertisements\Pages\CreateAdvertisement;
use App\Filament\Advertiser\Resources\Advertisements\Pages\EditAdvertisement;
use App\Filament\Advertiser\Resources\Advertisements\Pages\ListAdvertisements;
use App\Filament\Resources\Advertisements\RelationManagers\PlacementsRelationManager;
use App\Filament\Resources\Advertisements\Schemas\AdvertisementForm;
use App\Filament\Resources\Advertisements\Tables\AdvertisementsTable;
use App\Models\Advertisement;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class AdvertisementResource extends Resource
{
    protected static ?string $model = Advertisement::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $navigationLabel = 'Meus anúncios';

    protected static ?string $modelLabel = 'Anúncio';

    protected static ?string $pluralModelLabel = 'Anúncios';

    public static function form(Schema $schema): Schema
    {
        return AdvertisementForm::configure($schema, forAdvertiser: true);
    }

    public static function table(Table $table): Table
    {
        return AdvertisementsTable::configure($table, forAdvertiser: true);
    }

    public static function getRelations(): array
    {
        return [
            PlacementsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAdvertisements::route('/'),
            'create' => CreateAdvertisement::route('/create'),
            'edit' => EditAdvertisement::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $advertiserId = auth()->user()?->advertiser_id;

        if ($advertiserId === null) {
            return $query->whereRaw('1 = 0');
        }

        return $query->where('advertiser_id', $advertiserId);
    }

    public static function canCreate(): bool
    {
        $user = auth()->user();

        return $user !== null && $user->isAdvertiser();
    }
}
