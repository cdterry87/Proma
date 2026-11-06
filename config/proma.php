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
    | Due reminders
    |--------------------------------------------------------------------------
    |
    | How many days ahead of a due date projects and tasks send a reminder.
    |
    */

    'due_reminder_days' => (int) env('PROMA_DUE_REMINDER_DAYS', 10),

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
