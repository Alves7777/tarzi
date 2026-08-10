<?php

declare(strict_types=1);

return [
    'navigation' => [
        'administration' => 'Administration',
    ],
    'users' => [
        'singular' => 'User',
        'plural' => 'Users',
        'fields' => [
            'name' => 'Name',
            'email' => 'Email address',
            'password' => 'Password',
            'email_verified_at' => 'Email verified at',
            'roles' => 'Roles',
            'created_at' => 'Created at',
            'updated_at' => 'Updated at',
        ],
        'password_helper' => 'Leave empty to keep the current password.',
    ],
];
