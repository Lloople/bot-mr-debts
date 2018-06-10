<?php

namespace App\Models;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Hash;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'name', 'email', 'password',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];


    public static function findByUsernameOrFail($username)
    {
       $user = self::where('username', $username)->first();

       if (! $user) {
           throw new ModelNotFoundException('User not found.');
       }

       return $user;
    }

    public static function findOrCreate($botUser)
    {
        return self::firstOrCreate(
            [
                'telegram_id' => $botUser->getId()
            ],
            [
                'name' => $botUser->getFirstName() ?? $botUser->getId(),
                'surname' => $botUser->getLastName(),
                'username' => $botUser->getUsername(),
                'email' => $botUser->getId().'@money-tracking.com',
                'password' => Hash::make($botUser->getUsername().'-money-tracking')
            ]
        );
    }

    public function owes($amount)
    {
        $transaction = new Debt();
        $transaction->from_id = $this->id;
        $transaction->amount = $amount;

        return $transaction;
    }
}
