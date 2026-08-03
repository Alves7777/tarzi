<?php

namespace App\Application\Ads\Services;

use App\Application\Ads\DTO\AdItemDto;
use App\Application\Ads\DTO\DisplayFeedDto;
use App\Application\Widgets\Services\ForexRateService;
use App\Domain\Ads\Enums\AdPlacement as AdPlacementEnum;
use App\Models\AdPlacement;
use App\Models\Advertisement;
use App\Models\DisplayScreen;
use Illuminate\Support\Collection;

final class DisplayFeedService
{
    public function __construct(
        private readonly ForexRateService $forexRateService,
    ) {}

    public function buildForScreen(DisplayScreen $screen): DisplayFeedDto
    {
        $placements = AdPlacement::query()
            ->with('advertisement')
            ->active()
            ->where(function ($query) use ($screen): void {
                $query->whereNull('display_screen_id')
                    ->orWhere('display_screen_id', $screen->id);
            })
            ->orderBy('sort_order')
            ->get();

        $grouped = $placements->groupBy(fn (AdPlacement $p) => $p->placement->value);

        $forex = $this->forexRateService->getRates();

        return new DisplayFeedDto(
            screenUuid: $screen->uuid,
            screenName: $screen->name,
            carouselSeconds: $screen->carousel_seconds,
            mainCarousel: $this->mapMany($grouped->get(AdPlacementEnum::MainCarousel->value, collect())),
            sidebar1: $this->mapSingle($grouped->get(AdPlacementEnum::Sidebar1->value, collect())),
            sidebar2: $this->mapSingle($grouped->get(AdPlacementEnum::Sidebar2->value, collect())),
            sidebar3: $this->mapSingle($grouped->get(AdPlacementEnum::Sidebar3->value, collect())),
            footer1: $this->mapSingle($grouped->get(AdPlacementEnum::Footer1->value, collect())),
            footer2: $this->mapSingle($grouped->get(AdPlacementEnum::Footer2->value, collect())),
            currentTime: now()->timezone(config('app.timezone'))->toIso8601String(),
            timezone: config('app.timezone'),
            usdBrl: $forex['usd_brl'] ?? null,
            eurBrl: $forex['eur_brl'] ?? null,
            qrUrl: $screen->qr_url,
            qrLabel: $screen->qr_label,
            qrCaption: $screen->qr_caption,
        );
    }

    /** @param Collection<int, AdPlacement> $placements */
    private function mapMany(Collection $placements): array
    {
        return $placements
            ->map(fn (AdPlacement $placement) => $this->toDto($placement->advertisement))
            ->filter()
            ->values()
            ->all();
    }

    /** @param Collection<int, AdPlacement> $placements */
    private function mapSingle(Collection $placements): ?AdItemDto
    {
        $first = $placements->first();

        if ($first === null) {
            return null;
        }

        return $this->toDto($first->advertisement);
    }

    private function toDto(?Advertisement $advertisement): ?AdItemDto
    {
        if ($advertisement === null || ! $advertisement->is_active) {
            return null;
        }

        return new AdItemDto(
            id: $advertisement->id,
            title: $advertisement->title,
            mediaType: $advertisement->media_type->value,
            mediaUrl: $advertisement->mediaUrl(),
            clickUrl: $advertisement->click_url,
            durationSeconds: $advertisement->duration_seconds,
        );
    }
}
