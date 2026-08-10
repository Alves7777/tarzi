<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Defaults
    |--------------------------------------------------------------------------
    |
    | Used until an administrator saves something else on the settings page.
    | Every value must exist in the corresponding list below; the colour must
    | exist in the `ui-switcher` palette, which is shared with the panel.
    |
    */

    'defaults' => [
        'layout' => 'default',
        'color' => '#6366f1',
    ],

    /*
    |--------------------------------------------------------------------------
    | Layouts
    |--------------------------------------------------------------------------
    |
    | "default" centred card, no side panel
    | "left"    form on the left, illustrated panel on the right
    | "right"   form on the right, illustrated panel on the left
    |
    | Remove an entry to hide it from the settings page.
    |
    */

    'layouts' => [
        'default',
        'left',
        'right',
    ],

];
