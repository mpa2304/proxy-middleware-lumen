<?php

$router->group(['middleware' => ['cors', 'auth']], function () use ($router) {
	// generic route, several http verbs
	$uri = '/proxy';
	$callback = 'ProxyController@request';
    $router->get($uri, $callback);
	$router->post($uri, $callback);
	$router->put($uri, $callback);
	$router->patch($uri, $callback);
	$router->delete($uri, $callback);
	$router->options($uri, $callback);
});

