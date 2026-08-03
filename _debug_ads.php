<?php

require __DIR__.'/vendor/autoload.php';
$app = require __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== ADS ===\n";
foreach (App\Models\Advertisement::orderByDesc('id')->limit(10)->get() as $a) {
    echo "{$a->id} | {$a->media_type->value} | {$a->media_path} | {$a->title}\n";
}

echo "\n=== PLACEMENTS ===\n";
foreach (App\Models\AdPlacement::with('advertisement')->orderByDesc('id')->limit(10)->get() as $p) {
    echo "{$p->id} | {$p->placement->value} | ad={$p->advertisement_id} | {$p->advertisement?->title}\n";
}
