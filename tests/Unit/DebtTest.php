<?php

namespace Tests\Unit;

use App\Models\Debt;
use App\Models\Group;
use App\Models\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DebtTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function can_add_a_debt()
    {
        $debtor = factory(User::class)->create(['name' => 'Debtor']);
        $creditor = factory(User::class)->create(['name' => 'Creditor']);

        $debt = $debtor->owes(100)->to($creditor);

        $debt->save();

        $this->assertDatabaseHas('debts', [
            'from_id' => $debtor->id,
            'to_id' => $creditor->id,
            'amount' => 100
        ]);
    }

    /** @test */
    public function get_debts_from_debtor_to_creditor_only_for_one_group()
    {
        $debtor = factory(User::class)->create();
        $creditor = factory(User::class)->create();
        $group = factory(Group::class)->create();
        $debtor->groups()->attach([$group->id]);
        $creditor->groups()->attach([$group->id]);

        factory(Debt::class)->create([
            'from_id' => $debtor->id,
            'to_id' => $creditor->id,
            'amount' => 100
        ]);

        factory(Debt::class)->create([
            'from_id' => $debtor->id,
            'to_id' => $creditor->id,
            'amount' => 100,
            'group_id' => $group->id
        ]);

        $this->assertEquals(100, $debtor->owingTo($creditor, $group)->sum('amount'));
    }
}
