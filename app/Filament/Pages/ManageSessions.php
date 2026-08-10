<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use App\Filament\Actions\PrintColumn;
use App\Filament\Actions\PrintTableAction;
use App\Filament\Exports\SessionExporter;
use App\Models\Session;
use App\Support\SessionRegistry;
use BackedEnum;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\ExportAction;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;
use UnitEnum;

/**
 * Every signed-in device across the application, with the ability to sign them out.
 *
 * Only meaningful with the database session driver; the page hides itself
 * otherwise rather than showing an empty table that can never fill up.
 */
class ManageSessions extends Page implements HasTable
{
    use HasPageShield {
        canAccess as canAccessThroughShield;
    }
    use InteractsWithTable;

    /**
     * Sessions older than this are treated as signed out for the "online" filter.
     */
    private const ONLINE_THRESHOLD_MINUTES = 5;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedSignal;

    protected static ?int $navigationSort = 3;

    protected string $view = 'filament.pages.manage-sessions';

    /**
     * The Shield permission check still applies; this only adds the driver
     * requirement on top of it.
     */
    public static function canAccess(): bool
    {
        return SessionRegistry::isSupported() && static::canAccessThroughShield();
    }

    public static function getNavigationLabel(): string
    {
        return __('sessions.title');
    }

    public static function getNavigationGroup(): string|UnitEnum|null
    {
        return __('app.navigation.administration');
    }

    public function getTitle(): string
    {
        return __('sessions.title');
    }

    public function getSubheading(): ?string
    {
        return __('sessions.subheading');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(fn (): Builder => Session::query()->with('user'))
            ->defaultSort('last_activity', 'desc')
            ->columns([
                TextColumn::make('user.name')
                    ->label(__('sessions.columns.user'))
                    ->description(fn (Session $record): string => $record->user?->email ?? __('sessions.guest'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('user_agent')
                    ->label(__('sessions.columns.device'))
                    ->state(fn (Session $record): string => $record->agent()->describe())
                    ->badge()
                    ->color('gray'),
                TextColumn::make('ip_address')
                    ->label(__('sessions.columns.ip'))
                    ->searchable()
                    ->placeholder(__('sessions.unknown_ip')),
                TextColumn::make('last_activity')
                    ->label(__('sessions.columns.last_active'))
                    ->state(fn (Session $record): Carbon => Carbon::createFromTimestamp($record->last_activity))
                    ->since()
                    ->dateTimeTooltip()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('user_id')
                    ->label(__('sessions.filters.user'))
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload(),
                Filter::make('online')
                    ->label(__('sessions.filters.online'))
                    ->query(fn (Builder $query): Builder => $query->activeWithin(self::ONLINE_THRESHOLD_MINUTES)),
                Filter::make('guests')
                    ->label(__('sessions.filters.guests'))
                    ->query(fn (Builder $query): Builder => $query->whereNull('user_id')),
            ])
            ->headerActions([
                ExportAction::make()
                    ->label(__('table-output.actions.export'))
                    ->exporter(SessionExporter::class),
                PrintTableAction::make()
                    ->title(__('sessions.title'))
                    ->columns([
                        PrintColumn::make('user.name')
                            ->label(__('sessions.columns.user')),
                        PrintColumn::make('user.email')
                            ->label(__('app.users.fields.email')),
                        PrintColumn::make('user_agent')
                            ->label(__('sessions.columns.device'))
                            ->formatStateUsing(fn (mixed $state, Session $record): string => $record->agent()->describe()),
                        PrintColumn::make('ip_address')
                            ->label(__('sessions.columns.ip')),
                        PrintColumn::make('last_activity')
                            ->label(__('sessions.columns.last_active'))
                            ->formatStateUsing(fn (mixed $state, Session $record): string => $record->lastActiveAt()->toDateTimeString()),
                    ]),
            ])
            ->recordActions([
                Action::make('revoke')
                    ->label(__('sessions.actions.revoke'))
                    ->icon(Heroicon::ArrowRightStartOnRectangle)
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading(__('sessions.actions.revoke_confirm_heading'))
                    ->modalDescription(__('sessions.actions.revoke_confirm_description'))
                    // Signing yourself out from here would be a confusing way to log out.
                    ->hidden(fn (Session $record): bool => $record->isCurrent())
                    ->action(function (Session $record): void {
                        SessionRegistry::revoke($record);

                        Notification::make()
                            ->title(__('sessions.notifications.revoked'))
                            ->success()
                            ->send();
                    }),
            ])
            ->toolbarActions([
                BulkAction::make('revokeSelected')
                    ->label(__('sessions.actions.revoke_selected'))
                    ->icon(Heroicon::ArrowRightStartOnRectangle)
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading(__('sessions.actions.revoke_confirm_heading'))
                    ->modalDescription(__('sessions.actions.revoke_confirm_description'))
                    ->deselectRecordsAfterCompletion()
                    ->action(function (Collection $records): void {
                        $revoked = $records
                            ->reject(fn (Session $session): bool => $session->isCurrent())
                            ->each(fn (Session $session) => SessionRegistry::revoke($session))
                            ->count();

                        Notification::make()
                            ->title(trans_choice('sessions.notifications.revoked_many', $revoked, ['count' => $revoked]))
                            ->success()
                            ->send();
                    }),
            ])
            ->emptyStateHeading(__('sessions.empty'))
            ->poll('30s');
    }
}
