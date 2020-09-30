<?php

/**
 * This file is a part of Ekolo Builder
 * @author Don de Dieu BOLENGE <dondedieubolenge@gmail.com>
 */

namespace Ekolo\Builder\Routing;

use Ekolo\Builder\Routing\Route;

abstract class RouteRegistar
{
    /**
     * The routes
     * @var array
     */
    protected $routes = [];

    const NO_ROUTE = 1;

    /**
     * Allows you to add a new route
     * @param string $method The method of the road we record
     * @param Route $route L'instance de Ekolo\Routing\Route
     */
    public function addRoute(string $method, Route $route)
    {
        if (!array_key_exists($method, $this->routes)) {
            $this->routes[$method] = [];
        }

        if (!in_array($route, $this->routes[$method])) {
            $this->routes[$method][] = $route;
        }
    }

    /**
     * Renvoi toutes les routes
     * @return array
     */
    public function routes()
    {
        return $this->routes;
    }
}
