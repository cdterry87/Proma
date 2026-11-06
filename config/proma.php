<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Contact
    |--------------------------------------------------------------------------
    |
    | Where the privacy policy sends visitors with questions.
    |
    */

    'contact_url' => env('PROMA_CONTACT_URL', 'https://chaseterry.com'),

    /*
    |--------------------------------------------------------------------------
    | Demo
    |--------------------------------------------------------------------------
    |
    | The login page only shows these credentials when the demo is enabled.
    |
    */

    'demo' => [
        'enabled' => (bool) env('PROMA_DEMO_ENABLED', false),
        'email' => env('PROMA_DEMO_EMAIL'),
        'password' => env('PROMA_DEMO_PASSWORD'),
    ],

];
