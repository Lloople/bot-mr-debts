<?php

namespace App\Http\Middleware\Botman;

use App\Conversations\RegisterGroupConversation;
use App\Exceptions\MissingGroupException;
use App\Exceptions\InteractingWithBotException;
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
     * @throws \App\Exceptions\InteractingWithBotException
     */
    public function received(IncomingMessage $message, $next, BotMan $bot)
    {
        $user = User::findOrCreateTelegram($bot->getDriver()->getUser($message));

        auth()->login($user);

        $group = Group::where('telegram_id', collect($message->getPayload())->get('chat')['id'])->first();

        if ($group) {
            $user->addToGroup($group);

            $user->group = $group;

            app()->setLocale($group->language);

        } elseif (! $this->isRegisteringGroup($message, $bot)) {
            $bot->say(trans('groups.first_register'), $message->getRecipient());

            throw new MissingGroupException();
        }

        if (strpos($message->getText(), '@'.config('botman.telegram.bot.username')) !== false) {

            $bot->say(trans('debts.you_cannot_debt_to_bot'), $message->getRecipient());

            throw new InteractingWithBotException();
        }
        return $next($message);
    }

    private function isRegisteringGroup(IncomingMessage $message, BotMan $bot)
    {
        $payload = collect($message->getPayload());

        $conversation = $bot->getStoredConversation($message);

        return $payload->contains('new_chat_members')
            || $payload->contains('group_chat_created')
            || $message->getText() === '/register'
            || ($conversation && $conversation['conversation'] instanceof RegisterGroupConversation);
    }
}
