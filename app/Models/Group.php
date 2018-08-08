<?php

namespace App\Models;

use App\Traits\HasCurrency;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{

    use HasCurrency;

    const UPDATED_AT = null;

    protected $guarded = [];

    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    public static function updateOrCreateFromChat($chat, string $language = 'es', string $currency = 'eur')
    {
        $group = self::updateOrCreate([
            'telegram_id' => $chat['id'],
            'type' => $chat['type'],
        ], [
            'title' => $chat['title'],
            'language' => $language,
            'currency' => $currency,
        ]);

        return $group;
    }

}
