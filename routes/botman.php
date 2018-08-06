<?php

$botman = resolve('botman');

$botman->middleware->received(new \App\Http\Middleware\Botman\LoadUserMiddleware());

include 'botman.en.php';
include 'botman.es.php';
include 'botman.ca.php';

$botman->hears('/register', 'App\Http\Controllers\GroupsController@register');
$botman->on('group_chat_created', 'App\Http\Controllers\GroupsController@registerNewGroup');
$botman->on('new_chat_members', 'App\Http\Controllers\GroupsController@registerNewChatMember');

$botman->fallback('App\Http\Controllers\FallbackController@index');