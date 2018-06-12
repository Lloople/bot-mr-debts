<?php

namespace Tests\Feature;

use App\Models\Debt;
use App\Models\Group;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreatePaymentTest extends TestCase
{

    use RefreshDatabase;

    /** @test */
    public function can_pay_a_debt_and_it_is_marked_as_paid()
    {
        $me = factory(User::class)->create(['telegram_id' => 'han_solo', 'username' => 'hansolo']);
        $creditor = factory(User::class)->create(['telegram_id' => 'jabba_the_hutt', 'username' => 'jabbathehutt']);
        $group = factory(Group::class)->create(['telegram_id' => '789', 'type' => 'group', 'title' => 'Cantina']);

        $debt = factory(Debt::class)->create([
            'from_id' => $me->id,
            'to_id' => $creditor->id,
            'group_id' => $group->id,
            'amount' => 30,
        ]);

        factory(Debt::class)->create([
            'from_id' => $me->id,
            'to_id' => $creditor->id,
            'group_id' => $group->id,
            'amount' => 5,
        ]);

        $creditor->addToGroup($group);

        $this->bot->setUser(['id' => 'han_solo', 'username' => 'hansolo'])
            ->receives('I paid 30 to @jabbathehutt', $this->getGroupPayload($group))
            ->assertReply('Got it!');

        $debt->refresh();
        $this->assertNotNull($debt->paid_at);
        $this->assertGreaterThan(now()->subHour(1), $debt->paid_at);
        $this->assertDatabaseHas('debts', [
            'from_id' => $me->id,
            'to_id' => $creditor->id,
            'group_id' => $group->id,
            'amount' => 5,
            'paid_at' => null,
        ]);
    }

    /** @test */
    public function can_pay_a_debt_and_it_creates_another_debt_in_reverse_origin()
    {
        $me = factory(User::class)->create(['telegram_id' => 'han_solo', 'username' => 'hansolo']);
        $creditor = factory(User::class)->create(['telegram_id' => 'jabba_the_hutt', 'username' => 'jabbathehutt']);
        $group = factory(Group::class)->create(['telegram_id' => '789', 'type' => 'group', 'title' => 'Cantina']);

        $debt = factory(Debt::class)->create([
            'from_id' => $me->id,
            'to_id' => $creditor->id,
            'group_id' => $group->id,
            'amount' => 30,
        ]);

        $creditor->addToGroup($group);

        $this->bot->setUser(['id' => 'han_solo', 'username' => 'hansolo'])
            ->receives('I paid 40 to @jabbathehutt', $this->getGroupPayload($group))
            ->assertReply('Got it!');

        $debt->refresh();
        $this->assertNotNull($debt->paid_at);
        $this->assertGreaterThan(now()->subHour(1), $debt->paid_at);

        $this->assertDatabaseHas('debts', [
            'from_id' => $creditor->id,
            'to_id' => $me->id,
            'group_id' => $group->id,
            'amount' => 10,
            'paid_at' => null,
        ]);
    }

    /** @test */
    public function can_pay_a_debt_and_it_discounts_the_paid_amount()
    {
        $me = factory(User::class)->create(['telegram_id' => 'han_solo', 'username' => 'hansolo']);
        $creditor = factory(User::class)->create(['telegram_id' => 'jabba_the_hutt', 'username' => 'jabbathehutt']);
        $group = factory(Group::class)->create(['telegram_id' => '789', 'type' => 'group', 'title' => 'Cantina']);

        $debt = factory(Debt::class)->create([
            'from_id' => $me->id,
            'to_id' => $creditor->id,
            'group_id' => $group->id,
            'amount' => 30,
        ]);

        $creditor->addToGroup($group);

        $this->bot->setUser(['id' => 'han_solo', 'username' => 'hansolo'])
            ->receives('I paid 28 to @jabbathehutt', $this->getGroupPayload($group))
            ->assertReply('Got it!');

        $debt->refresh();
        $this->assertNull($debt->paid_at);
        $this->assertEquals(2, $debt->amount);
    }
}
