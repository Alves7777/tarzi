<?php

declare(strict_types=1);

return [

    'title' => 'Sessions',
    'subheading' => 'Every device currently signed in to the application.',
    'empty' => 'No active sessions.',

    'current_device' => 'This device',
    'active_now' => 'active now',
    'last_active' => 'last active :time',
    'unknown_device' => 'Unknown device',
    'unknown_ip' => 'Unknown IP',
    'guest' => 'Not signed in',
    'device_description' => ':browser on :platform',

    'columns' => [
        'user' => 'User',
        'device' => 'Device',
        'ip' => 'IP address',
        'last_active' => 'Last active',
    ],

    'filters' => [
        'user' => 'User',
        'online' => 'Online now',
        'guests' => 'Signed out',
    ],

    'actions' => [
        'revoke' => 'Sign out',
        'revoke_selected' => 'Sign out selected',
        'revoke_others' => 'Sign out other devices',
        'revoke_confirm_heading' => 'Sign this device out?',
        'revoke_confirm_description' => 'The device will have to sign in again to continue.',
        'revoke_others_confirm_heading' => 'Sign out every other device?',
        'revoke_others_confirm_description' => 'This device stays signed in. Every other device will have to sign in again.',
    ],

    'notifications' => [
        'revoked' => 'Device signed out.',
        'revoked_many' => '{0} No devices signed out.|{1} 1 device signed out.|[2,*] :count devices signed out.',
        'revoked_others' => '{0} No other devices were signed in.|{1} 1 other device signed out.|[2,*] :count other devices signed out.',
    ],

    'profile' => [
        'heading' => 'Browser sessions',
        'description' => 'The devices signed in to your account. Sign out any you do not recognise.',
    ],

];
