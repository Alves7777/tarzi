<?php

declare(strict_types=1);

return [

    'ad_status' => [
        'draft' => 'Rascunho',
        'pending' => 'Aguardando aprovação',
        'approved' => 'Aprovado',
        'rejected' => 'Rejeitado',
    ],

    'screen_formats' => [
        'landscape_16_9' => [
            'label' => 'TV / painel paisagem (16:9)',
            'description' => 'Monitores horizontais, totens e TVs comerciais. Padrão 1920×1080 px.',
        ],
        'portrait_9_16' => [
            'label' => 'Painel retrato (9:16)',
            'description' => 'Elevadores, espelhos digitais e colunas verticais. Padrão 1080×1920 px.',
        ],
        'square_1_1' => [
            'label' => 'Quadrado (1:1)',
            'description' => 'Tablets em quiosques e displays compactos. Padrão 1080×1080 px.',
        ],
        'tablet_4_3' => [
            'label' => 'Tablet (4:3)',
            'description' => 'Tablets fixos e telas legadas. Padrão 1024×768 px.',
        ],
        'elevator_portrait' => [
            'label' => 'Elevador retrato',
            'description' => 'Formato alto para cabines de elevador. Padrão 1080×1920 px.',
        ],
    ],

    'slots' => [
        'main_carousel' => [
            'label' => 'Carrossel principal',
            'description' => 'Área principal de rotação — exibe vários anúncios e vídeos em fila.',
        ],
        'sidebar_1' => [
            'label' => 'Lateral inferior',
            'description' => 'Slot fixo na parte inferior da coluna lateral.',
        ],
        'sidebar_2' => [
            'label' => 'Lateral central',
            'description' => 'Slot fixo no centro da coluna lateral.',
        ],
        'sidebar_3' => [
            'label' => 'Lateral superior',
            'description' => 'Slot fixo no topo da coluna lateral.',
        ],
        'footer_1' => [
            'label' => 'Rodapé esquerdo',
            'description' => 'Faixa inferior esquerda do layout.',
        ],
        'footer_2' => [
            'label' => 'Rodapé direito',
            'description' => 'Faixa inferior direita do layout.',
        ],
    ],

    'playback' => [
        'ads_before_video' => 'Anúncios antes do vídeo',
        'ads_before_video_helper' => 'Quantas imagens/anúncios exibir antes de cada trecho de vídeo no carrossel.',
        'video_segment_seconds' => 'Duração do trecho de vídeo (s)',
        'video_segment_seconds_helper' => 'Segundos de cada bloco de vídeo antes de voltar aos anúncios. O player continua de onde parou no próximo ciclo.',
        'carousel_seconds' => 'Tempo por imagem (s)',
        'carousel_seconds_helper' => 'Segundos que cada imagem fica visível no carrossel (contador 7s, 8s…).',
    ],

    'size_hint' => 'Tamanho ideal para :slot nesta tela: :width×:height px (:format).',
    'size_hint_generic' => 'Envie a arte exatamente neste tamanho para evitar cortes ou barras pretas.',

    'advertiser_panel' => [
        'title' => 'Portal do anunciante',
        'submit_for_review' => 'Enviar para aprovação',
        'submit_confirm' => 'Enviar este anúncio para análise do administrador?',
        'submitted' => 'Anúncio enviado para aprovação.',
    ],

    'admin' => [
        'approve' => 'Aprovar',
        'reject' => 'Rejeitar',
        'reject_reason' => 'Motivo da rejeição',
        'approved' => 'Anúncio aprovado e liberado para exibição.',
        'rejected' => 'Anúncio rejeitado.',
    ],

    'placements' => [
        'heading' => 'Onde exibir',
        'description' => 'Defina em quais telas e áreas este anúncio aparece. Um anúncio pode ter vários posicionamentos.',
    ],

    'screen_ads' => [
        'heading' => 'Anúncios nesta tela',
        'description' => 'Prévia dos criativos cadastrados para esta tela.',
    ],

];
