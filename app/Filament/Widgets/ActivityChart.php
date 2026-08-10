<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Spatie\Activitylog\Models\Activity;

/**
 * Sign-ins against everything else, day by day.
 *
 * Splitting the two apart is what makes the chart readable: a quiet week of
 * changes with a spike in sign-ins tells a very different story from the
 * reverse, and a single "activity" line hides both.
 */
class ActivityChart extends ChartWidget
{
    use HasWidgetShield;

    private const DAYS = 14;

    protected ?string $pollingInterval = null;

    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = [
        'default' => 'full',
        'lg' => 7,
    ];

    public function getHeading(): string
    {
        return __('dashboard.chart.heading');
    }

    public function getDescription(): ?string
    {
        return __('dashboard.chart.description', ['days' => self::DAYS]);
    }

    /**
     * @return array<string, mixed>
     */
    protected function getData(): array
    {
        $days = $this->days();

        $activities = Activity::query()
            ->where('created_at', '>=', $days->first())
            ->get(['log_name', 'event', 'created_at']);

        $signIns = $this->countPerDay(
            $activities->where('log_name', 'auth')->where('event', 'login'),
            $days,
        );

        $changes = $this->countPerDay(
            $activities->where('log_name', '!=', 'auth'),
            $days,
        );

        return [
            'datasets' => [
                [
                    'label' => __('dashboard.chart.sign_ins'),
                    'data' => $signIns->all(),
                    'borderColor' => 'rgb(16, 185, 129)',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.12)',
                    'fill' => true,
                    'tension' => 0.3,
                ],
                [
                    'label' => __('dashboard.chart.changes'),
                    'data' => $changes->all(),
                    'borderColor' => 'rgb(245, 158, 11)',
                    'backgroundColor' => 'rgba(245, 158, 11, 0.12)',
                    'fill' => true,
                    'tension' => 0.3,
                ],
            ],
            'labels' => $days->map(fn (Carbon $day): string => $day->translatedFormat('d M'))->all(),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    /**
     * @return array<string, mixed>
     */
    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => ['display' => true, 'position' => 'bottom'],
            ],
            'scales' => [
                // Counts are whole numbers, so half-step gridlines are noise.
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => ['precision' => 0],
                ],
            ],
        ];
    }

    /**
     * @return Collection<int, Carbon>
     */
    private function days(): Collection
    {
        $start = Carbon::today()->subDays(self::DAYS - 1);

        return Collection::times(self::DAYS, fn (int $day): Carbon => $start->copy()->addDays($day - 1));
    }

    /**
     * @param  Collection<int, Activity>  $activities
     * @param  Collection<int, Carbon>  $days
     * @return Collection<int, int>
     */
    private function countPerDay(Collection $activities, Collection $days): Collection
    {
        $counts = $activities
            ->groupBy(fn (Activity $activity): string => $activity->created_at->toDateString())
            ->map(fn (Collection $group): int => $group->count());

        return $days->map(fn (Carbon $day): int => $counts->get($day->toDateString(), 0));
    }
}
