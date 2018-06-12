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
        $debts = $this->payer->debts_to_pay()
            ->where('to_id', $this->receiver->id)
            ->where('group_id', $this->group->id)
            ->whereNull('paid_at')
            ->get();

        $debts->each(function (Debt $debt) {
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

        if ($this->amount) {
            $debt = $this->receiver->owes($this->amount)->to($this->payer)->in($this->group);
            $debt->save();
        }
    }
}