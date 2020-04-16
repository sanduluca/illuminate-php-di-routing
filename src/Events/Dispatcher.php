<?php

namespace GGbear\Routing\Events;

use Psr\Container\ContainerInterface;

class Dispatcher extends \Illuminate\Events\Dispatcher
{

    /**
     * The IoC container instance.
     *
     * @var ContainerInterface
     */
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }
}
