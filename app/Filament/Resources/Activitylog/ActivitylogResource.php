<?php

declare(strict_types=1);

namespace App\Filament\Resources\Activitylog;

use App\Filament\Actions\PrintColumn;
use App\Filament\Actions\PrintTableAction;
use App\Filament\Exports\ActivityExporter;
use App\Filament\Resources\Activitylog\Pages\ListActivitylog;
use App\Filament\Resources\Activitylog\Pages\ViewActivitylog;
use Filament\Actions\ExportAction;
use Filament\Resources\Pages\PageRegistration;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Rmsramos\Activitylog\ActivitylogPlugin;
use Rmsramos\Activitylog\Resources\Activitylog\ActivitylogResource as BaseActivitylogResource;
use Spatie\Activitylog\Models\Activity;

class ActivitylogResource extends BaseActivitylogResource
{
    /**
     * @return array<string, PageRegistration>
     */
    public static function getPages(): array
    {
        return [
            'index' => ListActivitylog::route('/'),
            'view' => ViewActivitylog::route('/{record}'),
        ];
    }

    public static function table(Table $table): Table
    {
        return parent::table($table)
            ->headerActions([
                ExportAction::make()
                    ->label(__('table-output.actions.export'))
                    ->exporter(ActivityExporter::class),
                PrintTableAction::make()
                    ->title(ActivitylogPlugin::get()->getPluralLabel())
                    ->columns([
                        PrintColumn::make('log_name')
                            ->label(__('activitylog::tables.columns.log_name.label')),
                        PrintColumn::make('event')
                            ->label(__('activitylog::tables.columns.event.label')),
                        PrintColumn::make('subject_type')
                            ->label(__('activitylog::tables.columns.subject_type.label'))
                            ->formatStateUsing(function (mixed $state, Activity $record): string {
                                if (blank($state)) {
                                    return '';
                                }

                                return Str::of((string) $state)->afterLast('\\')->headline()." #{$record->subject_id}";
                            }),
                        PrintColumn::make('causer.name')
                            ->label(__('activitylog::tables.columns.causer.label')),
                        PrintColumn::make('properties')
                            ->label(__('activitylog::tables.columns.properties.label'))
                            ->formatStateUsing(
                                fn (mixed $state): string => (string) json_encode($state, JSON_UNESCAPED_UNICODE),
                            ),
                        PrintColumn::make('created_at')
                            ->label(__('activitylog::tables.columns.created_at.label')),
                    ]),
            ]);
    }
}
