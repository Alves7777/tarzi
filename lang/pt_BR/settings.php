<?php

declare(strict_types=1);

return [

    'title' => 'Configurações',
    'subheading' => 'Opções que se aplicam a toda a aplicação, para todos os usuários.',
    'login' => [
        'heading' => 'Tela de login',
        'description' => 'Como a página de entrada aparece para quem ainda não está autenticado.',
        'layout' => [
            'label' => 'Layout',
            'helper' => 'O painel lateral fica oculto em telas pequenas, onde o formulário ocupa toda a largura.',
            'options' => [
                'default' => 'Centralizado',
                'left' => 'Formulário à esquerda',
                'right' => 'Formulário à direita',
            ],
        ],
        'color' => [
            'label' => 'Cor',
            'helper' => 'Define a cor da tela de login e da ilustração.',
        ],
    ],
    'actions' => [
        'save' => 'Salvar',
        'reset' => 'Restaurar padrões',
    ],
    'notifications' => [
        'saved' => 'Configurações salvas.',
    ],

];
