<?php

namespace App\Filament\Resources\Advertisers\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class AdvertiserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('email')
                    ->label('Endereço de e-mail')
                    ->email()
                    ->required(),
                TextInput::make('phone')
                    ->tel(),
                TextInput::make('document'),
                Toggle::make('is_active')
                    ->required(),
                TextInput::make('registration_fee_cents')
                    ->required()
                    ->numeric()
                    ->default(0),
            ]);
    }
}
