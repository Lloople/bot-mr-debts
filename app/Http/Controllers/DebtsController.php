<?php

namespace App\Http\Controllers;

use App\Models\User;
use BotMan\BotMan\BotMan;

class DebtsController extends Controller
{
    public function index(BotMan $bot, $language)
    {
        app()->setLocale($language);

        $user = auth()->user();

        $debtsToPay = $user->debts_to_pay()
            ->selectRaw('to_id, SUM(amount) as amount, currency')
            ->groupBy('to_id')
            ->get()
            ->map->toStringFromDebtor();

        $debtsToReceive = $user->debts_to_receive()
            ->selectRaw('from_id, SUM(amount) as amount, currency')
            ->groupBy('from_id')
            ->get()
            ->map->toStringFromCreditor();

        return $bot->reply(collect($debtsToPay)->merge($debtsToReceive)->implode(PHP_EOL));
    }

    public function createFromMe(BotMan $bot, $amount, $creditorUsername, $language)
    {
        app()->setLocale($language);

        $debtor = auth()->user();
        $creditor = User::where('username', $creditorUsername)->first();
        $group = $debtor->group;

        if (! $creditor || ! $group->users()->find($creditor->id)) {
            return $bot->reply(trans('errors.user_not_found', ['username' => $creditorUsername]));
        }

        $creditor->pays($amount)->to($debtor)->in($group)->save();

        return $bot->reply(trans('debts.add.debt_me'));
    }

    public function createFromOthers(BotMan $bot, $debtorUsername, $amount, $language)
    {
        app()->setLocale($language);

        $debtor = User::where('username', $debtorUsername)->first();
        $creditor = auth()->user();
        $group = $creditor->group;

        if (! $debtor || ! $group->users()->find($debtor->id)) {
            return $bot->reply(trans('errors.user_not_found', ['username' => $debtorUsername]));
        }

        $creditor->pays($amount)->to($debtor)->in($group)->save();

        return $bot->reply(trans('debts.add.debt_others'));
    }
}
