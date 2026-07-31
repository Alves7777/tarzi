<?php

namespace App\Application\Widgets\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

final class ForexRateService
{
    private const CACHE_KEY = 'signage.forex.usd_eur_brl';

    public function getRates(): array
    {
        $cached = Cache::get(self::CACHE_KEY);
        if (is_array($cached) && $this->hasValidRates($cached)) {
            return $cached;
        }

        $rates = $this->fetchFromProviders();

        if ($this->hasValidRates($rates)) {
            Cache::put(self::CACHE_KEY, $rates, now()->addMinutes(15));

            return $rates;
        }

        Log::warning('signage.forex: todas as fontes falharam');

        return ['usd_brl' => null, 'eur_brl' => null];
    }

    /** @return array{usd_brl: float|null, eur_brl: float|null} */
    private function fetchFromProviders(): array
    {
        $usd = null;
        $eur = null;

        $awesome = $this->fetchAwesomeApi();
        $usd ??= $awesome['usd_brl'];
        $eur ??= $awesome['eur_brl'];

        if ($usd === null || $eur === null) {
            $openEr = $this->fetchOpenErApi();
            $usd ??= $openEr['usd_brl'];
            $eur ??= $openEr['eur_brl'];
        }

        if ($usd === null || $eur === null) {
            $bcb = $this->fetchBcbApi();
            $usd ??= $bcb['usd_brl'];
            $eur ??= $bcb['eur_brl'];
        }

        return ['usd_brl' => $usd, 'eur_brl' => $eur];
    }

    /** @return array{usd_brl: float|null, eur_brl: float|null} */
    private function fetchAwesomeApi(): array
    {
        try {
            $response = Http::timeout(8)
                ->acceptJson()
                ->get('https://economia.awesomeapi.com.br/json/last/USD-BRL,EUR-BRL');

            if (! $response->successful()) {
                return ['usd_brl' => null, 'eur_brl' => null];
            }

            $data = $response->json();

            return [
                'usd_brl' => $this->parseBid($data['USDBRL'] ?? null),
                'eur_brl' => $this->parseBid($data['EURBRL'] ?? null),
            ];
        } catch (\Throwable $e) {
            Log::debug('signage.forex.awesome: '.$e->getMessage());

            return ['usd_brl' => null, 'eur_brl' => null];
        }
    }

    /** @return array{usd_brl: float|null, eur_brl: float|null} */
    private function fetchOpenErApi(): array
    {
        try {
            $usdResponse = Http::timeout(8)
                ->acceptJson()
                ->get('https://open.er-api.com/v6/latest/USD');

            $eurResponse = Http::timeout(8)
                ->acceptJson()
                ->get('https://open.er-api.com/v6/latest/EUR');

            $usd = null;
            $eur = null;

            if ($usdResponse->successful()) {
                $usdData = $usdResponse->json();
                $usd = isset($usdData['rates']['BRL'])
                    ? (float) $usdData['rates']['BRL']
                    : null;
            }

            if ($eurResponse->successful()) {
                $eurData = $eurResponse->json();
                $eur = isset($eurData['rates']['BRL'])
                    ? (float) $eurData['rates']['BRL']
                    : null;
            }

            return ['usd_brl' => $usd, 'eur_brl' => $eur];
        } catch (\Throwable $e) {
            Log::debug('signage.forex.open_er: '.$e->getMessage());

            return ['usd_brl' => null, 'eur_brl' => null];
        }
    }

    /** @return array{usd_brl: float|null, eur_brl: float|null} */
    private function fetchBcbApi(): array
    {
        try {
            $usdResponse = Http::timeout(8)
                ->acceptJson()
                ->get('https://api.bcb.gov.br/dados/serie/bcdata.sgs.1/dados/ultimos/1?formato=json');

            $eurResponse = Http::timeout(8)
                ->acceptJson()
                ->get('https://api.bcb.gov.br/dados/serie/bcdata.sgs.21619/dados/ultimos/1?formato=json');

            $usd = null;
            $eur = null;

            if ($usdResponse->successful()) {
                $rows = $usdResponse->json();
                $usd = isset($rows[0]['valor']) ? (float) $rows[0]['valor'] : null;
            }

            if ($eurResponse->successful()) {
                $rows = $eurResponse->json();
                $eur = isset($rows[0]['valor']) ? (float) $rows[0]['valor'] : null;
            }

            return ['usd_brl' => $usd, 'eur_brl' => $eur];
        } catch (\Throwable $e) {
            Log::debug('signage.forex.bcb: '.$e->getMessage());

            return ['usd_brl' => null, 'eur_brl' => null];
        }
    }

    /** @param array<string, mixed>|null $entry */
    private function parseBid(?array $entry): ?float
    {
        if ($entry === null) {
            return null;
        }

        $bid = $entry['bid'] ?? null;

        if ($bid === null || $bid === '') {
            return null;
        }

        return (float) str_replace(',', '.', (string) $bid);
    }

    /** @param array<string, mixed> $rates */
    private function hasValidRates(array $rates): bool
    {
        return ($rates['usd_brl'] ?? null) !== null
            && ($rates['eur_brl'] ?? null) !== null;
    }
}
