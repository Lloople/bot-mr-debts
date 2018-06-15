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

        if (! $creditor || ! $group->users()->find($creditor->id)) {
            return $bot->reply(trans('errors.user_not_found', ['username' => $creditorUsername]));
        }

        $payment = $debtor->pays($amount)->to($creditor)->in($group);

        $payment->save();

        return $bot->reply(trans('debts.add.payment'));
    }

    public function createFromOthers(BotMan $bot, $debtorUsername, $amount)
    {
        $creditor = auth()->user();
        $debtor = User::where('username', $debtorUsername)->first();
        $group = $creditor->group;

        if (! $debtor || ! $group->users()->find($debtor->id)) {
            return $bot->reply(trans('errors.user_not_found', ['username' => $debtorUsername]));
        }

        $payment = $debtor->pays($amount)->to($creditor)->in($group);

        $payment->save();

        return $bot->reply(trans('debts.add.payment'));
    }
}
