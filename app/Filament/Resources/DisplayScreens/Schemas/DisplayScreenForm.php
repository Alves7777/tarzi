<?php

namespace App\Filament\Resources\DisplayScreens\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
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
                Section::make('QR Code fixo')
                    ->description('Link generico escaneado no display: PIX, Tune, site ou qualquer URL.')
                    ->schema([
                        TextInput::make('qr_url')
                            ->label('URL do QR Code')
                            ->placeholder('https://exemplo.com/pix ou https://tune.zeivoll.com.br/ride/uuid')
                            ->url()
                            ->maxLength(2048),
                        TextInput::make('qr_label')
                            ->label('Titulo')
                            ->placeholder('PIX / Zeivoll Tune / Promo')
                            ->maxLength(80),
                        TextInput::make('qr_caption')
                            ->label('Subtitulo')
                            ->placeholder('Escaneie para pagar / ouvir musica')
                            ->maxLength(120),
                    ])
                    ->columns(1),
            ]);
    }
}
