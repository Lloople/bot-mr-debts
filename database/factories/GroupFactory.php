<?php

use Faker\Generator as Faker;

$factory->define(App\Models\Group::class, function (Faker $faker) {
    return [
        'telegram_id' => $faker->creditCardNumber,
        'type' => 'group',
        'title' => $faker->jobTitle
    ];
});
