<?php

require __DIR__.'/vendor/autoload.php';
$app = require __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Domain\Ads\Enums\AdPlacement;
use App\Models\AdPlacement as AdPlacementModel;
use App\Models\Advertisement;

$ad = Advertisement::query()->find(98);

if ($ad === null) {
    echo "Anuncio 98 nao encontrado.\n";
    exit(1);
}

$exists = AdPlacementModel::query()
    ->where('advertisement_id', $ad->id)
    ->where('placement', AdPlacement::MainCarousel->value)
    ->exists();

if ($exists) {
    echo "Placement ja existe para {$ad->title}.\n";
    exit(0);
}

AdPlacementModel::query()->create([
    'advertisement_id' => $ad->id,
    'display_screen_id' => null,
    'placement' => AdPlacement::MainCarousel,
    'sort_order' => 0,
    'is_active' => true,
    'price_cents' => 0,
]);

echo "Placement main_carousel criado para: {$ad->title} (id {$ad->id})\n";
