<?php

namespace GGbear\Routing;

use DI\Container;

class Route extends \Illuminate\Routing\Route
{

    /**
     * The container instance used by the route.
     *
     * @var Container
     */
    protected $container;

    /**
     * The router instance used by the route.
     *
     * @var Router
     */
    protected $router;

    /**
     * Set the container instance on the route.
     *
     * @param  Container  $container
     * @return $this
     */
    public function setContainerDI(Container $container)
    {

        $this->container = $container;

        return $this;
    }

    /**
     * Set the router instance on the route.
     *
     * @param  Router  $router
     * @return $this
     */
    public function setRouterDI(Router $router)
    {

        $this->router = $router;

        return $this;
    }

    /**
     * Get the dispatcher for the route's controller.
     *
     * @return ControllerDispatcher
     */
    public function controllerDispatcher()
    {
        if ($this->container->has(ControllerDispatcher::class)) {
            return $this->container->make(ControllerDispatcher::class);
        }

        return new ControllerDispatcher($this->container);
    }
}
