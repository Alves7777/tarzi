<?php

namespace App\Http\Controllers\Api\V1;

use App\Application\Widgets\Services\ForexRateService;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

final class ForexController extends Controller
{
    public function __construct(
        private readonly ForexRateService $forexRateService,
    ) {}

    public function show(): JsonResponse
    {
        $rates = $this->forexRateService->getRates();

        return response()->json([
            'usd_brl' => $rates['usd_brl'] ?? null,
            'eur_brl' => $rates['eur_brl'] ?? null,
            'source' => ($rates['usd_brl'] ?? null) !== null
                ? 'awesomeapi + fallbacks (open.er-api, bcb)'
                : 'unavailable',
            'updated_at' => now()->timezone(config('app.timezone'))->toIso8601String(),
        ]);
    }
}
