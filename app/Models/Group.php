<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Group extends Model
{

    const UPDATED_AT = null;

    protected $guarded = [];

    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    public static function findOrCreateTelegram($chat)
    {
        $group = self::where('telegram_id', $chat['id'])->first();

        if (! $group) {
            $group = new self;
            $group->telegram_id = $chat['id'];
            $group->title = $chat['title'];
            $group->type = $chat['type'];
            $group->language = 'es';
            $group->currency = 'eur';
            $group->save();
        }

        return $group;
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
