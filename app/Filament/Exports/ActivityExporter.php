<?php

namespace App\Filament\Exports;

use App\Support\SpreadsheetValue;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Illuminate\Support\Str;
use Spatie\Activitylog\Models\Activity;

class ActivityExporter extends Exporter
{
    protected static ?string $model = Activity::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('log_name')
                ->label(__('activitylog::tables.columns.log_name.label')),
            ExportColumn::make('event')
                ->label(__('activitylog::tables.columns.event.label')),
            ExportColumn::make('subject_type')
                ->label(__('activitylog::tables.columns.subject_type.label'))
                ->formatStateUsing(function (mixed $state, Activity $record): string {
                    if (blank($state)) {
                        return '';
                    }

                    return Str::of((string) $state)->afterLast('\\')->headline()." #{$record->subject_id}";
                }),
            ExportColumn::make('causer.name')
                ->label(__('activitylog::tables.columns.causer.label'))
                ->formatStateUsing(SpreadsheetValue::escape(...)),
            ExportColumn::make('properties')
                ->label(__('activitylog::tables.columns.properties.label'))
                ->formatStateUsing(fn (mixed $state): string => (string) json_encode($state, JSON_UNESCAPED_UNICODE)),
            ExportColumn::make('created_at')
                ->label(__('activitylog::tables.columns.created_at.label')),
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
