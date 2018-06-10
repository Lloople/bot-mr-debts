<?php

namespace App\Http\Controllers;

use App\Models\User;
use BotMan\BotMan\BotMan;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class DebtsController extends Controller
{

    public function createMe(BotMan $bot, $amount, $creditorUsername)
    {
        try {
            $creditor = User::findByUsernameOrFail($creditorUsername);
        } catch (ModelNotFoundException $e) {
            return $bot->reply("Sorry, I don't know who @{$creditorUsername} is.");
        }

        auth()->user()->owes($amount)->to($creditor);

        return $bot->reply('Got it! you shall pay that debt as soon as possible');
    }
}
