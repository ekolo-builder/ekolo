<?php

/**
 * This file is a part of Ekolo Builder
 * @author Don de Dieu BOLENGE <dondedieubolenge@gmail.com>
 */

namespace Ekolo\Builder\Routing;

/**
 * Represents the informations of a route
 * @package Ekolo\Routing\Route
 */
class Route
{
    /**
     * The actions to be performed
     * @var array
     */
    protected $actions;

    /**
     * The url that the client to request
     * @var string
     */
    public $url;

    /**
     * The names of the variables contained in the route
     * @var array
     */
    protected $varsNames;

    /**
     * The variables contained in the route
     * @var array
     */
    protected $vars = [];

    /**
     * Construct
     * @param string $url
     * @param array $actions
     * @param array $varsNames
     */
    public function __construct(string $url, array $actions, array $varsNames)
    {
        $this->setUrl($url);
        $this->setActions($actions);
        $this->setVarsNames($varsNames);
    }

    /**
     * Check if the route contains variables
     * @return bool
     */
    public function hasVars()
    {
        return !empty($this->varsNames);
    }

    /**
     * Check if the url passed in parameter matches that of the route
     * @param string $url
     * @return array|bool $matches
     */
    public function match($url)
    {
        if (preg_match('#^' . $this->url . '$#', $url, $matches)) {
            // for ($i=0; $i < count($matches); $i++) { 
            // 	if ($i > 0 && is_paire($i)) {
            // 		$matches[$i] = preg_replace('#_#', '', $matches[$i]);
            // 	}
            // }

            return $matches;
        } else {
            return false;
        }
    }

    /**
     * Modify the actions
     * @param array[Closure]
     * @return void
     */
    public function setActions(array $actions)
    {
        $this->actions = $actions;
    }

    /**
     * Modify the url of route
     * @param string $url
     * @return void
     */
    public function setUrl(string $url)
    {
        $this->url = $url;
    }

    /**
     * Modify the names of variables
     * @param array $varsNames
     * @param void
     */
    public function setVarsNames(array $varsNames)
    {
        $this->varsNames = $varsNames;
    }

    /**
     * Modify the values of variables
     * @param $vars
     */
    public function setVars($vars)
    {
        $this->vars = $vars;
    }

    /**
     * Return the right actions
     * @return array
     */
    public function actions()
    {
        return $this->actions;
    }

    /**
     * Return the different variables
     * @return $vars
     */
    public function vars()
    {
        return $this->vars;
    }

    /**
     * Return the names of the variables
     * @return array $varsNames
     */
    public function varsNames()
    {
        return $this->varsNames;
    }
}
