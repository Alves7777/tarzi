<?php

namespace App\Filament\Resources\Advertisements\Schemas;

use App\Domain\Ads\Enums\AdMediaType;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;

class AdvertisementForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('advertiser_id')
                    ->relationship('advertiser', 'name')
                    ->required(),
                TextInput::make('title')
                    ->required(),
                Select::make('media_type')
                    ->options(AdMediaType::class)
                    ->default('image')
                    ->required(),
                TextInput::make('media_path')
                    ->label('URL da mídia')
                    ->helperText('Cole uma URL https ou use o upload abaixo')
                    ->required(),
                FileUpload::make('media_upload')
                    ->label('Upload de arquivo')
                    ->disk('public')
                    ->directory('advertisements')
                    ->visibility('public')
                    ->acceptedFileTypes(['image/*', 'video/mp4'])
                    ->maxSize(20480)
                    ->dehydrated(false)
                    ->afterStateUpdated(function (?string $state, Set $set): void {
                        if ($state !== null && $state !== '') {
                            $set('media_path', $state);
                        }
                    }),
                TextInput::make('click_url')
                    ->url(),
                TextInput::make('duration_seconds')
                    ->required()
                    ->numeric()
                    ->default(8),
                Toggle::make('is_active')
                    ->default(true)
                    ->required(),
            ]);
    }
}
