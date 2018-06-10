<?php

namespace Tests\Unit;

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

        $debtor->owes(100)->to($creditor);

        $this->assertDatabaseHas('debts', [
            'from_id' => $debtor->id,
            'to_id' => $creditor->id,
            'amount' => 100
        ]);
    }
}
