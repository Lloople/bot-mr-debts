<?php

$botman->hears("[le debo|debo] ([0-9]+) a @([^\s]+)", function ($bot, $amount, $username) {
    return app(App\Http\Controllers\DebtsController::class)->createFromMe($bot, $amount, $username, 'es');
});
$botman->hears("@([^\s]+) [me debe|debe] ([0-9]+)", function ($bot, $username, $amount) {
    return app(App\Http\Controllers\DebtsController::class)->createFromOthers($bot, $username, $amount, 'es');
});

$botman->hears("[le he pagado|le pago|pago|he pagado] ([0-9]+) a @([^s]+)", function ($bot, $amount, $username) {
    return app(App\Http\Controllers\PaymentsController::class)->createFromMe($bot, $amount, $username, 'es');
});
$botman->hears("@([^\s]+) [me ha pagado|ha pagado|me paga] ([0-9]+)", function ($bot, $username, $amount) {
    return app(App\Http\Controllers\PaymentsController::class)->createFromOthers($bot, $username, $amount, 'es');
});

$botman->hears("/resumen|💰|💵", function ($bot) {
    return app(App\Http\Controllers\DebtsController::class)->index($bot, 'es');
});