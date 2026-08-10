<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Domain\Billing\Enums\InvoiceStatus;
use App\Models\AdPlacement;
use App\Models\Advertisement;
use App\Models\Advertiser;
use App\Models\DisplayScreen;
use App\Models\Invoice;
use App\Models\Session;
use App\Models\User;
use App\Support\SessionRegistry;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

/**
 * Números do negócio e da operação do painel.
 */
class PlatformOverview extends StatsOverviewWidget
{
    use HasWidgetShield;

    private const ONLINE_THRESHOLD_MINUTES = 5;

    private const TREND_DAYS = 7;

    protected ?string $pollingInterval = '60s';

    protected static ?int $sort = 1;

    protected int|string|array $columnSpan = 'full';

    protected function getColumns(): int
    {
        return 4;
    }

    /**
     * @return array<int, Stat>
     */
    protected function getStats(): array
    {
        return [
            $this->advertisersStat(),
            $this->advertisementsStat(),
            $this->screensStat(),
            $this->placementsStat(),
            $this->usersStat(),
            $this->onlineStat(),
            $this->pendingInvoicesStat(),
            $this->revenueStat(),
        ];
    }

    private function advertisersStat(): Stat
    {
        $total = Advertiser::query()->count();
        $active = Advertiser::query()->where('is_active', true)->count();

        return Stat::make(__('dashboard.stats.advertisers'), $active)
            ->description(trans_choice('dashboard.stats.advertisers_description', $active, [
                'count' => $active,
                'total' => $total,
            ]))
            ->descriptionIcon(Heroicon::BuildingStorefront)
            ->color('primary')
            ->icon(Heroicon::OutlinedBuildingStorefront);
    }

    private function advertisementsStat(): Stat
    {
        $total = Advertisement::query()->count();
        $active = Advertisement::query()->where('is_active', true)->count();

        return Stat::make(__('dashboard.stats.advertisements'), $active)
            ->description(trans_choice('dashboard.stats.advertisements_description', $active, [
                'count' => $active,
                'total' => $total,
            ]))
            ->descriptionIcon(Heroicon::RectangleStack)
            ->color('info')
            ->icon(Heroicon::OutlinedRectangleStack);
    }

    private function screensStat(): Stat
    {
        $total = DisplayScreen::query()->count();
        $active = DisplayScreen::query()->where('is_active', true)->count();

        return Stat::make(__('dashboard.stats.screens'), $active)
            ->description(trans_choice('dashboard.stats.screens_description', $active, [
                'count' => $active,
                'total' => $total,
            ]))
            ->descriptionIcon(Heroicon::Tv)
            ->color('success')
            ->icon(Heroicon::OutlinedTv);
    }

    private function placementsStat(): Stat
    {
        $active = AdPlacement::query()->active()->count();

        return Stat::make(__('dashboard.stats.placements'), $active)
            ->description(trans_choice('dashboard.stats.placements_description', $active, ['count' => $active]))
            ->descriptionIcon(Heroicon::ViewColumns)
            ->color('warning')
            ->icon(Heroicon::OutlinedViewColumns);
    }

    private function usersStat(): Stat
    {
        $total = User::query()->count();
        $trend = $this->dailyCounts(User::query()->toBase(), 'created_at');
        $joinedThisWeek = (int) $trend->sum();

        return Stat::make(__('dashboard.stats.users'), $total)
            ->description(trans_choice('dashboard.stats.users_description', $joinedThisWeek, ['count' => $joinedThisWeek]))
            ->descriptionIcon($joinedThisWeek > 0 ? Heroicon::ArrowTrendingUp : Heroicon::Minus)
            ->color($joinedThisWeek > 0 ? 'success' : 'gray')
            ->chart($trend->all())
            ->icon(Heroicon::OutlinedUsers);
    }

    private function onlineStat(): Stat
    {
        if (! SessionRegistry::isSupported()) {
            return Stat::make(__('dashboard.stats.online'), '—')
                ->description(__('dashboard.stats.online_unavailable'))
                ->color('gray')
                ->icon(Heroicon::OutlinedSignalSlash);
        }

        $online = Session::query()->activeWithin(self::ONLINE_THRESHOLD_MINUTES)->count();
        $usersOnline = Session::query()
            ->activeWithin(self::ONLINE_THRESHOLD_MINUTES)
            ->whereNotNull('user_id')
            ->distinct('user_id')
            ->count('user_id');
        $total = Session::query()->count();

        return Stat::make(__('dashboard.stats.online'), $online)
            ->description(
                trans_choice('dashboard.stats.online_description', $usersOnline, [
                    'users' => $usersOnline,
                    'total' => $total,
                ]).' · '.__('dashboard.stats.online_hint')
            )
            ->descriptionIcon(Heroicon::ComputerDesktop)
            ->color($online > 0 ? 'success' : 'gray')
            ->icon(Heroicon::OutlinedSignal);
    }

    private function pendingInvoicesStat(): Stat
    {
        $pending = Invoice::query()
            ->whereIn('status', [InvoiceStatus::Pending, InvoiceStatus::Overdue])
            ->get(['amount_cents']);

        $count = $pending->count();
        $amount = (int) $pending->sum('amount_cents');

        return Stat::make(__('dashboard.stats.invoices_pending'), $count)
            ->description(trans_choice('dashboard.stats.invoices_pending_description', $count, [
                'count' => $count,
                'amount' => $this->formatBrl($amount),
            ]))
            ->descriptionIcon(Heroicon::Clock)
            ->color($count > 0 ? 'danger' : 'gray')
            ->icon(Heroicon::OutlinedBanknotes);
    }

    private function revenueStat(): Stat
    {
        $paid = Invoice::query()
            ->where('status', InvoiceStatus::Paid)
            ->where('paid_at', '>=', now()->startOfMonth())
            ->get(['amount_cents']);

        $count = $paid->count();
        $amount = (int) $paid->sum('amount_cents');

        return Stat::make(__('dashboard.stats.revenue'), $this->formatBrl($amount))
            ->description(trans_choice('dashboard.stats.revenue_description', $count, ['count' => $count]))
            ->descriptionIcon(Heroicon::ArrowTrendingUp)
            ->color($amount > 0 ? 'success' : 'gray')
            ->icon(Heroicon::OutlinedCurrencyDollar);
    }

    /**
     * @return Collection<int, int>
     */
    private function dailyCounts(Builder $query, string $column): Collection
    {
        $since = Carbon::today()->subDays(self::TREND_DAYS - 1);

        $counts = $query
            ->where($column, '>=', $since)
            ->get([$column])
            ->groupBy(fn (object $row): string => Carbon::parse($row->{$column})->toDateString())
            ->map(fn (Collection $rows): int => $rows->count());

        return Collection::times(
            self::TREND_DAYS,
            fn (int $day): int => $counts->get($since->copy()->addDays($day - 1)->toDateString(), 0),
        );
    }

    private function formatBrl(int $cents): string
    {
        return 'R$ '.number_format($cents / 100, 2, ',', '.');
    }
}
