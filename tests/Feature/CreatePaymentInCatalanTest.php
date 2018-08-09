<?php

namespace Tests\Feature;

use App\Models\Debt;
use App\Models\Group;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreatePaymentInCatalanTest extends TestCase
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

        app()->setLocale('en');
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
            ->receives('he pagat 30 a @jabbathehutt', $this->getGroupPayload($this->group))
            ->assertReply(trans('debts.add.payment'));

        $this->assertDatabaseMissing('debts', ['amount' => 30]);

        $this->assertDatabaseHas('debts', ['amount' => 5, 'currency' => 'eur']);
    }

    /** @test */
    public function can_pay_a_debt_and_it_creates_another_debt_in_reverse_origin()
    {
        factory(Debt::class)->create([
            'from_id' => $this->creditor->id,
            'to_id' => $this->me->id,
            'group_id' => $this->group->id,
            'amount' => 5,
        ]);

        $this->bot->setUser(['id' => 'han_solo', 'username' => 'hansolo'])
            ->receives('he pagat 40 a @jabbathehutt', $this->getGroupPayload($this->group))
            ->assertReply(trans('debts.add.payment'));

        $this->assertDatabaseMissing('debts', ['amount' => 30]);

        $this->assertDatabaseHas('debts', [
            'from_id' => $this->creditor->id,
            'to_id' => $this->me->id,
            'group_id' => $this->group->id,
            'amount' => 15,
        ]);
    }

    /** @test */
    public function can_pay_a_debt_and_it_discounts_the_paid_amount()
    {

        $this->bot->setUser(['id' => 'han_solo', 'username' => 'hansolo'])
            ->receives('he pagat 28 a @jabbathehutt', $this->getGroupPayload($this->group))
            ->assertReply(trans('debts.add.payment'));

        $this->debt->refresh();

        $this->assertEquals(2, $this->debt->amount);
    }
}
