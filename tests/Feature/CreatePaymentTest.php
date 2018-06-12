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

    public function setUp()
    {
        parent::setUp();

        $this->me = factory(User::class)->create(['telegram_id' => 'han_solo', 'username' => 'hansolo']);
        $this->creditor = factory(User::class)->create(['telegram_id' => 'jabba_the_hutt', 'username' => 'jabbathehutt']);
        $this->group = factory(Group::class)->create(['telegram_id' => '789', 'type' => 'group', 'title' => 'Cantina']);
        $this->debt = factory(Debt::class)->create(['from_id' => $this->me->id, 'to_id' => $this->creditor->id, 'group_id' => $this->group->id, 'amount' => 30]);

        $this->creditor->addToGroup($this->group);
    }

    /** @test */
    public function can_pay_a_debt_and_it_is_marked_as_paid()
    {
        factory(Debt::class)->create([
            'from_id' => $this->me->id,
            'to_id' => $this->creditor->id,
            'group_id' => $this->group->id,
            'amount' => 5,
        ]);

        $this->bot->setUser(['id' => 'han_solo', 'username' => 'hansolo'])
            ->receives('I paid 30 to @jabbathehutt', $this->getGroupPayload($this->group))
            ->assertReply('Got it!');


        $this->debt->refresh();

        $this->assertNotNull($this->debt->paid_at);

        $this->assertGreaterThan(now()->subHour(1), $this->debt->paid_at);

        $this->assertDatabaseHas('debts', ['amount' => 5, 'paid_at' => null]);
    }

    /** @test */
    public function can_pay_a_debt_and_it_creates_another_debt_in_reverse_origin()
    {

        $this->bot->setUser(['id' => 'han_solo', 'username' => 'hansolo'])
            ->receives('I paid 40 to @jabbathehutt', $this->getGroupPayload($this->group))
            ->assertReply('Got it!');

        $this->debt->refresh();

        $this->assertNotNull($this->debt->paid_at);

        $this->assertGreaterThan(now()->subHour(1), $this->debt->paid_at);

        $this->assertDatabaseHas('debts', [
            'from_id' => $this->creditor->id,
            'to_id' => $this->me->id,
            'group_id' => $this->group->id,
            'amount' => 10,
            'paid_at' => null,
        ]);
    }

    /** @test */
    public function can_pay_a_debt_and_it_discounts_the_paid_amount()
    {

        $this->bot->setUser(['id' => 'han_solo', 'username' => 'hansolo'])
            ->receives('I paid 28 to @jabbathehutt', $this->getGroupPayload($this->group))
            ->assertReply('Got it!');

        $this->debt->refresh();

        $this->assertNull($this->debt->paid_at);

        $this->assertEquals(2, $this->debt->amount);
    }
}
