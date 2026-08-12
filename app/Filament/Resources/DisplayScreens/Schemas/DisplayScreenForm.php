<?php

namespace App\Filament\Resources\DisplayScreens\Schemas;

use App\Domain\Ads\Enums\ScreenFormat;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
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
                    ->label('Nome')
                    ->required(),
                TextInput::make('location')
                    ->label('Local'),
                Select::make('format')
                    ->label('Formato da tela')
                    ->options(collect(ScreenFormat::cases())->mapWithKeys(
                        fn (ScreenFormat $format) => [$format->value => $format->label()]
                    ))
                    ->default(ScreenFormat::Landscape169->value)
                    ->required()
                    ->live()
                    ->afterStateUpdated(function (Set $set, ?string $state): void {
                        $format = ScreenFormat::tryFrom((string) $state);

                        if ($format === null) {
                            return;
                        }

                        $set('width_px', $format->defaultWidth());
                        $set('height_px', $format->defaultHeight());
                    })
                    ->helperText(fn (Get $get): string => ScreenFormat::tryFrom((string) $get('format'))?->description() ?? ''),
                TextInput::make('width_px')
                    ->label('Largura (px)')
                    ->numeric()
                    ->required(),
                TextInput::make('height_px')
                    ->label('Altura (px)')
                    ->numeric()
                    ->required(),
                Toggle::make('is_active')
                    ->label('Ativa')
                    ->required(),
                Section::make('Rotação do carrossel')
                    ->description('Controla o contador de páginas (ex.: 1/6 · 7s) exibido no player.')
                    ->schema([
                        TextInput::make('carousel_seconds')
                            ->label(__('signage.playback.carousel_seconds'))
                            ->helperText(__('signage.playback.carousel_seconds_helper'))
                            ->required()
                            ->numeric()
                            ->default(8)
                            ->minValue(3)
                            ->maxValue(120),
                        TextInput::make('ads_before_video')
                            ->label(__('signage.playback.ads_before_video'))
                            ->helperText(__('signage.playback.ads_before_video_helper'))
                            ->required()
                            ->numeric()
                            ->default(3)
                            ->minValue(1)
                            ->maxValue(20),
                        TextInput::make('video_segment_seconds')
                            ->label(__('signage.playback.video_segment_seconds'))
                            ->helperText(__('signage.playback.video_segment_seconds_helper'))
                            ->required()
                            ->numeric()
                            ->default(30)
                            ->minValue(5)
                            ->maxValue(600),
                    ])
                    ->columns(3),
                Section::make('QR Code fixo')
                    ->description('Link genérico escaneado no display: PIX, Tune, site ou qualquer URL.')
                    ->schema([
                        TextInput::make('qr_url')
                            ->label('URL do QR Code')
                            ->placeholder('https://exemplo.com/pix ou https://tune.zeivoll.com.br/ride/uuid')
                            ->url()
                            ->maxLength(2048),
                        TextInput::make('qr_label')
                            ->label('Título')
                            ->placeholder('PIX / Zeivoll Tune / Promo')
                            ->maxLength(80),
                        TextInput::make('qr_caption')
                            ->label('Subtítulo')
                            ->placeholder('Escaneie para pagar / ouvir música')
                            ->maxLength(120),
                    ])
                    ->columns(1),
            ]);
    }
}
