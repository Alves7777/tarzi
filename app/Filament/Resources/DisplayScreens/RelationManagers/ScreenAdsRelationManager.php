<?php

namespace App\Filament\Resources\DisplayScreens\RelationManagers;

use App\Domain\Ads\Enums\AdMediaType;
use App\Models\AdPlacement;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;

class ScreenAdsRelationManager extends RelationManager
{
    protected static string $relationship = 'placements';

    protected static ?string $title = null;

    public static function getTitle(\Illuminate\Database\Eloquent\Model $ownerRecord, string $pageClass): string
    {
        return __('signage.screen_ads.heading');
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->description(__('signage.screen_ads.description'))
            ->modifyQueryUsing(fn ($query) => $query->with('advertisement.advertiser')->orderBy('sort_order'))
            ->columns([
                ImageColumn::make('preview')
                    ->label('Prévia')
                    ->getStateUsing(fn (AdPlacement $record): ?string => $this->previewUrl($record))
                    ->checkFileExistence(false)
                    ->height(60),
                TextColumn::make('advertisement.title')
                    ->label('Anúncio')
                    ->searchable()
                    ->description(fn (AdPlacement $record): ?string => $record->advertisement?->advertiser?->name),
                TextColumn::make('placement')
                    ->label('Área')
                    ->formatStateUsing(fn ($state) => $state->label()),
                TextColumn::make('advertisement.status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state->label())
                    ->color(fn ($state) => $state->color()),
                TextColumn::make('advertisement.media_type')
                    ->label('Tipo')
                    ->badge()
                    ->formatStateUsing(fn (AdMediaType $state): string => $state->label()),
                TextColumn::make('sort_order')
                    ->label('Ordem'),
            ])
            ->paginated([10, 25, 50])
            ->defaultSort('sort_order')
            ->recordActions([])
            ->headerActions([]);
    }

    private function previewUrl(AdPlacement $record): ?string
    {
        $ad = $record->advertisement;

        if ($ad === null || $ad->media_type !== AdMediaType::Image) {
            return null;
        }

        return $ad->mediaUrl();
    }
}
