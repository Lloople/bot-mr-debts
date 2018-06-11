<?php

namespace Tests;

use App\BotManTester;
use BotMan\BotMan\BotMan;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{

    use CreatesApplication;

    /**
     * @var BotMan
     */
    protected $botman;

    /**
     * @var BotManTester
     */
    protected $bot;

    public function getGroupPayload($group = null)
    {
        return collect([
            'chat' => $group
                ? ['id' => $group->telegram_id, 'type' => $group->type, 'title' => $group->title]
                : ['id' => '789', 'type' => 'group', 'title' => 'Testing Group'],
        ]);
    }
}
