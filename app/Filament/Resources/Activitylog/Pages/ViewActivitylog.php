<?php

declare(strict_types=1);

namespace App\Filament\Resources\Activitylog\Pages;

use App\Filament\Resources\Activitylog\ActivitylogResource;
use Rmsramos\Activitylog\Resources\Activitylog\Pages\ViewActivitylog as BaseViewActivitylog;

class ViewActivitylog extends BaseViewActivitylog
{
    protected static string $resource = ActivitylogResource::class;
}
