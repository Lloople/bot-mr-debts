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

    public static function createFromChat($chat, string $language = 'es', string $currency = 'eur')
    {
        $group = new self();
        $group->telegram_id = $chat['id'];
        $group->title = $chat['title'];
        $group->type = $chat['type'];
        $group->language = $language;
        $group->currency = $currency;
        $group->save();

        return $group;
    }

}
