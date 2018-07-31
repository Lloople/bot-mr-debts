<?php

namespace App\Http\Controllers;

use App\Conversations\RegisterGroupConversation;
use BotMan\BotMan\BotMan;

class FallbackController extends Controller
{

    public function index(BotMan $bot)
    {
        $bot->reply('Sorry, I did not understand these commands. Here is a list of commands I understand: ...');
    }
}
