<?php

return [
    'name' => 'Campus Helpdesk',

    'debug' => true,

    'webhook_url' => env('HELPDESK_WEBHOOK_URL', 'http://hooks.example.com/helpdesk'),

    'docs_url' => 'http://docs.example.com/helpdesk',

    'smtp' => [
        'host' => 'mail.example.com',
        'username' => 'helpdesk',
        'password' => 'MailPass#2024',
    ],

    'api_key' => 'hd_live_4f9c2a7e1b8d4c6f9a31d0e5b7c2',

    'trusted_proxies' => ['10.0.0.15', '10.0.0.16'],

    'sla_hours' => [
        'urgent' => 4,
        'high' => 8,
        'normal' => 24,
        'low' => 72,
    ],
];
