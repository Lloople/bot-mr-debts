<?php

namespace App\Http\Controllers;

use App\Conversations\RegisterGroupConversation;
use BotMan\BotMan\BotMan;

class GroupsController extends Controller
{
    public function registerNewGroup($payload, BotMan $bot)
    {
        $bot->startConversation(new RegisterGroupConversation());
    }

    public function registerNewChatMember($payload, BotMan $bot)
    {
        dump($payload);
        foreach ($payload as $newUser) {
            if ($newUser['is_bot'] && $newUser['id'] === config('telegram.bot.id')) {
                $bot->startConversation(new RegisterGroupConversation());

                return;
            }
        }
    }
}
