<?php

$botman = resolve('botman');

$botman->middleware->received(new \App\Http\Middleware\Botman\LoadUserMiddleware());

include 'botman.en.php';
include 'botman.es.php';
include 'botman.ca.php';