<?php

$botman->hears('Hi|/hi', function ($bot) {
    $bot->reply('Hello! 👋');
});

// ENGLISH
$botman->hears("I owe ([0-9]+) to @([^\s]+)", function ($bot, $amount, $username) {
    return app(App\Http\Controllers\DebtsController::class)->createFromMe($bot, $amount, $username, 'en');
});
$botman->hears("@([^\s]+) owes me ([0-9]+)", function ($bot, $username, $amount) {
    return app(App\Http\Controllers\DebtsController::class)->createFromOthers($bot, $username, $amount, 'en');
});

$botman->hears("I paid ([0-9]+) to @([^s]+)", function ($bot, $amount, $username) {
    return app(App\Http\Controllers\PaymentsController::class)->createFromMe($bot, $amount, $username, 'en');
});
$botman->hears("@([^\s]+) paid me ([0-9]+)", function ($bot, $username, $amount) {
    return app(App\Http\Controllers\PaymentsController::class)->createFromOthers($bot, $username, $amount, 'en');
});

$botman->hears("/balance|💰|💵", function ($bot) {
    return app(App\Http\Controllers\DebtsController::class)->index($bot, 'en');
});