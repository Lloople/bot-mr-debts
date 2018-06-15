<?php

$botman->hears('Hola|/hola|👋', function ($bot) {
    $bot->reply('Hola! 👋');
});

$botman->hears("[li dec|dec] ([0-9]+) a @([^\s]+)", function ($bot, $amount, $username) {
    return app(App\Http\Controllers\DebtsController::class)->createFromMe($bot, $amount, $username, 'ca');
});
$botman->hears("@([^\s]+) [em deu|hem deu|deu] ([0-9]+)", function ($bot, $username, $amount) {
    return app(App\Http\Controllers\DebtsController::class)->createFromOthers($bot, $username, $amount, 'ca');
});

$botman->hears("[li he pagat|he pagat|pago] ([0-9]+) a @([^s]+)", function ($bot, $amount, $username) {
    return app(App\Http\Controllers\PaymentsController::class)->createFromMe($bot, $amount, $username, 'ca');
});
$botman->hears("@([^\s]+) [m'ha pagat|ha pagat|em paga] ([0-9]+)", function ($bot, $username, $amount) {
    return app(App\Http\Controllers\PaymentsController::class)->createFromOthers($bot, $username, $amount, 'ca');
});

$botman->hears("/resum|💰|💵", function ($bot) {
    return app(App\Http\Controllers\DebtsController::class)->index($bot, 'ca');
});