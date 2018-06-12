<?php

namespace App;

use App\Models\Debt;
use App\Models\Group;
use App\Models\User;

class Payment
{

    private $payer;
    private $amount;
    private $receiver;
    private $group;

    public function __construct(User $payer, $amount)
    {
        $this->payer = $payer;
        $this->amount = $amount;
    }

    public function to(User $receiver)
    {
        $this->receiver = $receiver;

        return $this;
    }

    public function in(Group $group)
    {
        $this->group = $group;

        return $this;
    }

    public function save()
    {
        $myDebts = $this->payer->debts_to_pay()
            ->where('to_id', $this->receiver->id)
            ->where('group_id', $this->group->id)
            ->whereNull('paid_at')
            ->get();

        $myDebts->each(function (Debt $debt) {
            if ($this->amount < $debt->amount) {
                $debt->amount -= $this->amount;
            }  else {
                $debt->paid_at = date('Y-m-d H:i:s');
            }

            $this->amount -= $debt->amount;

            $debt->save();

            if ($this->amount <= 0) {
                return false;
            }
        });

        if ($this->amount <= 0) {
            return;
        }

        $previousDebt = $this->payer->debts_to_receive()
            ->where('from_id', $this->receiver->id)
            ->where('group_id', $this->group->id)
            ->whereNull('paid_at')
            ->first();

        if ($previousDebt) {
            $previousDebt->amount += $this->amount;
            $previousDebt->save();
            return;
        }

        $debt = new Debt();
        $debt->from_id = $this->receiver->id;
        $debt->to_id = $this->payer->id;
        $debt->amount = $this->amount;
        $debt->group_id = $this->group->id;
        $debt->save();
    }
}