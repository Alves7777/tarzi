<?php

namespace App\Filament\Resources\Users\Tables;

use App\Filament\Actions\PrintColumn;
use App\Filament\Actions\PrintTableAction;
use App\Filament\Exports\UserExporter;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ExportAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('app.users.fields.name'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('email')
                    ->label(__('app.users.fields.email'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('roles.name')
                    ->label(__('app.users.fields.roles'))
                    ->badge()
                    ->separator(','),
                TextColumn::make('email_verified_at')
                    ->label(__('app.users.fields.email_verified_at'))
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label(__('app.users.fields.created_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label(__('app.users.fields.updated_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('roles')
                    ->label(__('app.users.fields.roles'))
                    ->relationship('roles', 'name')
                    ->multiple()
                    ->preload(),
            ])
            ->headerActions([
                ExportAction::make()
                    ->label(__('table-output.actions.export'))
                    ->exporter(UserExporter::class),
                PrintTableAction::make()
                    ->title(__('app.users.plural'))
                    ->columns([
                        PrintColumn::make('name')
                            ->label(__('app.users.fields.name')),
                        PrintColumn::make('email')
                            ->label(__('app.users.fields.email')),
                        PrintColumn::make('roles.name')
                            ->label(__('app.users.fields.roles')),
                        PrintColumn::make('email_verified_at')
                            ->label(__('app.users.fields.email_verified_at')),
                        PrintColumn::make('created_at')
                            ->label(__('app.users.fields.created_at')),
                    ]),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
