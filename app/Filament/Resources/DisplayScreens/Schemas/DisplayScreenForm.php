<?php

namespace App\Filament\Resources\DisplayScreens\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class DisplayScreenForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('uuid')
                    ->label('UUID')
                    ->required(),
                TextInput::make('name')
                    ->required(),
                TextInput::make('location'),
                Toggle::make('is_active')
                    ->required(),
                TextInput::make('carousel_seconds')
                    ->required()
                    ->numeric()
                    ->default(8),
            ]);
    }
}
