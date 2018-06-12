<?php

namespace App\Http\Controllers;

use App\Models\User;
use BotMan\BotMan\BotMan;

class PaymentsController extends Controller
{
    public function createFromMe(BotMan $bot, $amount, $creditorUsername)
    {
        $debtor = auth()->user();
        $creditor = User::where('username', $creditorUsername)->first();
        $group = $debtor->group;

        if (! $creditor) {
            return $bot->reply("Sorry, I don't know who @{$creditorUsername} is.");
        }

        if (! $group->users()->find($creditor->id)) {
            return $bot->reply("You cannot pay to @{$creditorUsername} on this group.");
        }

        $payment = $debtor->pays($amount)->to($creditor)->in($group);

        $payment->save();

        return $bot->reply('Got it!');
    }

    public function createFromOthers(BotMan $bot, $debtorUsername, $amount)
    {
        $debtor = User::where('username', $debtorUsername)->first();
        $creditor = auth()->user();
        $group = $creditor->group;

        if (! $debtor) {
            return $bot->reply("Sorry, I don't know who @{$debtorUsername} is.");
        }

        if (! $group->users()->find($debtor->id)) {
            return $bot->reply("You cannot add a debt to @{$debtorUsername} on this group.");
        }

        $debt = $debtor->owes($amount)->to($creditor)->in($group);

        $debt->save();

        return $bot->reply('Got it!');
    }
}
