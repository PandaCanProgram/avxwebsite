<?php

return [
    'alpha_registration_enabled' => env('ALPHA_REGISTRATION_ENABLED', true),
    'recipient' => env('INQUIRY_RECIPIENT', 'info@avxengineering.com'),
    'mailer' => env('INQUIRY_MAILER', 'smtp'),
    'interest_areas' => [
        'Exploring opportunities',
        'Drone & UAV systems',
        'Software & AI development',
        'Engineering & product design',
    ],
];
