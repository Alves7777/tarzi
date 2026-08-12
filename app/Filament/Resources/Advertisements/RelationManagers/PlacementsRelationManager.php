<?php

namespace App\Filament\Resources\Advertisements\RelationManagers;

use App\Domain\Ads\Enums\AdPlacement;
use App\Models\AdPlacement as AdPlacementModel;
use App\Models\DisplayScreen;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PlacementsRelationManager extends RelationManager
{
    protected static string $relationship = 'placements';

    protected static ?string $title = null;

    public static function getTitle(\Illuminate\Database\Eloquent\Model $ownerRecord, string $pageClass): string
    {
        return __('signage.placements.heading');
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('display_screen_id')
                ->label('Tela')
                ->options(fn (): array => DisplayScreen::query()->orderBy('name')->pluck('name', 'id')->all())
                ->searchable()
                ->placeholder('Todas as telas')
                ->helperText('Deixe vazio para exibir em qualquer tela com este formato.'),
            Select::make('placement')
                ->label('Área de exibição')
                ->options(collect(AdPlacement::cases())->mapWithKeys(
                    fn (AdPlacement $slot) => [$slot->value => $slot->label().' — '.$slot->description()]
                ))
                ->required()
                ->native(false),
            TextInput::make('sort_order')
                ->label('Ordem')
                ->numeric()
                ->default(0)
                ->helperText('0 = primeiro na fila da área.'),
            DateTimePicker::make('starts_at')
                ->label('Início da campanha'),
            DateTimePicker::make('ends_at')
                ->label('Fim da campanha'),
            Toggle::make('is_active')
                ->label('Ativo')
                ->default(true),
            TextInput::make('price_cents')
                ->label('Valor (centavos)')
                ->numeric()
                ->default(0)
                ->visible(fn (): bool => ! auth()->user()?->isAdvertiser()),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->description(__('signage.placements.description'))
            ->columns([
                TextColumn::make('placement')
                    ->label('Área')
                    ->formatStateUsing(fn (AdPlacement $state): string => $state->label())
                    ->description(fn (AdPlacementModel $record): string => $record->placement->description()),
                TextColumn::make('displayScreen.name')
                    ->label('Tela')
                    ->placeholder('Todas'),
                TextColumn::make('sort_order')
                    ->label('Ordem')
                    ->sortable(),
                IconColumn::make('is_active')
                    ->label('Ativo')
                    ->boolean(),
                TextColumn::make('starts_at')
                    ->label('Início')
                    ->dateTime()
                    ->placeholder('—'),
                TextColumn::make('ends_at')
                    ->label('Fim')
                    ->dateTime()
                    ->placeholder('—'),
            ])
            ->headerActions([
                CreateAction::make()
                    ->icon(Heroicon::OutlinedPlus),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->defaultSort('sort_order');
    }
}
