<?php

declare(strict_types=1);

return [

    'title' => 'Sessões',
    'subheading' => 'Todos os dispositivos conectados à aplicação no momento.',
    'empty' => 'Nenhuma sessão ativa.',

    'current_device' => 'Este dispositivo',
    'active_now' => 'ativo agora',
    'last_active' => 'última atividade :time',
    'unknown_device' => 'Dispositivo desconhecido',
    'unknown_ip' => 'IP desconhecido',
    'guest' => 'Não autenticado',
    'device_description' => ':browser no :platform',

    'columns' => [
        'user' => 'Usuário',
        'device' => 'Dispositivo',
        'ip' => 'Endereço IP',
        'last_active' => 'Última atividade',
    ],

    'filters' => [
        'user' => 'Usuário',
        'online' => 'Online agora',
        'guests' => 'Desconectados',
    ],

    'actions' => [
        'revoke' => 'Desconectar',
        'revoke_selected' => 'Desconectar selecionados',
        'revoke_others' => 'Desconectar outros dispositivos',
        'revoke_confirm_heading' => 'Desconectar este dispositivo?',
        'revoke_confirm_description' => 'O dispositivo precisará entrar novamente para continuar.',
        'revoke_others_confirm_heading' => 'Desconectar todos os outros dispositivos?',
        'revoke_others_confirm_description' => 'Este dispositivo permanece conectado. Todos os outros precisarão entrar novamente.',
    ],

    'notifications' => [
        'revoked' => 'Dispositivo desconectado.',
        'revoked_many' => '{0} Nenhum dispositivo desconectado.|{1} 1 dispositivo desconectado.|[2,*] :count dispositivos desconectados.',
        'revoked_others' => '{0} Nenhum outro dispositivo estava conectado.|{1} 1 outro dispositivo desconectado.|[2,*] :count outros dispositivos desconectados.',
    ],

    'profile' => [
        'heading' => 'Sessões do navegador',
        'description' => 'Os dispositivos conectados à sua conta. Desconecte os que você não reconhece.',
    ],

];
