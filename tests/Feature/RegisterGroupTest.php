<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegisterGroupTest extends TestCase
{

    use RefreshDatabase;

    /** @test */
    public function can_listen_for_creating_group_event()
    {
        $me = factory(User::class)->create();

        $this->bot
            ->setUser(['id' => $me->telegram_id, 'username' => $me->username, 'language_code' => 'es'])
            ->receivesEvent('group_chat_created', [
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
            ])
            ->assertReply(trans('groups.new_group_greetings'));
    }

    /** @test */
    public function can_listen_for_adding_bot_to_a_group_event()
    {
        $me = factory(User::class)->create();

        $this->bot
            ->setUser(['id' => $me->telegram_id, 'username' => $me->username, 'language_code' => 'es'])
            ->receivesEvent('new_chat_members', [
                [
                    'id' => config('telegram.bot.id'),
                    'is_bot' => true,
                    'first_name' => 'Bot',
                    'last_name' => 'Money Tracking',
                    'username' => config('telegram.bot.username'),
                    'language_code' => 'es',
                ],
            ])
            ->assertReply(trans('groups.new_group_greetings'));
    }

}
