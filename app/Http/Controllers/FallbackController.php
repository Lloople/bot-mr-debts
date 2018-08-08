<?php

namespace App\Http\Controllers;

use BotMan\BotMan\BotMan;

class FallbackController extends Controller
{

    const FALLBACK_REPLIES = [
        'dont_understadd',
        'sure_write_ok',
        'use_other_words',
    ];

    public function index(BotMan $bot)
    {
        return $bot->randomReply($this->getRepliesTranslated());
    }

    private function getRepliesTranslated()
    {
        return collect(self::FALLBACK_REPLIES)->map(function ($key) {
            return trans('fallback.' . $key);
        })->toArray();
    }
}
