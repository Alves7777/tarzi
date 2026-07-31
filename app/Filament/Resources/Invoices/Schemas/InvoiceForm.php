<?php

namespace App\Filament\Resources\Invoices\Schemas;

use App\Domain\Billing\Enums\InvoiceStatus;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class InvoiceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('advertiser_id')
                    ->relationship('advertiser', 'name')
                    ->required(),
                Select::make('billing_plan_id')
                    ->relationship('billingPlan', 'name'),
                TextInput::make('reference')
                    ->required(),
                TextInput::make('description')
                    ->required(),
                TextInput::make('amount_cents')
                    ->required()
                    ->numeric(),
                Select::make('status')
                    ->options(InvoiceStatus::class)
                    ->default('pending')
                    ->required(),
                DatePicker::make('due_at'),
                DateTimePicker::make('paid_at'),
            ]);
    }
}
