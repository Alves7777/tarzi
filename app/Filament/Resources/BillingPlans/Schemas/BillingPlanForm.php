<?php

namespace App\Filament\Resources\BillingPlans\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class BillingPlanForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('slug')
                    ->required(),
                Textarea::make('description')
                    ->columnSpanFull(),
                TextInput::make('monthly_price_cents')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('ad_slot_price_cents')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('registration_fee_cents')
                    ->required()
                    ->numeric()
                    ->default(0),
                Toggle::make('is_active')
                    ->required(),
            ]);
    }
}
