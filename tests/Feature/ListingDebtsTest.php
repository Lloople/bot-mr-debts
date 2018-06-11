<?php

namespace Tests\Feature;

use App\Models\Debt;
use App\Models\Group;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ListingDebtsTest extends TestCase
{

    use RefreshDatabase;

    /** @test */
    public function i_can_see_all_my_debts_in_a_group()
    {
        $me = factory(User::class)->create();
        $group = factory(Group::class)->create();

        $debt1 = factory(Debt::class)->create(['amount' => 100, 'to_id' => $me->id, 'group_id' => $group->id]);
        $debt2 = factory(Debt::class)->create(['amount' => 102, 'from_id' => $me->id, 'group_id' => $group->id]);

        $debt1->refresh();
        $debt2->refresh();

        $me->addToGroup($group);
        $debt1->debtor->addToGroup($group);
        $debt2->creditor->addToGroup($group);

        $this->bot->setUser(['id' => $me->telegram_id, 'username' => $me->username])
            ->receives('/balance', $this->getGroupPayload($group))
            ->assertReply("You have to pay {$debt2->amount} to @{$debt2->creditor->username}<br>You have to receive {$debt1->amount} from @{$debt1->debtor->username}");
    }
}
