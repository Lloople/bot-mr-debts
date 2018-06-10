<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Group extends Model
{

    protected $guarded = [];

    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    public static function findOrCreateTelegram($botChat)
    {
        $group = self::where('telegram_id', $botChat->id)->first();

        if (! $group) {
            $group = new self;
            $group->telegram_id = $botChat->id;
            $group->title = $botChat->title;
            $group->type = $botChat->type;
            $group->save();
        }

        return $group;
    }
}
