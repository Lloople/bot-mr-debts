<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasCurrency;

class Debt extends Model
{

    use HasCurrency;

    const UPDATED_AT = null;

    public function debtor()
    {
        return $this->belongsTo(User::class, 'from_id');
    }

    public function creditor()
    {
        return $this->belongsTo(User::class, 'to_id');
    }

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function getAmountFormattedAttribute()
    {
        return str_replace(',00', '', number_format($this->amount, 2, ',', '.')) . " {$this->currency_symbol}";
    }

    public function toStringFromDebtor()
    {
        return trans('debts.you_have_to_pay', ['amount' => $this->amount_formatted, 'username' => $this->creditor->username]);
    }

    public function toStringFromCreditor()
    {
        return trans('debts.you_have_to_receive', ['amount' => $this->amount_formatted, 'username' => $this->debtor->username]);
    }
}
