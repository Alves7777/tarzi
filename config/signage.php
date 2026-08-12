<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Áreas de exibição (slots)
    |--------------------------------------------------------------------------
    |
    | Cada slot representa uma região fixa no layout do player Flutter.
    | O carrossel principal aceita vários itens; laterais e rodapés aceitam 1.
    |
    */

    'slots' => [
        'main_carousel' => [
            'label' => 'Carrossel principal',
            'description' => 'Área grande à esquerda. Rotação de imagens e vídeos em sequência.',
            'max_items' => null,
            'recommended' => [
                'landscape_16_9' => ['width' => 1920, 'height' => 1080],
                'portrait_9_16' => ['width' => 1080, 'height' => 1920],
                'square_1_1' => ['width' => 1080, 'height' => 1080],
                'tablet_4_3' => ['width' => 1024, 'height' => 768],
                'elevator_portrait' => ['width' => 1080, 'height' => 1920],
            ],
        ],
        'sidebar_1' => [
            'label' => 'Lateral inferior',
            'description' => 'Faixa inferior da coluna direita (abaixo do QR).',
            'max_items' => 1,
            'recommended' => [
                'landscape_16_9' => ['width' => 1080, 'height' => 500],
                'portrait_9_16' => ['width' => 1080, 'height' => 600],
                'square_1_1' => ['width' => 800, 'height' => 800],
                'tablet_4_3' => ['width' => 800, 'height' => 600],
                'elevator_portrait' => ['width' => 1080, 'height' => 500],
            ],
        ],
        'sidebar_2' => [
            'label' => 'Lateral central',
            'description' => 'Faixa central da coluna direita.',
            'max_items' => 1,
            'recommended' => [
                'landscape_16_9' => ['width' => 1080, 'height' => 500],
                'portrait_9_16' => ['width' => 1080, 'height' => 600],
                'square_1_1' => ['width' => 800, 'height' => 800],
                'tablet_4_3' => ['width' => 800, 'height' => 600],
                'elevator_portrait' => ['width' => 1080, 'height' => 500],
            ],
        ],
        'sidebar_3' => [
            'label' => 'Lateral superior',
            'description' => 'Faixa superior da coluna direita (acima do QR).',
            'max_items' => 1,
            'recommended' => [
                'landscape_16_9' => ['width' => 1080, 'height' => 500],
                'portrait_9_16' => ['width' => 1080, 'height' => 600],
                'square_1_1' => ['width' => 800, 'height' => 800],
                'tablet_4_3' => ['width' => 800, 'height' => 600],
                'elevator_portrait' => ['width' => 1080, 'height' => 500],
            ],
        ],
        'footer_1' => [
            'label' => 'Rodapé esquerdo',
            'description' => 'Barra inferior esquerda (quando o layout tiver rodapé).',
            'max_items' => 1,
            'recommended' => [
                'landscape_16_9' => ['width' => 960, 'height' => 200],
                'portrait_9_16' => ['width' => 1080, 'height' => 250],
                'square_1_1' => ['width' => 600, 'height' => 600],
                'tablet_4_3' => ['width' => 700, 'height' => 200],
                'elevator_portrait' => ['width' => 1080, 'height' => 250],
            ],
        ],
        'footer_2' => [
            'label' => 'Rodapé direito',
            'description' => 'Barra inferior direita (quando o layout tiver rodapé).',
            'max_items' => 1,
            'recommended' => [
                'landscape_16_9' => ['width' => 960, 'height' => 200],
                'portrait_9_16' => ['width' => 1080, 'height' => 250],
                'square_1_1' => ['width' => 600, 'height' => 600],
                'tablet_4_3' => ['width' => 700, 'height' => 200],
                'elevator_portrait' => ['width' => 1080, 'height' => 250],
            ],
        ],
    ],

    'media_guidelines' => [
        'image' => 'JPG ou PNG, sRGB, sem transparência para melhor contraste em telas externas.',
        'video' => 'MP4 H.264, áudio opcional. Informe a duração total para segmentação automática.',
        'youtube' => 'Link público do YouTube. A duração do trecho exibido é definida na tela.',
    ],

    /*
    |--------------------------------------------------------------------------
    | Armazenamento de mídia
    |--------------------------------------------------------------------------
    |
    | SIGNAGE_MEDIA_DISK=s3 grava uploads em AWS S3 / MinIO (bucket tarzi).
    | Use "public" apenas em desenvolvimento local sem S3.
    |
    */

    'media_disk' => env('SIGNAGE_MEDIA_DISK', 's3'),

    'media_directory' => 'advertisements',

    'media_proxy' => env('SIGNAGE_MEDIA_PROXY', true),

];
