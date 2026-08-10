<?php

namespace App\Filament\Exports;

use App\Models\Session;
use App\Support\SpreadsheetValue;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class SessionExporter extends Exporter
{
    protected static ?string $model = Session::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('user.name')
                ->label(__('sessions.columns.user'))
                ->formatStateUsing(SpreadsheetValue::escape(...)),
            ExportColumn::make('user.email')
                ->label(__('app.users.fields.email'))
                ->formatStateUsing(SpreadsheetValue::escape(...)),
            ExportColumn::make('user_agent')
                ->label(__('sessions.columns.device'))
                ->formatStateUsing(
                    fn (mixed $state, Session $record): mixed => SpreadsheetValue::escape($record->agent()->describe()),
                ),
            ExportColumn::make('ip_address')
                ->label(__('sessions.columns.ip')),
            ExportColumn::make('last_activity')
                ->label(__('sessions.columns.last_active'))
                ->formatStateUsing(fn (mixed $state, Session $record): string => $record->lastActiveAt()->toDateTimeString()),
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
