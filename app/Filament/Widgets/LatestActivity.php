<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Str;
use Spatie\Activitylog\Models\Activity;

/**
 * O fim da trilha de auditoria, para uma olhada rápida no que acabou de acontecer.
 */
class LatestActivity extends TableWidget
{
    use HasWidgetShield;

    protected static ?int $sort = 3;

    protected int|string|array $columnSpan = [
        'default' => 'full',
        'lg' => 5,
    ];

    public static function canView(): bool
    {
        return parent::canView() && (Auth::user()?->can('ViewAny:Activity') ?? false);
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(fn (): Builder => Activity::query()->with(['causer', 'subject'])->latest('id'))
            ->heading(__('dashboard.latest_activity.heading'))
            ->paginated(false)
            ->queryStringIdentifier('latest-activity')
            ->recordUrl(null)
            ->columns([
                TextColumn::make('event')
                    ->label(__('dashboard.latest_activity.event'))
                    ->badge()
                    ->color(fn (?string $state): string => match ($state) {
                        'created', 'login' => 'success',
                        'deleted', 'login_failed', 'lockout', 'revoked', 'revoked_others' => 'danger',
                        'updated', 'role_attached', 'permission_attached' => 'warning',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (?string $state): string => $this->translateEvent($state)),
                TextColumn::make('description')
                    ->label(__('dashboard.latest_activity.description'))
                    ->formatStateUsing(fn (?string $state): string => $this->translateLog($state))
                    ->description(fn (Activity $record): ?string => $this->describeSubject($record))
                    ->wrap(),
                TextColumn::make('causer.name')
                    ->label(__('dashboard.latest_activity.causer'))
                    ->placeholder(__('dashboard.latest_activity.system')),
                TextColumn::make('created_at')
                    ->label(__('dashboard.latest_activity.when'))
                    ->since()
                    ->dateTimeTooltip(),
            ])
            ->emptyStateHeading(__('dashboard.latest_activity.empty'))
            ->defaultPaginationPageOption(10)
            ->modifyQueryUsing(fn (Builder $query): Builder => $query->limit(10));
    }

    private function translateEvent(?string $event): string
    {
        if (blank($event)) {
            return '—';
        }

        $key = "dashboard.events.{$event}";

        return Lang::has($key)
            ? __($key)
            : (string) Str::of($event)->replace('_', ' ')->title();
    }

    private function translateLog(?string $message): string
    {
        if (blank($message)) {
            return '—';
        }

        $key = "dashboard.logs.{$message}";

        return Lang::has($key)
            ? __($key)
            : $message;
    }

    private function describeSubject(Activity $record): ?string
    {
        if (! $record->subject instanceof Model) {
            return null;
        }

        $label = $record->subject->getAttribute('name')
            ?? $record->subject->getAttribute('email')
            ?? "#{$record->subject->getKey()}";

        $modelKey = 'dashboard.models.'.class_basename($record->subject);
        $modelLabel = Lang::has($modelKey)
            ? __($modelKey)
            : class_basename($record->subject);

        return "{$modelLabel}: {$label}";
    }
}
