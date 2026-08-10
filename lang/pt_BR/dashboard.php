<?php

declare(strict_types=1);

return [

    'greeting' => ':greeting, :name. Veja o que aconteceu recentemente.',

    'greetings' => [
        'morning' => 'Bom dia',
        'afternoon' => 'Boa tarde',
        'evening' => 'Boa noite',
    ],

    'stats' => [
        'users' => 'Usuários',
        'users_description' => '{0} nenhuma conta nova esta semana|{1} 1 conta nova esta semana|[2,*] :count contas novas esta semana',

        'online' => 'Sessões ativas',
        'online_description' => '{0} nenhum usuário conectado|{1} 1 usuário · :total sessão no total|[2,*] :users usuários · :total sessões no total',
        'online_hint' => 'Abas no mesmo navegador compartilham 1 sessão',
        'online_unavailable' => 'Requer o driver de sessão database',

        'advertisers' => 'Anunciantes',
        'advertisers_description' => '{0} nenhum ativo|{1} 1 ativo|[2,*] :count ativos · :total cadastrados',

        'advertisements' => 'Anúncios',
        'advertisements_description' => '{0} nenhum ativo|{1} 1 ativo|[2,*] :count ativos · :total cadastrados',

        'screens' => 'Telas',
        'screens_description' => '{0} nenhuma ativa|{1} 1 ativa|[2,*] :count ativas · :total cadastradas',

        'placements' => 'Posicionamentos',
        'placements_description' => '{0} nenhum slot ativo|{1} 1 slot ativo|[2,*] :count slots ativos',

        'invoices_pending' => 'Faturas pendentes',
        'invoices_pending_description' => '{0} nenhuma pendente|{1} :amount em aberto|[2,*] :count faturas · :amount em aberto',

        'revenue' => 'Receita do mês',
        'revenue_description' => '{0} nenhum pagamento|{1} 1 fatura paga|[2,*] :count faturas pagas',

        'roles' => 'Funções',
        'roles_description' => '{0} nenhuma permissão|{1} 1 permissão|[2,*] :count permissões',

        'activity' => 'Atividade hoje',
        'activity_description' => '{0} nada nos últimos 7 dias|{1} 1 evento nos últimos 7 dias|[2,*] :count eventos nos últimos 7 dias',
    ],

    'chart' => [
        'heading' => 'Atividade',
        'description' => 'Últimos :days dias',
        'sign_ins' => 'Entradas',
        'changes' => 'Alterações',
    ],

    'latest_activity' => [
        'heading' => 'Última atividade',
        'event' => 'Evento',
        'description' => 'O que aconteceu',
        'causer' => 'Por',
        'when' => 'Quando',
        'system' => 'Sistema',
        'empty' => 'Nada aconteceu ainda.',
    ],

    'events' => [
        'created' => 'Criado',
        'updated' => 'Atualizado',
        'deleted' => 'Excluído',
        'login' => 'Entrada',
        'logout' => 'Saída',
        'login_failed' => 'Entrada falhou',
        'lockout' => 'Bloqueio',
        'password_reset' => 'Senha redefinida',
        'revoked' => 'Sessão encerrada',
        'revoked_others' => 'Outras sessões encerradas',
        'role_attached' => 'Função atribuída',
        'permission_attached' => 'Permissão atribuída',
    ],

    'logs' => [
        'auth.login' => 'Entrada no painel',
        'auth.logout' => 'Saída do painel',
        'auth.login_failed' => 'Tentativa de entrada falhou',
        'auth.lockout' => 'Conta bloqueada por tentativas',
        'auth.password_reset' => 'Senha redefinida',
        'session.revoked' => 'Sessão encerrada remotamente',
        'session.revoked_others' => 'Outras sessões encerradas',
    ],

    'models' => [
        'User' => 'Usuário',
        'Role' => 'Função',
        'Advertiser' => 'Anunciante',
        'Advertisement' => 'Anúncio',
        'DisplayScreen' => 'Tela',
        'AdPlacement' => 'Posicionamento',
        'Invoice' => 'Fatura',
        'BillingPlan' => 'Plano',
    ],

];
