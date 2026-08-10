<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label(__('app.users.fields.name'))
                    ->required()
                    ->maxLength(255),
                TextInput::make('email')
                    ->label(__('app.users.fields.email'))
                    ->email()
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true),
                TextInput::make('password')
                    ->label(__('app.users.fields.password'))
                    ->password()
                    ->revealable()
                    ->maxLength(255)
                    ->required(fn (string $operation): bool => $operation === 'create')
                    // An empty field on edit keeps the current password.
                    ->dehydrated(fn (?string $state): bool => filled($state))
                    ->helperText(fn (string $operation): ?string => $operation === 'edit'
                        ? __('app.users.password_helper')
                        : null),
                DateTimePicker::make('email_verified_at')
                    ->label(__('app.users.fields.email_verified_at')),
                Select::make('roles')
                    ->label(__('app.users.fields.roles'))
                    ->relationship('roles', 'name')
                    ->multiple()
                    ->preload()
                    ->searchable()
                    ->columnSpanFull(),
            ]);
    }
}
