<?php

declare(strict_types=1);

return [

    'title' => 'Settings',
    'subheading' => 'Options that apply to the whole application, for every user.',
    'login' => [
        'heading' => 'Login screen',
        'description' => 'How the sign in page looks to anyone who is not signed in yet.',
        'layout' => [
            'label' => 'Layout',
            'helper' => 'The side panel is hidden on small screens, where the form always fills the width.',
            'options' => [
                'default' => 'Centred',
                'left' => 'Form on the left',
                'right' => 'Form on the right',
            ],
        ],
        'color' => [
            'label' => 'Colour',
            'helper' => 'Colours the login screen and its illustration.',
        ],
    ],
    'actions' => [
        'save' => 'Save',
        'reset' => 'Restore defaults',
    ],
    'notifications' => [
        'saved' => 'Settings saved.',
    ],

];
