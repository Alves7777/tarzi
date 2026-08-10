<?php

declare(strict_types=1);

return [

    'greeting' => ':greeting, :name. Here is what has been happening.',

    'greetings' => [
        'morning' => 'Good morning',
        'afternoon' => 'Good afternoon',
        'evening' => 'Good evening',
    ],

    'stats' => [
        'users' => 'Users',
        'users_description' => '{0} no new accounts this week|{1} 1 new account this week|[2,*] :count new accounts this week',
        'online' => 'Online now',
        'online_description' => '{0} no sessions stored|{1} 1 session stored|[2,*] :count sessions stored',
        'online_unavailable' => 'Needs the database session driver',
        'roles' => 'Roles',
        'roles_description' => '{0} no permissions|{1} 1 permission|[2,*] :count permissions',
        'activity' => 'Activity today',
        'activity_description' => '{0} nothing in the last 7 days|{1} 1 event in the last 7 days|[2,*] :count events in the last 7 days',
    ],

    'chart' => [
        'heading' => 'Activity',
        'description' => 'The last :days days',
        'sign_ins' => 'Sign-ins',
        'changes' => 'Changes',
    ],

    'latest_activity' => [
        'heading' => 'Latest activity',
        'event' => 'Event',
        'description' => 'What happened',
        'causer' => 'By',
        'when' => 'When',
        'system' => 'System',
        'empty' => 'Nothing has happened yet.',
    ],

];
