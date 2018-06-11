<?php

$botman = resolve('botman');

$botman->middleware->received(new \App\Http\Middleware\Botman\LoadUserMiddleware());

$botman->hears('Hi|/hi', function ($bot) {
    $bot->reply('Hello!');
});

$botman->hears("I owe ([0-9]+) to @([^\s]+)", 'App\Http\Controllers\DebtsController@createPayment');

$botman->hears("@([^\s]+) owes me ([0-9]+)", 'App\Http\Controllers\DebtsController@createCharge');

$botman->hears("/debts", "App\Http\Controllers\DebtsController@index");