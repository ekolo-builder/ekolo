<?php

    /**
     * This file is a part of the Ekolo Builder
     * @author Don de Dieu BOLENGE <dondedieubolenge@gmail.com>
     */

    namespace Ekolo\Builder\Bin;

    use Ekolo\Builder\Http\Request;
    use Ekolo\Builder\Http\Response;
    use Ekolo\Builder\Routing\Router;
    use Ekolo\Builder\Routing\Route;
    use Ekolo\Builder\Bin\Error;

    class Application {

        /**
         * Instance of Ekolo\Http\Request
         * @var Request
         */
        protected $request;

        /**
         * Instance of Ekolo\Http\Response
         * @var Response
         */
        protected $response;

        /**
         * List of middlewares
         * @var array
         */
        protected $middlewares = [];

        /**
         * Instance of Ekolo\Routing\Router
         * @var Router
         */
        protected $router;

        /**
         * Indicates that there may be an error
         * @var bool
         */
        protected $errorPossible = true;

        /**
         * Error found in the current route
         * @var Error
         */
        protected $error;

        /**
         * Les actions exécutées
         * @var array 
         */
        protected $actionsExecuted = [];

        /**
         * The paths adress
         * @var string
         */
        protected $paths = [
            'views' => 'views',
            'public' => 'public'
        ];

        public function __construct()
        {   
            $this->request = new Request;
            $this->response = new Response;
            $this->router = new Router;
        }

        /**
         * Method responsible for calling the pages or modules that are needed in relation to the routes
         * @param $args
         */
        public function use(...$args)
        {
            if (gettype($args[0]) === 'object' && get_class($args[0]) == 'Closure') {
                $this->executeActions($args);
            }elseif (gettype($args[0]) === 'string') {
                $prefixeUri = $args[0];
                $router = $args[1];

                unset($args[0]);
                unset($args[1]);

                if (gettype($router) != 'object' || get_class($router) != 'Ekolo\\Builder\\Routing\\Router') {
                    throw new \Exception('The second parameter for '.$prefixeUri. ' must be the instance of Ekolo\\Builder\\Routing\\Router');
                    
                }

                $regex = '#^' . $prefixeUri . '#';

                if ($prefixeUri === '/') {
                    if ($route = $router->getRoute($this->request->method(), $this->request->uri())) {
                        $this->getController($route);
                    } else {
                        $this->setError(404, "The requested route does not exist or no longer exists");
                    }
                } else {
                    if (preg_match($regex, $this->request->uri(), $matches)) {
                        $url = preg_replace($regex, '', $this->request->uri());
                        $url = empty($url) ? '/' : $url;

                        $route = $router->getRoute($this->request->method(), $url);

                        if ($route) {
                            $this->getController($route);
                        } else {
                            $this->setError(404, "The requested route does not exist or no longer exists");
                        }
                    }
                }
            }else {
                $this->setError(404, "The requested route does not exist or no longer exists");
            }
        }

        /**
         * Permet de lancer le controller par rapport à la route trouvée
         * @param Route $route L'instance de la Core\Route
         */
        public function getController(Route $route)
        {
            $this->response->setViewsPath($this->paths['views']);
            if ($route->vars()) {
                if (count($route->vars()) > 0) {
                    $_GET = array_merge($_GET, $route->vars());
                }
            }

            $actions = $route->actions();
            $actions_exected = [];

            if (!empty($actions)) {
                $ip = 0;
                foreach ($actions as $action) {
                    $ip++;
                    if (!is_callable($action)) {
                        throw new \Exception('The parameter of '.$ip.' of the route '.$route->url.' must be a Closure');
                    }

                    $actions_exected[] = $action($this->request, $this->response);
                }
            }else {
                throw new \Exception('No action (function) taken in the route '.$route->url);
            }
        }

        /**
         * Allows to execute actions (middlewares functions callback)
         * @param array $actions
         * @return void
         */
        public function executeActions(array $actions)
        {
            $this->response->setViewsPath($this->paths['views']);
            foreach ($actions as $action) {
                $this->actionsExecuted[] = $action($this->request, $this->response);
            }
        }

        /**
         * Allows to track errors from the errors middleware
         * @param \Closure $callback
         * @return void
         */
        public function trackErrors($callback)
        {
            if (!empty($this->error)) {
                $callback($this->error, $this->request, $this->response);
            }
        }

        /**
         * Modify error
         * @param int|string $status
         * @param string $message
         * @return void
         */
        public function setError($status, $message)
        {
            if ($this->errorPossible) {
                $this->error = new Error($message, $status);
            }
        }

        /**
         * Set the paths adress for files
         * @var string $pathName
         * @var string $value
         * @return void
         */
        public function set(string $pathName, string $value)
        {
            $this->paths[$pathName] = $value;
        }
    }