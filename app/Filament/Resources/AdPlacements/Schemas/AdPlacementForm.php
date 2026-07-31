<?php

namespace App\Filament\Resources\AdPlacements\Schemas;

use App\Domain\Ads\Enums\AdPlacement;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class AdPlacementForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('advertisement_id')
                    ->relationship('advertisement', 'title')
                    ->required(),
                Select::make('display_screen_id')
                    ->relationship('displayScreen', 'name'),
                Select::make('placement')
                    ->options(AdPlacement::class)
                    ->required(),
                TextInput::make('sort_order')
                    ->required()
                    ->numeric()
                    ->default(0),
                DateTimePicker::make('starts_at'),
                DateTimePicker::make('ends_at'),
                Toggle::make('is_active')
                    ->required(),
                TextInput::make('price_cents')
                    ->required()
                    ->numeric()
                    ->default(0),
            ]);
    }
}
