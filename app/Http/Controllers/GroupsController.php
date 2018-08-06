<?php

namespace App\Http\Controllers;

use App\Conversations\RegisterGroupConversation;
use BotMan\BotMan\BotMan;

class GroupsController extends Controller
{
    public function register(BotMan $bot)
    {
        $bot->startConversation(new RegisterGroupConversation());
    }

    public function registerNewGroup($payload, BotMan $bot)
    {
        $this->register($bot);
    }

    public function registerNewChatMember($payload, BotMan $bot)
    {
        foreach ($payload as $newUser) {
            if ($newUser['is_bot'] && $newUser['id'] === config('telegram.bot.id')) {
                $this->register($bot);

                return;
            }
        }
    }
}
