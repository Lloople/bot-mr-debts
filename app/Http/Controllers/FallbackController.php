<?php

namespace App\Http\Controllers;

use App\Conversations\RegisterGroupConversation;
use BotMan\BotMan\BotMan;

class FallbackController extends Controller
{

    public function index(BotMan $bot)
    {
        if ($this->isCreatingAGroupOrJoiningAGroup($bot)) {
            $bot->startConversation(new RegisterGroupConversation);

            return;
        }

        dump($bot->getMessage()->getPayload());
        $bot->reply('Sorry, I did not understand these commands. Here is a list of commands I understand: ...');
    }

    private function isCreatingAGroupOrJoiningAGroup(BotMan $bot)
    {
        return $bot->getMessage()->getPayload()->get('group_chat_created')
            || ($bot->getMessage()->getPayload()->get('new_chat_members'));
    }
}
