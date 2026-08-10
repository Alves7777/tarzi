<?php

namespace App\Filament\Exports;

use App\Support\SpreadsheetValue;
use BezhanSalleh\FilamentShield\Support\Utils;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class RoleExporter extends Exporter
{
    public static function getColumns(): array
    {
        return [
            ExportColumn::make('name')
                ->label(__('filament-shield::filament-shield.column.name'))
                ->formatStateUsing(SpreadsheetValue::escape(...)),
            ExportColumn::make('guard_name')
                ->label(__('filament-shield::filament-shield.column.guard_name')),
            ExportColumn::make('permissions_count')
                ->label(__('filament-shield::filament-shield.column.permissions'))
                ->counts('permissions'),
            ExportColumn::make('updated_at')
                ->label(__('filament-shield::filament-shield.column.updated_at')),
        ];
    }

    public static function getModel(): string
    {
        return Utils::getRoleModel();
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
