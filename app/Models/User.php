<?php

namespace App\Models;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;

class User extends Authenticatable
{

    use Notifiable;

    protected $guarded = [];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function groups()
    {
        return $this->belongsToMany(Group::class);
    }

    public function charges()
    {
        return $this->hasMany(Debt::class, 'to_id');
    }

    public function payments()
    {
        return $this->hasMany(Debt::class, 'from_id');
    }

    public function addToGroup($group)
    {
        $this->groups()->sync([$group->id], false);
    }

    public static function findByUsernameOrFail($username)
    {
        $user = self::where('username', $username)->first();

        if (! $user) {
            throw new ModelNotFoundException('User not found.');
        }

        return $user;
    }

    /**
     * @param $botUser
     *
     * @return \App\Models\User
     */
    public static function findOrCreateTelegram($botUser)
    {
        $user = self::where('telegram_id', $botUser->getId())->first();

        if (! $user) {
            $user = new self;
            $user->name = $botUser->getFirstName() ?? $botUser->getId();
            $user->username = $botUser->getUsername();
            $user->email = $botUser->getId() . '@money-tracking.com';
            $user->password = Hash::make($botUser->getUsername() . '-money-tracking');
            $user->save();
        }

        return $user;
    }

    public function owes($amount)
    {
        $transaction = new Debt();
        $transaction->from_id = $this->id;
        $transaction->amount = $amount;

        return $transaction;
    }

    public function owingTo($creditor, $group = null)
    {
        $debts = $this->payments()->where('to_id', $creditor->id);

        if ($group) {
            $debts->where('group_id', $group->id);
        }

        return $debts->get();
    }
}
