<?php

declare(strict_types=1);

return [

    'actions' => [
        'export' => 'Export CSV / XLSX',
        'print' => 'Print',
        'csv' => 'Export CSV',
        'xlsx' => 'Export XLSX',
    ],
    'notifications' => [
        'completed' => '{1} Export completed: :successful row exported, :failed failed.|[2,*] Export completed: :successful rows exported, :failed failed.',
    ],
    'print' => [
        'generated_at' => 'Generated on :date',
        'empty' => 'No records match the current filters.',
        'frame_title' => 'Printable table',
    ],
    'values' => [
        'yes' => 'Yes',
        'no' => 'No',
    ],

];
