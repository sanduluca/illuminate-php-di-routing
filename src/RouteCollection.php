<?php

namespace GGbear\Routing;

class RouteCollection extends \Illuminate\Routing\RouteCollection
{
    /**
     * Add a Route instance to the collection.
     *
     * @param  Route  $route
     * @return Route
     */
    public function addDI(Route $route)
    {
        $this->addToCollections($route);

        $this->addLookups($route);

        return $route;
    }
}
