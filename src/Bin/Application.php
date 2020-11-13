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
         * Track if intercept the 404 error
         * @var bool
         */
        protected $interceptError404 = false;

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

            if (!empty($this->paths['template'])) {
                $this->response->extends(($this->paths['template']));
            }

            if ($route->vars()) {
                if (count($route->vars()) > 0) {
                    $_GET = array_merge($_GET, $route->vars());
                    $this->request->params()->add($_GET);
                }
            }

            $actions = $route->actions();
            $actions_exected = [];

            if (!empty($actions)) {
                $this->interceptError404 = true;
                
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
                if ($this->error->status == 404) {
                    if ($this->interceptError404) {
                        exit;
                    }
                }
                
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

        /**
         * Allows to register a route on application directly
         * @param string $method
         * @param string $url
         * @param mixed $actions
         */
        public function registarMethodDirect(string $method, string $url, ...$actions)
        {
            $methodExecute = strtolower($method);
            $router = new Router;
            $routesList = \explode('/', $url);
            $prefixeUri = '/'.$routesList[1];
            $url = \str_replace($prefixeUri, '', $url);
            
            if (empty($url)) {
                $url = '/';
            }
            
            if (!is_callable([$router, $methodExecute])) {
                throw new \Exception("The ".$methodExecute." is not supported by Ekolo Builder");
            }

            // debug($prefixeUri);  

            $callbacks = $actions[0];

            $router->$methodExecute($url, $callbacks);

            $this->use($prefixeUri, $router);
        }

        /**
         * Execute the route of the GET method by the url
         * @param string $url
         * @param $callbacks
         */
        public function get(string $url, ...$callbacks)
        {
            $this->registarMethodDirect('GET', $url, $callbacks);
        }

        /**
         * Execute the route of the POST method by the url
         * @param string $url
         * @param $callbacks
         */
        public function post(string $url, ...$callbacks)
        {
            $this->registarMethodDirect('POST', $url, $callbacks);
        }

        /**
         * Execute the route of the PUT method by the url
         * @param string $url
         * @param $callbacks
         */
        public function put(string $url, ...$callbacks)
        {
            $this->registarMethodDirect('PUT', $url, $callbacks);
        }

        /**
         * Execute the route of the DELETE method by the url
         * @param string $url
         * @param $callbacks
         */
        public function delete(string $url, ...$callbacks)
        {
            $this->registarMethodDirect('DELETE', $url, $callbacks);
        }

        /**
         * It records the route of the HEAD method
         * @param string $uri
         * @param \Closure $callbacks
         */
        public function head(string $uri, ...$callbacks)
        {
            $this->registarMethodDirect('HEAD', $uri, $callbacks);
        }

        /**
         * It records the route of the CONNECT method
         * @param string $uri
         * @param \Closure $callbacks
         */
        public function connect(string $uri, ...$callbacks)
        {
            $this->registarMethodDirect('CONNECT', $uri, $callbacks);
        }

        /**
         * It records the route of the OPTIONS method
         * @param string $uri
         * @param \Closure $callbacks
         */
        public function options(string $uri, ...$callbacks)
        {
            $this->registarMethodDirect('OPTIONS', $uri, $callbacks);
        }

        /**
         * It records the route of the TRACE method
         * @param string $uri
         * @param \Closure $callbacks
         */
        public function trace(string $uri, ...$callbacks)
        {
            $this->registarMethodDirect('TRACE', $uri, $callbacks);
        }
    }