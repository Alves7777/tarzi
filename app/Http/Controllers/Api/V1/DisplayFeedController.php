<?php

namespace App\Http\Controllers\Api\V1;

use App\Application\Ads\Services\DisplayFeedService;
use App\Http\Controllers\Controller;
use App\Models\DisplayScreen;
use Illuminate\Http\JsonResponse;

final class DisplayFeedController extends Controller
{
    public function __construct(
        private readonly DisplayFeedService $displayFeedService,
    ) {}

    public function show(string $uuid): JsonResponse
    {
        $screen = DisplayScreen::query()
            ->where('uuid', $uuid)
            ->where('is_active', true)
            ->firstOrFail();

        return response()->json(
            $this->displayFeedService->buildForScreen($screen)->toArray(),
        );
    }
}
