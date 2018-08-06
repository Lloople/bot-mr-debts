<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegisterGroupTest extends TestCase
{

    use RefreshDatabase;

    /** @test */
    public function can_register_new_group_from_group_chat_created()
    {
        $this->registerNewGroup('group_chat_created', $this->getNewGroupPayload());
    }

    /** @test */
    public function can_register_new_group_from_new_chat_members()
    {
        $this->registerNewGroup('new_chat_members', $this->getNewChatMemberPayload());
    }

    private function registerNewGroup($eventName, $eventPayload)
    {
        $this->bot
            ->setUser($this->getUserPayload())
            ->receivesEvent($eventName, $eventPayload)
            ->assertReply(trans('groups.new_group_greetings'))
            ->assertQuestion(trans('groups.ask_language'))
            ->receivesInteractiveMessage('es')
            ->assertQuestion(trans('groups.ask_currency'))
            ->receivesInteractiveMessage('eur')
            ->assertSay(trans('groups.new_group_setted'));
    }

    private function getUserPayload()
    {
        $me = factory(User::class)->create();

        return ['id' => $me->telegram_id, 'username' => $me->username, 'language_code' => 'es'];
    }

    private function getNewGroupPayload()
    {
        return [
            'message_id' => 344,
            'from' => [
                'id' => 4256522,
                'is_bot' => false,
                'first_name' => 'David',
                'last_name' => 'Llop',
                'username' => 'Lloople',
                'language_code' => 'es',
            ],
            'chat' => [
                'id' => -303757137,
                'title' => 'dfdfsdfs',
                'type' => 'group',
                'all_members_are_administrators' => true,
            ],
            'date' => 1532976465,
            'group_chat_created' => true,
        ];
    }

    private function getNewChatMemberPayload()
    {
        return [
            [
                'id' => config('telegram.bot.id'),
                'is_bot' => true,
                'first_name' => 'Bot',
                'last_name' => 'Money Tracking',
                'username' => config('telegram.bot.username'),
                'language_code' => 'es',
            ],
        ];
    }

}
