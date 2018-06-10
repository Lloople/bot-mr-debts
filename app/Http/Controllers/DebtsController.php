<?php

namespace App\Http\Controllers;

use App\Models\User;
use BotMan\BotMan\BotMan;

class DebtsController extends Controller
{

    public function createMe(BotMan $bot, $amount, $creditorUsername)
    {
        $creditor = User::where('username', $creditorUsername)->first();

        if (! $creditor) {
            return $bot->reply("Sorry, I don't know who @{$creditorUsername} is.");
        }

        if (! auth()->user()->group->users()->find($creditor->id)) {
            return $bot->reply("You cannot add a debt to @{$creditorUsername} on this group.");
        }

        $debt = auth()->user()->owes($amount)->to($creditor)->in(auth()->user()->group->id);

        $debt->save();

        return $bot->reply('Got it! you shall pay that debt as soon as possible');
    }
}
