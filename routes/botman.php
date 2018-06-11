<?php

$botman = resolve('botman');

$botman->middleware->received(new \App\Http\Middleware\Botman\LoadUserMiddleware());

$botman->hears('Hi|/hi', function ($bot) {
    $bot->reply('Hello!');
});

$botman->hears("I owe ([0-9]+) to @([^\s]+)", 'App\Http\Controllers\DebtsController@createFromMe');
$botman->hears("@([^\s]+) owes me ([0-9]+)", 'App\Http\Controllers\DebtsController@createFromOthers');

$botman->hears("I paid ([0-9]+) to @([^s]+)", "App\Http\Controllers\PaymentsController@createFromMe");
$botman->hears("@([^\s]+) paid me ([0-9]+)", "App\Http\Controllers\PaymentsController@createFromOthers");

$botman->hears("/balance", "App\Http\Controllers\DebtsController@index");