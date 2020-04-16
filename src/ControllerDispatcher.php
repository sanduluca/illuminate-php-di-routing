<?php

namespace GGbear\Routing;

use Psr\Container\ContainerInterface;

class ControllerDispatcher extends \Illuminate\Routing\ControllerDispatcher
{

    /**
     * Create a new class instance.
     *
     * @param  ContainerInterface  $container
     * @return void
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }
}
