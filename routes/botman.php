<?php

$botman = resolve('botman');

$botman->middleware->received(new \App\Http\Middleware\Botman\LoadUserMiddleware());

$botman->hears('Hi|/hi|Hola|👋', function ($bot) {
    $bot->reply('Hola! 👋');
});

$botman->hears(
    '(?|'.implode('|', [
        'en' => 'I owe ([0-9]+) to @([^\s]+)',
        'ca' => '(?:(?:li dec)|(?:dec)) ([0-9]+) a @([^\s]+)',
        'es' => '(?:(?:le debo)|(?:debo)) ([0-9]+) a @([^\s]+)',
    ]).')',
    'App\Http\Controllers\DebtsController@createFromMe'
);

$botman->hears(
    '(?|'.implode('|', [
        'en' => '@([^\s]+) owes me ([0-9]+)',
        'ca' => '@([^\s]+) (?:(?:em deu)|(?:hem deu)|(?:deu)) ([0-9]+)',
        'es' => '@([^\s]+) (?:(?:me debe)|(?:debe)) ([0-9]+)',
    ]).')',
    'App\Http\Controllers\DebtsController@createFromOthers'
);


$botman->hears(
    '(?|'.implode('|', [
        'en' => 'I paid ([0-9]+) to @([^\s]+)',
        'ca' => '(?:(?:li he pagat)|(?:he pagat)|(?:pago)) ([0-9]+) a @([^\s]+)',
        'es' => '(?:(?:le he pagado)|(?:le pago)|(?:he pagado)) ([0-9]+) a @([^\s]+)',
    ]).')',
    'App\Http\Controllers\PaymentsController@createFromMe'
);

$botman->hears(
    implode('|', [
        'en' => '@([^\s]+) paid me ([0-9]+)',
        'ca' => '@([^\s]+) (?:(?:m\'ha pagat)|(?:ha pagat)|(?:em paga)|(?:paga)) ([0-9]+)',
        'es' => '@([^\s]+) (?:(?:me ha pagado)|(?:ha pagado)|(?:me paga)) ([0-9]+)'
    ]),
    'App\Http\Controllers\PaymentsController@createFromOthers'
);

$botman->hears('/debt ([0-9]+) @([^\s]+)', function ($bot, $amount, $username) {
    return app(App\Http\Controllers\DebtsController::class)->createFromMe($bot, $amount, $username);
});
$botman->hears('/paid ([0-9]+) @([^\s]+)', function ($bot, $amount, $username) {
    return app(App\Http\Controllers\DebtsController::class)->createFromOthers($bot, $username, $amount);
});

$botman->hears('/balance|/resum|/resumen|💰|💵', 'App\Http\Controllers\DebtsController@index');

$botman->hears('/register', 'App\Http\Controllers\GroupsController@register');
$botman->on('group_chat_created', 'App\Http\Controllers\GroupsController@registerNewGroup');
$botman->on('new_chat_members', 'App\Http\Controllers\GroupsController@registerNewChatMember');