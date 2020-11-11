<?php

/**
 * This file is a part of Ekolo Builder
 * @author Don de Dieu BOLENGE <dondedieubolenge@gmail.com>
 */

namespace Ekolo\Builder\Routing;

use Ekolo\Builder\Routing\RouteRegistar;

/**
 * @package The router allows you to do the routing
 */
class Router extends RouteRegistar
{

    /**
     * Renvoi la route par rapport à l'url passé en paramètre
     * @param string $url
     */
    public function getRoute($method, $url)
    {
        $urls = [];

        if (!empty($this->routes[$method])) {
            foreach ($this->routes[$method] as $route) {
                // If the route matches the URL
                if (($varsValues = $route->match($url)) !== false) {
                    // if she has variables

                    if ($route->hasVars()) {
                        $varsNames = $route->varsNames();
                        $listVars = [];

                        foreach ($varsValues as $key => $match) {
                            if ($key !== 0) {
                                $listVars[$varsNames[$key - 1]] = $match;
                            } else {
                                $listVars['url'] = substr($match, 1);
                            }
                        }

                        $route->setVars($listVars);
                    } else {

                        if (count($varsValues) > 1) {
                            $route->setVars(['id' => $varsValues[1]]);
                        }
                    }

                    return $route;
                }
            }
        }
    }

    /**
     * Allows to register a route
     * @param string $method the route method
     * @param string $uri The url of route
     * @param array $callbacks The callback function
     */
    public function registrarRoute(string $method, string $url, ...$callbacks)
    {
        if (is_array($callbacks)) {
            while (!\is_callable($callbacks[0])) {
                $callbacks = $callbacks[0];
            }
        }

        // debug($callbacks);
        
        $regexVarUrl = '#:([a-zA-Z]{1,})+([a-zA-Z0-9_\-]{1,})?#';

        $urlRegexed = preg_replace($regexVarUrl, '([a-zA-Z0-9_\-]{1,})', $url);
        $urlRegexed .= '/?';

        $vars = $this->matchVars($url);
        $this->addRoute($method, new Route($urlRegexed, $callbacks, $vars));
    }

    /**
     * Allows to match variables in a url
     * @param string $url
     * @return array $vars
     */
    public function matchVars($url)
    {
        $vars = [];

        if (preg_match('#:#', $url, $macthes)) {
            $stringVars = explode(':', $url);

            foreach ($stringVars as $key => $value) {
                if ($key > 0) {
                    $var = explode('/', $value);
                    $vars[] = $var[0];
                }
            }
        }

        return $vars;
    }

    /**
     * It records the route of the GET method
     * @param string $uri
     * @param array $callbacks
     */
    public function get(string $uri, ...$callbacks)
    {
        $this->registrarRoute('GET', $uri, $callbacks);
    }

    /**
     * It records the route of the POST method
     * @param string $uri
     * @param array[\Closure] $callbacks
     */
    public function post(string $uri, ...$callbacks)
    {
        $this->registrarRoute('POST', $uri, $callbacks);
    }

    /**
     * It records the route of the PUT method
     * @param string $uri
     * @param \Closure $callbacks
     */
    public function put(string $uri, ...$callbacks)
    {
        $this->registrarRoute('PUT', $uri, $callbacks);
    }

    /**
     * It records the route of the DELETE method
     * @param string $uri
     * @param \Closure $callbacks
     */
    public function delete(string $uri, ...$callbacks)
    {
        $this->registrarRoute('DELETE', $uri, $callbacks);
    }

    /**
     * It records the route of the HEAD method
     * @param string $uri
     * @param \Closure $callbacks
     */
    public function head(string $uri, ...$callbacks)
    {
        $this->registrarRoute('HEAD', $uri, $callbacks);
    }

    /**
     * It records the route of the CONNECT method
     * @param string $uri
     * @param \Closure $callbacks
     */
    public function connect(string $uri, ...$callbacks)
    {
        $this->registrarRoute('CONNECT', $uri, $callbacks);
    }

    /**
     * It records the route of the OPTIONS method
     * @param string $uri
     * @param \Closure $callbacks
     */
    public function options(string $uri, ...$callbacks)
    {
        $this->registrarRoute('OPTIONS', $uri, $callbacks);
    }

    /**
     * It records the route of the TRACE method
     * @param string $uri
     * @param \Closure $callbacks
     */
    public function trace(string $uri, ...$callbacks)
    {
        $this->registrarRoute('TRACE', $uri, $callbacks);
    }
}
