<?php

return [

    'from_address' => env('MAIL_FROM_ADDRESS', env('MAIL_FROM', 'noreply@swedish-academy.se')),

    'from_name' => env('MAIL_FROM_NAME', 'Swedish Academy'),

    'imap' => [
        'enabled' => env('IMAP_ENABLED', false),
        'host' => env('IMAP_HOST'),
        'port' => env('IMAP_PORT', 993),
        'encryption' => env('IMAP_ENCRYPTION', 'ssl'),
        'username' => env('IMAP_USERNAME'),
        'password' => env('IMAP_PASSWORD'),
        'folder' => env('IMAP_FOLDER', 'INBOX'),
    ],

    'inbound_webhook_token' => env('EMAIL_INBOUND_WEBHOOK_TOKEN'),

];
