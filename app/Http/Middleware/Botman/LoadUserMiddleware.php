<?php

namespace App\Http\Middleware\Botman;

use App\Exceptions\MissingGroupException;
use App\Models\Group;
use App\Models\User;
use BotMan\BotMan\BotMan;
use BotMan\BotMan\Interfaces\Middleware\Received;
use BotMan\BotMan\Messages\Incoming\IncomingMessage;

class LoadUserMiddleware implements Received
{

    /**
     * @param \BotMan\BotMan\Messages\Incoming\IncomingMessage $message
     * @param callable $next
     * @param \BotMan\BotMan\BotMan $bot
     *
     * @return mixed
     * @throws \App\Exceptions\MissingGroupException
     * @throws \BotMan\BotMan\Exceptions\Base\BotManException
     */
    public function received(IncomingMessage $message, $next, BotMan $bot)
    {
        $user = User::findOrCreateTelegram($bot->getDriver()->getUser($message));

        auth()->login($user);

        $group = Group::where('telegram_id', collect($message->getPayload())->get('chat')['id'])->first();

        if ($group) {
            $user->addToGroup($group);

            $user->group = $group;

        } elseif (! $this->isRegisteringGroup($message)) {
            $bot->say(trans('groups.first_register'), $message->getRecipient());

            throw new MissingGroupException();
        }

        return $next($message);
    }

    private function isRegisteringGroup(IncomingMessage $message)
    {
        dump($message);
        $payload = collect($message->getPayload());

        return $payload->contains('new_chat_members')
            || $payload->contains('group_chat_created')
            || $message->getText() === '/register'
            || $payload->get('from')['is_bot'];
    }
}
