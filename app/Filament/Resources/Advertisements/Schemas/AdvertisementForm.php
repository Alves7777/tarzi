<?php

namespace App\Filament\Resources\Advertisements\Schemas;

use App\Domain\Ads\Enums\AdMediaType;
use App\Domain\Ads\Enums\AdPlacement as AdPlacementEnum;
use App\Models\Advertisement;
use App\Models\DisplayScreen;
use App\Support\YoutubeUrl;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Illuminate\Support\HtmlString;

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
                    ->required()
                    ->live(),
                Placeholder::make('media_preview')
                    ->label('Midia atual')
                    ->content(function (?Advertisement $record): HtmlString|string {
                        if ($record === null) {
                            return 'Nenhum arquivo cadastrado.';
                        }

                        if ($record->media_type === AdMediaType::Youtube) {
                            $videoId = $record->mediaUrl();

                            return new HtmlString(
                                '<a href="https://www.youtube.com/watch?v='.e($videoId).'" target="_blank" rel="noopener" class="text-primary-500 underline">'
                                .'Abrir video no YouTube ('.e($videoId).')'
                                .'</a>'
                            );
                        }

                        if (blank($record->media_path)) {
                            return 'Nenhum arquivo cadastrado.';
                        }

                        $url = e($record->mediaUrl());

                        if ($record->media_type === AdMediaType::Video) {
                            return new HtmlString(
                                '<a href="'.$url.'" target="_blank" rel="noopener" class="text-primary-500 underline">Abrir video atual</a>'
                            );
                        }

                        return new HtmlString(
                            '<a href="'.$url.'" target="_blank" rel="noopener">'
                            .'<img src="'.$url.'" alt="Preview" style="max-height:140px;border-radius:8px;" />'
                            .'</a>'
                        );
                    })
                    ->visible(fn (?Advertisement $record): bool => $record !== null && (
                        filled($record->media_path) || $record->media_type === AdMediaType::Youtube
                    )),
                TextInput::make('youtube_url')
                    ->label('Link do YouTube')
                    ->placeholder('https://youtu.be/... ou https://www.youtube.com/watch?v=...')
                    ->helperText('Cole o link do video. Upload de arquivo nao e necessario para YouTube.')
                    ->url()
                    ->visible(fn (Get $get): bool => self::isMediaType($get, AdMediaType::Video, AdMediaType::Youtube))
                    ->required(fn (Get $get, ?Advertisement $record): bool => self::requiresYoutubeUrl($get, $record))
                    ->dehydrated(false)
                    ->afterStateHydrated(function (TextInput $component, ?Advertisement $record): void {
                        if ($record?->media_type !== AdMediaType::Youtube) {
                            return;
                        }

                        $videoId = $record->mediaUrl();

                        if ($videoId !== '') {
                            $component->state('https://youtu.be/'.$videoId);
                        }
                    }),
                FileUpload::make('media_path')
                    ->label('Upload de arquivo')
                    ->helperText('Carrossel: 1920x1080 px · Lateral: 1080x500 px · JPG/PNG ou MP4 · Max 2 MB por limite do PHP.')
                    ->disk('public')
                    ->directory('advertisements')
                    ->visibility('public')
                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp', 'video/mp4'])
                    ->maxSize(2048)
                    ->imagePreviewHeight('120')
                    ->downloadable()
                    ->openable()
                    ->fetchFileInformation(false)
                    ->deletable(true)
                    ->visible(fn (Get $get): bool => ! self::isMediaType($get, AdMediaType::Youtube))
                    ->dehydrated(fn (Get $get): bool => ! self::isMediaType($get, AdMediaType::Youtube))
                    ->required(fn (?Advertisement $record, Get $get): bool => self::requiresFileUpload($get, $record)),
                TextInput::make('click_url')
                    ->label('Click url')
                    ->helperText('Destino ao clicar no anuncio (opcional).')
                    ->url()
                    ->visible(fn (Get $get): bool => ! self::isMediaType($get, AdMediaType::Youtube)),
                TextInput::make('duration_seconds')
                    ->required()
                    ->numeric()
                    ->default(8),
                Toggle::make('is_active')
                    ->default(true)
                    ->required(),
                Select::make('placement_slot')
                    ->label('Onde exibir')
                    ->options(AdPlacementEnum::class)
                    ->default(AdPlacementEnum::MainCarousel->value)
                    ->required()
                    ->dehydrated(false)
                    ->visible(fn (?Advertisement $record): bool => $record === null),
                Select::make('display_screen_id')
                    ->label('Tela (opcional)')
                    ->options(fn (): array => DisplayScreen::query()
                        ->orderBy('name')
                        ->pluck('name', 'id')
                        ->all())
                    ->searchable()
                    ->helperText('Deixe vazio para exibir em todas as telas.')
                    ->dehydrated(false)
                    ->visible(fn (?Advertisement $record): bool => $record === null),
                TextInput::make('placement_sort')
                    ->label('Ordem no slot')
                    ->numeric()
                    ->default(0)
                    ->helperText('0 = primeiro. Videos/YouTube costumam ficar em 0.')
                    ->dehydrated(false)
                    ->visible(fn (?Advertisement $record): bool => $record === null),
            ]);
    }

    private static function mediaTypeValue(Get $get): ?string
    {
        $type = $get('media_type');

        if ($type instanceof AdMediaType) {
            return $type->value;
        }

        return is_string($type) ? $type : null;
    }

    private static function isMediaType(Get $get, AdMediaType ...$types): bool
    {
        $current = self::mediaTypeValue($get);

        foreach ($types as $type) {
            if ($current === $type->value) {
                return true;
            }
        }

        return false;
    }

    private static function requiresYoutubeUrl(Get $get, ?Advertisement $record): bool
    {
        if (self::isMediaType($get, AdMediaType::Youtube)) {
            return blank($get('youtube_url'))
                && ! YoutubeUrl::isYoutube($get('click_url'))
                && $record?->media_type !== AdMediaType::Youtube;
        }

        if (self::isMediaType($get, AdMediaType::Video)) {
            return blank($get('media_path'))
                && blank($record?->media_path)
                && ! YoutubeUrl::isYoutube($get('youtube_url'))
                && ! YoutubeUrl::isYoutube($get('click_url'));
        }

        return false;
    }

    private static function requiresFileUpload(Get $get, ?Advertisement $record): bool
    {
        if (self::isMediaType($get, AdMediaType::Youtube)) {
            return false;
        }

        if (filled($get('media_path')) || filled($record?->media_path)) {
            return false;
        }

        if (self::isMediaType($get, AdMediaType::Video) && (
            YoutubeUrl::isYoutube($get('youtube_url')) || YoutubeUrl::isYoutube($get('click_url'))
        )) {
            return false;
        }

        return true;
    }
}
