<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Debt extends Model
{

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
        return str_replace(',00', '', number_format($this->amount, 2, ',', '.'));
    }

    public function toStringFromDebtor()
    {
        return "You have to pay {$this->amount_formatted} to @{$this->creditor->username}";
    }

    public function toStringFromCreditor()
    {
        return "You have to receive {$this->amount_formatted} from @{$this->debtor->username}";
    }
}
