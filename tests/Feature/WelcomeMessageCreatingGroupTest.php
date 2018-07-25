<?php

namespace Tests\Feature;

use App\Models\User;
use BotMan\Drivers\Telegram\TelegramDriver;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WelcomeMessageCreatingGroupTest extends TestCase
{

    use RefreshDatabase;

    /** @test */
    public function can_listen_for_creating_group_event()
    {
        $me = factory(User::class)->create();

        $this->bot->setDriver(TelegramDriver::class)
            ->setUser(['id' => $me->telegram_id, 'username' => $me->username, 'language_code' => 'es'])
            ->receives('', collect([
                'group_chat_created' => true,
                'new_chat_members' => [
                    [
                        'is_bot' => false,
                        'first_name' => 'Han',
                        'last_name' => 'Solo',
                        'username' => 'han_solo',
                        'id' => uniqid(),
                        'language_code' => 'es',
                    ],
                ],
                'chat' => [
                    'id' => 'starwars',
                    'type' => 'group',
                    'title' => 'Star Wars Heroes'
                ]
            ]))
            ->assertReply(trans('groups.new_group_greetings'));
    }

}
