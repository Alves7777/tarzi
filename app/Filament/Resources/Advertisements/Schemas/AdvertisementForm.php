<?php

namespace App\Filament\Resources\Advertisements\Schemas;

use App\Domain\Ads\Enums\AdMediaType;
use App\Domain\Ads\Enums\AdPlacement as AdPlacementEnum;
use App\Domain\Ads\Enums\AdvertisementStatus;
use App\Domain\Ads\Enums\ScreenFormat;
use App\Models\Advertisement;
use App\Models\DisplayScreen;
use App\Support\SignageSlot;
use App\Support\AdvertisementMedia;
use App\Support\YoutubeUrl;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Illuminate\Support\HtmlString;

class AdvertisementForm
{
    public static function configure(Schema $schema, bool $forAdvertiser = false): Schema
    {
        return $schema
            ->components([
                Select::make('advertiser_id')
                    ->label('Anunciante')
                    ->relationship('advertiser', 'name')
                    ->required()
                    ->default(fn () => $forAdvertiser ? auth()->user()?->advertiser_id : null)
                    ->disabled($forAdvertiser)
                    ->dehydrated(true),
                TextInput::make('title')
                    ->label('Título')
                    ->required()
                    ->maxLength(255),
                Select::make('status')
                    ->label('Status')
                    ->options(AdvertisementStatus::class)
                    ->default(AdvertisementStatus::Approved)
                    ->required()
                    ->visible(! $forAdvertiser),
                Select::make('media_type')
                    ->label('Tipo de mídia')
                    ->options(AdMediaType::class)
                    ->default('image')
                    ->required()
                    ->live(),
                Placeholder::make('size_guidelines')
                    ->label('Tamanho ideal')
                    ->content(fn (Get $get): string => self::sizeGuidelines($get))
                    ->columnSpanFull(),
                Placeholder::make('media_preview')
                    ->label('Mídia atual')
                    ->content(function (?Advertisement $record): HtmlString|string {
                        if ($record === null) {
                            return 'Nenhum arquivo cadastrado.';
                        }

                        if ($record->media_type === AdMediaType::Youtube) {
                            $videoId = $record->mediaUrl();

                            return new HtmlString(
                                '<a href="https://www.youtube.com/watch?v='.e($videoId).'" target="_blank" rel="noopener" class="text-primary-500 underline">'
                                .'Abrir vídeo no YouTube ('.e($videoId).')'
                                .'</a>'
                            );
                        }

                        if (blank($record->media_path)) {
                            return 'Nenhum arquivo cadastrado.';
                        }

                        $url = e($record->mediaUrl());

                        if ($record->media_type === AdMediaType::Video) {
                            return new HtmlString(
                                '<a href="'.$url.'" target="_blank" rel="noopener" class="text-primary-500 underline">Abrir vídeo atual</a>'
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
                    ->helperText('Cole o link do vídeo. '.config('signage.media_guidelines.youtube'))
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
                    ->helperText(fn (Get $get): string => self::uploadHelper($get))
                    ->disk(AdvertisementMedia::disk())
                    ->directory(AdvertisementMedia::directory())
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
                    ->label('URL de clique')
                    ->helperText('Destino ao clicar no anúncio (opcional).')
                    ->url()
                    ->visible(fn (Get $get): bool => ! self::isMediaType($get, AdMediaType::Youtube)),
                TextInput::make('duration_seconds')
                    ->label('Duração na tela (s)')
                    ->helperText('Tempo de exibição de imagens. Vídeos usam o trecho configurado na tela.')
                    ->required()
                    ->numeric()
                    ->default(8),
                TextInput::make('video_total_seconds')
                    ->label('Duração total do vídeo (s)')
                    ->helperText('Necessário para segmentar vídeos longos (ex.: 50 anúncios + vídeo em partes).')
                    ->numeric()
                    ->minValue(1)
                    ->visible(fn (Get $get): bool => self::isMediaType($get, AdMediaType::Video, AdMediaType::Youtube)),
                Toggle::make('is_active')
                    ->label('Ativo no sistema')
                    ->default(true)
                    ->visible(! $forAdvertiser),
                Textarea::make('rejection_reason')
                    ->label('Motivo da rejeição')
                    ->rows(2)
                    ->disabled()
                    ->visible(fn (?Advertisement $record): bool => ! $forAdvertiser && $record?->status === AdvertisementStatus::Rejected),
                Section::make(__('signage.placements.heading'))
                    ->description(__('signage.placements.description'))
                    ->schema([
                        Select::make('placement_slot')
                            ->label('Área de exibição')
                            ->options(collect(AdPlacementEnum::cases())->mapWithKeys(
                                fn (AdPlacementEnum $slot) => [$slot->value => $slot->label().' — '.$slot->description()]
                            ))
                            ->default(AdPlacementEnum::MainCarousel->value)
                            ->required()
                            ->dehydrated(false)
                            ->native(false),
                        Select::make('display_screen_id')
                            ->label('Tela (opcional)')
                            ->options(fn (): array => DisplayScreen::query()
                                ->orderBy('name')
                                ->pluck('name', 'id')
                                ->all())
                            ->searchable()
                            ->helperText('Deixe vazio para exibir em todas as telas compatíveis.')
                            ->dehydrated(false),
                        TextInput::make('placement_sort')
                            ->label('Ordem na área')
                            ->numeric()
                            ->default(0)
                            ->helperText('0 = primeiro na fila.')
                            ->dehydrated(false),
                    ])
                    ->visible(fn (?Advertisement $record): bool => $record === null)
                    ->columns(1),
            ]);
    }

    private static function sizeGuidelines(Get $get): string
    {
        $slotValue = $get('placement_slot') ?? AdPlacementEnum::MainCarousel->value;
        $slot = AdPlacementEnum::tryFrom((string) $slotValue) ?? AdPlacementEnum::MainCarousel;

        $screenId = $get('display_screen_id');
        $screen = filled($screenId) ? DisplayScreen::query()->find($screenId) : null;
        $format = $screen?->format ?? ScreenFormat::Landscape169;

        $hint = SignageSlot::sizeHint($slot, $format);
        $mediaType = self::mediaTypeValue($get);
        $mediaHint = match ($mediaType) {
            AdMediaType::Video->value => config('signage.media_guidelines.video'),
            AdMediaType::Youtube->value => config('signage.media_guidelines.youtube'),
            default => config('signage.media_guidelines.image'),
        };

        return $hint.' '.$mediaHint;
    }

    private static function uploadHelper(Get $get): string
    {
        return self::sizeGuidelines($get).' Máx. 2 MB (limite do PHP).';
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
