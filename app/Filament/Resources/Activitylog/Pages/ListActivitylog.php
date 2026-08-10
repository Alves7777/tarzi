<?php

declare(strict_types=1);

namespace App\Filament\Resources\Activitylog\Pages;

use App\Filament\Resources\Activitylog\ActivitylogResource;
use Rmsramos\Activitylog\Resources\Activitylog\Pages\ListActivitylog as BaseListActivitylog;

class ListActivitylog extends BaseListActivitylog
{
    protected static string $resource = ActivitylogResource::class;
}
