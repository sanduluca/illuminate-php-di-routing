<?php

use GGbear\Routing\ExceptionHandler;
use GGbear\Routing\Middleware\BasicAuthentication;
use GGbear\Routing\Middleware\TokenAuthentication;
use Illuminate\Http\Request;
use Psr\Container\ContainerInterface;

use function DI\factory;

return [

    \Illuminate\Contracts\Debug\ExceptionHandler::class => factory(
        function (ContainerInterface $c) {
            return $c->get(ExceptionHandler::class);
        }
    ),
    BasicAuthentication::class => factory(
        function () {
            return new BasicAuthentication([
                'secret' => 'secret'
            ]);
        }
    ),
    TokenAuthentication::class => factory(
        function () {
            return new TokenAuthentication('fdsvcbd4yequ56fdsf34fsf3436m679fkf', ['HS256']);
        }
    ),
    Request::class => \Di\value(Request::capture()),
];
