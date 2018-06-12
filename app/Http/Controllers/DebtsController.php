<?php

namespace App\Http\Controllers;

use App\Models\User;
use BotMan\BotMan\BotMan;

class DebtsController extends Controller
{

    public function index(BotMan $bot)
    {
        $user = auth()->user();

        $debtsToPay = $user->debts_to_pay()->whereNull('paid_at')
            ->selectRaw('to_id, SUM(amount) as amount')
            ->groupBy('to_id')
            ->get()
            ->map->toStringFromDebtor();

        $debtsToReceive = $user->debts_to_receive()->whereNull('paid_at')
            ->selectRaw('from_id, SUM(amount) as amount')
            ->groupBy('from_id')
            ->get()
            ->map->toStringFromCreditor();

        return $bot->reply(collect($debtsToPay)->merge($debtsToReceive)->implode('<br>'));
    }

    public function createFromMe(BotMan $bot, $amount, $creditorUsername)
    {
        $debtor = auth()->user();
        $creditor = User::where('username', $creditorUsername)->first();
        $group = $debtor->group;

        if (! $creditor) {
            return $bot->reply("Sorry, I don't know who @{$creditorUsername} is.");
        }

        if (! $group->users()->find($creditor->id)) {
            return $bot->reply("You cannot add a debt to @{$creditorUsername} on this group.");
        }

        $payment = $creditor->pays($amount)->to($debtor)->in($group);
        $payment->save();

        return $bot->reply('Got it! you shall pay that debt as soon as possible');
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

        $payment = $creditor->pays($amount)->to($debtor)->in($group);
        $payment->save();


        return $bot->reply('Got it!');
    }
}
