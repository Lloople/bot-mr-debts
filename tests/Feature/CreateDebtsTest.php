<?php

namespace Tests\Feature;

use App\Models\Debt;
use App\Models\Group;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreateDebtsTest extends TestCase
{

    use RefreshDatabase;

    /** @test */
    public function i_can_owe_money_to_someone()
    {
        $me = factory(User::class)->create(['telegram_id' => 'han_solo', 'username' => 'hansolo']);
        $creditor = factory(User::class)->create(['telegram_id' => 'jabba_the_hutt', 'username' => 'jabbathehutt']);
        $group = factory(Group::class)->create(['telegram_id' => '789', 'type' => 'group', 'title' => 'Cantina']);

        $creditor->addToGroup($group);

        $this->bot->setUser(['id' => 'han_solo', 'username' => 'hansolo'])
            ->receives('I owe 100 to @jabbathehutt', $this->getGroupPayload())
            ->assertReply('Got it! you shall pay that debt as soon as possible');

        $this->assertDatabaseHas('debts', [
            'from_id' => $me->id,
            'to_id' => $creditor->id,
            'amount' => 100,
        ]);
    }

    /** @test */
    public function i_cannot_owe_money_to_a_no_registered_user()
    {
        factory(User::class)->create(['telegram_id' => 'han_solo', 'username' => 'hansolo']);

        $this->bot->setUser(['id' => 'han_solo', 'username' => 'hansolo'])
            ->receives('I owe 100 to @jabbathehutt', $this->getGroupPayload())
            ->assertReply('Sorry, I don\'t know who @jabbathehutt is.');
    }

    /** @test */
    public function someone_can_owe_me_money()
    {
        $me = factory(User::class)->create(['telegram_id' => 'jabba_the_hutt', 'username' => 'jabbathehutt']);
        $creditor = factory(User::class)->create(['telegram_id' => 'han_solo', 'username' => 'hansolo']);
        $group = factory(Group::class)->create(['telegram_id' => '789', 'type' => 'group', 'title' => 'Cantina']);

        $creditor->addToGroup($group);

        $this->bot->setUser(['id' => 'jabba_the_hutt', 'username' => 'jabbathehutt'])
            ->receives('@hansolo owes me 100', $this->getGroupPayload())
            ->assertReply('Got it!');

        $this->assertDatabaseHas('debts', [
            'from_id' => $creditor->id,
            'to_id' => $me->id,
            'amount' => 100,
        ]);
    }

    /** @test */
    public function someone_cannot_owe_me_money_if_its_not_registered()
    {
        factory(User::class)->create(['telegram_id' => 'jabba_the_hutt', 'username' => 'jabbathehutt']);

        $this->bot->setUser(['id' => 'jabba_the_hutt', 'username' => 'jabbathehutt'])
            ->receives('@hansolo owes me 100', $this->getGroupPayload())
            ->assertReply('Sorry, I don\'t know who @hansolo is.');

    }
}
