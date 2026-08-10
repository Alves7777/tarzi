<?php

declare(strict_types=1);

return [

    'actions' => [
        'export' => 'Exportar CSV / XLSX',
        'print' => 'Imprimir',
        'csv' => 'Exportar CSV',
        'xlsx' => 'Exportar XLSX',
    ],
    'notifications' => [
        'completed' => '{1} Exportação concluída: :successful linha exportada, :failed com falha.|[2,*] Exportação concluída: :successful linhas exportadas, :failed com falha.',
    ],
    'print' => [
        'generated_at' => 'Gerado em :date',
        'empty' => 'Nenhum registro corresponde aos filtros atuais.',
        'frame_title' => 'Tabela para impressão',
    ],
    'values' => [
        'yes' => 'Sim',
        'no' => 'Não',
    ],

];
