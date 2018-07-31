<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Telegram Token
    |--------------------------------------------------------------------------
    |
    | Your Telegram bot token you received after creating
    | the chatbot through Telegram.
    |
    */
    'token' => env('TELEGRAM_TOKEN'),

    'bot' => [
        'id' => env('TELEGRAM_BOT_ID'),
        'username' => env('TELEGRAM_BOT_USERNAME')
    ],
];
