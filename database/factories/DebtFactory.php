<?php

use App\Models\Group;
use App\Models\User;
use Faker\Generator as Faker;

$factory->define(App\Models\Debt::class, function (Faker $faker) {
    return [
        'from_id' => factory(User::class)->create(),
        'to_id' => factory(User::class)->create(),
        'group_id' => factory(Group::class)->create(),
        'amount' => random_int(0, 200)
    ];
});
