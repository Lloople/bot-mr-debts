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
        $creditor = auth()->user();
        $debtor = User::where('username', $debtorUsername)->first();
        $group = $creditor->group;

        if (! $debtor) {
            return $bot->reply("Sorry, I don't know who @{$debtorUsername} is.");
        }

        if (! $group->users()->find($debtor->id)) {
            return $bot->reply("@{$debtorUsername} cannot pay you on this group.");
        }

        $payment = $debtor->pays($amount)->to($creditor)->in($group);

        $payment->save();

        return $bot->reply('Got it!');
    }
}
