<?php
    /**
    * This file is a part of the Ekolo Builder
    * @author Don de Dieu BOLENGE <dondedieubolenge@gmail.com>
    */

    namespace Ekolo\Builder\Http;

    use Ekolo\Http\Response as HTTPResponse;

    class Response extends HTTPResponse {

        /**
         * La vue à afficher
         * @var string
         */
        protected $view;

        /**
         * Le template à éttendre la vue
         * @var string
         */
        protected $template = null;

        /**
         * Les variables à renvoyer à la vue
         * @var array
         */
        protected $vars = [];

        /**
         * The path of views files
         * @var string
         */
        protected $viewsPath;


        public function __construct()
        {
            parent::__construct();
        }

        /**
         * Set the view file
         * @param string $view
         * @return void
         */
        public function setView(string $view)
        {
            $view = preg_replace('#\/|\.#', DIRECTORY_SEPARATOR, $view);
            $view = '.'.DIRECTORY_SEPARATOR.$this->viewsPath.DIRECTORY_SEPARATOR.$view.'.php';

            if (!file_exists($view)) {
				throw new \Exception('The file specified for the view does not exist');
            }

            $this->view = $view;
        }

        /**
         * Set the template
         * @param string $template
         * @return void
         */
        public function extends(string $template)
        {
            $template = preg_replace('#\/|\.#', DIRECTORY_SEPARATOR, $template);
            $template = '.'.DIRECTORY_SEPARATOR.$this->viewsPath.DIRECTORY_SEPARATOR.$template.'.php';

            if (!file_exists($template)) {
				throw new \Exception('The file specified for the layout does not exist');
            }
            
            $this->template = $template;
        }

        /**
         * Allows to return a view
         * @param string $view The view to return
         * @param array $vars The variables to pass in the view
         * @param int $status The status to return
         * @param array $headers The headers to send
         */
        public function render(string $view, array $vars = [], int $status = null, array $headers = [])
        {
            $vars += $this->vars;

            $this->setVars($vars);

			if (!empty($vars)) {
				extract($vars);
			}

            $this->setView($view);
            
            if (!empty($this->template)) {
                ob_start();
                require $this->view;
                $content = ob_get_clean();

                require $this->template;
            }else {
                require $this->view;
            }

            if (!empty($status)) {
                $this->setStatus($status);
            }
            
            $this->addHeaders($headers);
        }

        /**
         * Modifie les vars
         * @param array $vars
         * @return void
         */
        public function setVars(array $vars = [])
        {
            $this->vars = $vars;
        }

        /**
         * Renvoi les vars
         * @return array $vars
         */
        public function vars()
        {
            return $this->vars;
        }

        /**
         * Modify the headers to response
         * @param array $headers
         * @return void
         */
        public function headers(array $headers)
        {
            $this->addHeaders($headers);
        }

        /**
         * Set the views path
         * @param string $viewsPath
         * @return void
         */
        public function setViewsPath(string $viewsPath)
        {
            $this->viewsPath = $viewsPath;
        }
    }
