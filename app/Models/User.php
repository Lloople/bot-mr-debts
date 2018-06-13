<?php

namespace App\Models;

use App\PaymentFactory;
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

    public function debts_to_pay()
    {
        return $this->hasMany(Debt::class, 'from_id');
    }

    public function debts_to_receive()
    {
        return $this->hasMany(Debt::class, 'to_id');
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
            $user->telegram_id = $botUser->getId();
            $user->name = $botUser->getFirstName() ?? $botUser->getId();
            $user->username = $botUser->getUsername();
            $user->email = $botUser->getId() . '@money-tracking.com';
            $user->password = Hash::make($botUser->getUsername() . '-money-tracking');
            $user->save();
        }

        return $user;
    }

    public function pays($amount)
    {
        return new PaymentFactory($this, $amount);
    }
}
