<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Debt extends Model
{

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

    public function to($user)
    {
        $this->to_id = $user->id;

        return $this;
    }

    public function in($group)
    {
        $this->group_id = $group->id;

        return $this;
    }
}
