<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class IOweTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function i_can_owe_money_to_someone()
    {
        $me = factory(User::class)->create(['telegram_id' => 'han_solo','username' => 'hansolo']);

        $creditor = factory(User::class)->create(['telegram_id' => 'jabba_the_hutt', 'username' => 'jabbathehutt']);

        $this->bot->setUser(['id' => 'han_solo', 'username' => 'hansolo'])
            ->receives('I owe 100 to @jabbathehutt')
            ->assertReply('Got it! you shall pay that debt as soon as possible');

        $this->assertDatabaseHas('debts', [
            'from_id' => $me->id,
            'to_id' => $creditor->id,
            'amount' => 100,
            'paid_at' => null
        ]);
    }
}
