<?php

require_once './../vendor/autoload.php';

use GGbear\Routing\Events\Dispatcher;
use GGbear\Routing\Pipeline;
use GGbear\Routing\Router;
use Illuminate\Http\Request;
use DI\ContainerBuilder;
use GGbear\Routing\Middleware\BasicAuthentication;

// Create a service container
$containerBuilder = new ContainerBuilder();
$containerBuilder->addDefinitions(__DIR__ . '/config.php');
$container = $containerBuilder->build();

$request = $container->get(Request::class);

$events = new Dispatcher($container);

// Create the router instance
$router = new Router($events, $container);

// Load the routes
// require_once '/path/to/file/api.php';
$router->group(['middleware' =>  BasicAuthentication::class], function (Router $router) {
    $router->get('/basic', function () {
        return ['success' => true];
    });
});
$router->get('/test', function () {
    return ['success' => true];
});

// $globalMiddleware = [];

$response = (new Pipeline($container))
    ->send($request)
    // ->through($globalMiddleware)
    ->then(function ($request) use ($router) {
        return $router->dispatch($request);
    });

// Send the response back to the browser
$response->send();
