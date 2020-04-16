<?php

namespace GGbear\Routing;

use DI\Container;
use Illuminate\Http\Request;
use Illuminate\Routing\Events\RouteMatched;
use Illuminate\Routing\MiddlewareNameResolver;
use GGbear\Routing\Events\Dispatcher;

class Router extends \Illuminate\Routing\Router
{

    /**
     * The IoC container instance.
     *
     * @var Container
     */
    protected $container;

    /**
     * The event dispatcher instance.
     *
     * @var Dispatcher
     */
    protected $events;

    /**
     * The route collection instance.
     *
     * @var RouteCollection
     */
    protected $routes;


    /**
     * The currently dispatched route instance.
     *
     * @var Route|null
     */
    protected $current;

    public function __construct(Dispatcher $events, Container $container = null)
    {
        $this->events = $events;
        $this->routes = new RouteCollection;
        $this->container = $container ?: new Container;
    }

    /**
     * Create a new Route object.
     *
     * @param  array|string  $methods
     * @param  string  $uri
     * @param  mixed  $action
     * @return Route
     */
    protected function newRoute($methods, $uri, $action)
    {
        $route = new Route($methods, $uri, $action);
        $route->setRouterDI($this);
        $route->setContainerDI($this->container);
        return $route;
    }

    /**
     * Run the given route within a Stack "onion" instance.
     *
     * @param  Route  $route
     * @param  Request  $request
     * @return mixed
     */
    protected function runRouteWithinStackDI(Route $route, Request $request)
    {
        $shouldSkipMiddleware = $this->container->has('middleware.disable') &&
            $this->container->make('middleware.disable') === true;

        $middleware = $shouldSkipMiddleware ? [] : $this->gatherRouteMiddlewareDI($route);

        return (new Pipeline($this->container))
            ->send($request)
            ->through($middleware)
            ->then(function ($request) use ($route) {
                return $this->prepareResponse(
                    $request,
                    $route->run()
                );
            });
    }

    /**
     * Gather the middleware for the given route with resolved class names.
     *
     * @param  Route  $route
     * @return array
     */
    public function gatherRouteMiddlewareDI(Route $route)
    {
        $middleware = collect($route->gatherMiddleware())->map(function ($name) {
            return (array) MiddlewareNameResolver::resolve($name, $this->middleware, $this->middlewareGroups);
        })->flatten();

        return $this->sortMiddleware($middleware);
    }

    /**
     * Find the route matching a given request.
     *
     * @param  Request  $request
     * @return Route
     */
    protected function findRoute($request)
    {
        $this->current = $route = $this->routes->match($request);

        $this->container->set(Route::class, $route);

        return $route;
    }

    /**
     * Return the response for the given route.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Route  $route
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function runRouteDI(Request $request, Route $route)
    {
        $request->setRouteResolver(function () use ($route) {
            return $route;
        });

        $this->events->dispatch(new RouteMatched($route, $request));

        return $this->prepareResponse(
            $request,
            $this->runRouteWithinStackDI($route, $request)
        );
    }

    /**
     * Dispatch the request to a route and return the response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function dispatchToRoute(Request $request)
    {
        return $this->runRouteDI($request, $this->findRoute($request));
    }

    /**
     * Return the response returned by the given route.
     *
     * @param  string  $name
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function respondWithRoute($name)
    {
        $route = tap($this->routes->getByName($name))->bind($this->currentRequest);

        return $this->runRouteDI($this->currentRequest, $route);
    }

    /**
     * Add a route to the underlying route collection.
     *
     * @param  array|string  $methods
     * @param  string  $uri
     * @param  \Closure|array|string|callable|null  $action
     * @return \Illuminate\Routing\Route
     */
    public function addRoute($methods, $uri, $action)
    {
        return $this->routes->addDI($this->createRoute($methods, $uri, $action));
    }
}
