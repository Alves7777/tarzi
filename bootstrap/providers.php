<?php

use App\Providers\ActivityLogServiceProvider;
use App\Providers\AppServiceProvider;
use App\Providers\Filament\AdminPanelProvider;

return [
    ActivityLogServiceProvider::class,
    AppServiceProvider::class,
    AdminPanelProvider::class,
];
