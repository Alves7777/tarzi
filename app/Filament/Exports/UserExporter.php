<?php

namespace App\Filament\Exports;

use App\Models\User;
use App\Support\SpreadsheetValue;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class UserExporter extends Exporter
{
    protected static ?string $model = User::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('name')
                ->label(__('app.users.fields.name'))
                ->formatStateUsing(SpreadsheetValue::escape(...)),
            ExportColumn::make('email')
                ->label(__('app.users.fields.email'))
                ->formatStateUsing(SpreadsheetValue::escape(...)),
            ExportColumn::make('roles.name')
                ->label(__('app.users.fields.roles')),
            ExportColumn::make('email_verified_at')
                ->label(__('app.users.fields.email_verified_at')),
            ExportColumn::make('created_at')
                ->label(__('app.users.fields.created_at')),
            ExportColumn::make('updated_at')
                ->label(__('app.users.fields.updated_at')),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        return trans_choice(
            'table-output.notifications.completed',
            $export->successful_rows,
            [
                'successful' => $export->successful_rows,
                'failed' => $export->getFailedRowsCount(),
            ],
        );
    }
}
