<?php
    /**
     * This file is a part of the Ekolo Builder
     * @author Don de Dieu BOLENGE <dondedieubolenge@gmail.com>
     */

    use Ekolo\Builder\Routing\Router;
    use Ekolo\Builder\Http\Request;
    use Ekolo\Builder\Http\Response;
    use Ekolo\Http\Options\Server;
    use Ekolo\Http\Options\Session;

    if (!function_exists('debug')) {
        /**
         * Allows debugging
         * @param mixed $data
         * @param bool $console if the execution is in console
         * @return void
         */
        function debug ($data, $console = false) {
            if ($console) {
                print_r($data);
            }else {    
                echo '<pre>';
                print_r($data);
                echo '</pre>';
            }

            die();
        }
    }


    if (!function_exists('initializer')) {
        /**
         * Permet d'initialiser des fonctions ou autres
         * @return void
         */
        function initializer() {
            init_env();
        }
    }

    if (!function_exists('e')) {
        /**
         * Permet d'échapper les balises html
         * @param string $string La chaine à échapper
         * @return string $string La chaine échappée
         */
        function e($string)
        {
            return htmlspecialchars($string);
        }
    }
    
    if (!function_exists('config')) {
        /**
         * Permet de renvoyer une configuration ou les tableaux de toutes les configuration
         * @param string $conf La configuration à trouver
         * @param string $default
         * @return mixed
         * 
         */
        function config(string $conf, string $default = null) {
            $basePath = base_path();
            
            $config = preg_match('#\.#', $conf) ? explode('.', $conf) : $conf;
            $fileConf = is_array($config) ? $config[0] : $config;
            $filenameConf = $basePath.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.$fileConf.'.php';

            if (!file_exists($filenameConf)) {
                throw new \Exception('Le fichier de la configuration "'.$fileConf.'" n\'existe pas');
                
            }

            $data = require $filenameConf;
            $value = is_array($config) ? $data[$config[1]] : $data;

            return !empty($value) ? $value : $default;
        }
    }

    if (!function_exists('array_key_first')) {
        /**
         * Retourne la première clée du tableau
         * @param array $array Le tableau en question
         * @return mixed
         */
        function array_key_first(array $array) {
            if (empty($array)) {
                throw new \Exception('Le paramètre doit être un tableau non null');
            }

            $array_keys = array_keys($array);
            return $array_keys[0];
        }
    }

    if (!function_exists('array_key_last')) {
        /**
         * Retourne la dernière clé du tableau
         * @param array $array
         * @return mixed
         */
        function array_key_last(array $array) {
            if (empty($array)) {
                throw new \Exception('Le paramètre doit être un tableau non null');
            }
            
            $array_keys = array_keys($array);
            $count = count($array_keys);
            return $array_keys[$count - 1];
        }
    }

    if (!function_exists('flash')) {
        /**
         * Permet de manipuler l'objet Ekolo\Framework\Utils\Flash
         * @return Flash
         */
        function flash() {
            return new Flash;
        }
    }

    if (!function_exists('base_path')) {
        /**
         * Permet de renvoyer le base path de l'application
         * @return string $base_path
         */
        function base_path() {
            return (new Server)->get('DOCUMENT_ROOT');
        }
    }

    if (!function_exists('env')) {
        /**
         * Permet de renvoyer une env
         * @param string $key La clé de l'env
         * @param mixed $default La valeur par défaut au cas où l'env n'existe pas
         * @return string $env
         */
        function env(string $key, $default = null) {
            return !empty($_ENV[$key]) ? $_ENV[$key] : $default;
        }
    }

    if (!function_exists('init_env')) {
        /**
         * Permet d'initialiser les env
         * @return void
         */
        function init_env() {
            $envs_filename = base_path().DIRECTORY_SEPARATOR.'.env';
            $envs = $envs_array = [];
            
            if ($ressources = fopen($envs_filename, 'r')) {
                while (!feof($ressources)) {
                    $element = fgets($ressources);

                    if (!empty(trim($element))) {
                        $element_array = explode('=', $element);
                        $envs_array[$element_array[0]] = $element_array[1];
                    }

                    $envs[] = $element;
                }

                fclose($ressources);
            }

            $_ENV = array_merge($envs_array, $_ENV);
        }
    }

    if (!function_exists('request')) {
        /**
         * Permet de renvoyer l'instance de Ekolo\Framework\Http\Request
         * @return Request $request
         */
        function request() {
            return new Request;
        }
    }

    if (!function_exists('response')) {
        /**
         * Permet de renvoyer l'instance de Ekolo\Framework\Http\Response
         * @return Response $response
         */
        function response() {
            return new Response;
        }
    }
    
     if (!function_exists('is_email_valid')) {
        /**
         * Vérifie si l'email passé en paramètre est valide
         * @param string $email L'adresse email en question
         * @return bool
         */
        function is_email_valid(string $email) {
            return filter_var($email, FILTER_VALIDATE_EMAIL);
        }
    }

    if (!function_exists('make_to_pluriel')) {
        /**
         * Permet de renvoyé un 's' si la valeur passé est > 1
         * @param mixed $value
         * @return string
         */
        function make_to_pluriel($value) {
            return $value > 1 ? 's' : '';
        }
    }

    if (!function_exists('is_int_valid')) {
        /**
         * Vérifie si la valeur passée en paramètre est réellement un entier
         * @param mixed $value La valeur à vérifier
         * @return boolean
         */
        function is_int_valid($value) {
            $value = (int) $value;

            if ($value > 0) {
                return true;
            }

            return false;
        }
    }

    if (!function_exists('is_tel')) {
        /**
         * Véririfie si c'est un numéro de téléphone
         * @param mixed $tel Le numéro de téléphone à vérifier
         * @return bool
         */
        function is_tel($tel) {
            if (is_numeric($tel)) {
                if (strlen($tel) <= 15) {
                    if (preg_match('#^\+([1-9]){1}([0-9]){11,}#', $tel)) {
                        return true;
                    }

                    if (strlen($tel) === 10) {
                        if (preg_match('#^0([1-9]){1}([0-9]){8}#', $tel)) {
                            return true;
                        }
                    }
                }
            }
            
            return false;
        }
    }
    
    /**
     * Fonction de hashage de mot de passe via Blowfish algorithme 
     * @param string|integer $value La valeur (mot de passe) à crypter
     * @param array $option Les options du cryptage
     * @return string $hash La valeur du mot de passe hashée
     */
    if (!function_exists('bcrypt_hash_password')) {
        function bcrypt_hash_password($value, $option = array()){

            $cost = isset($option['round'])? $option['round']:10;
            $hash = password_hash($value, PASSWORD_BCRYPT, array('cost',$cost));
            if ($hash === false) {
                throw new Exception("Bcrypt hashing n'est pas supporté");
                
            }
            return $hash;
        }
    }

    /**
     * Fonction de Vérification de hashage de mot de passe
     * @param string|integer $value La valeur de mot de passe à vérifier
     * @param string $hashvalue La valeur du mot de passe qui était hashée
     * @return bool
     */
    if (!function_exists('bcrypt_verify_password')) {
        function bcrypt_verify_password($value, string $hashedvalue){

            return password_verify($value, $hashedvalue);
        }
    }

    if (!function_exists('session')) {
        /**
         * Permet de gérer la session
         * @param string $key La clé de la session
         * @param mixed $value La valeur à assigner à la session
         */
        function session(string $key = null, $value = null) {
            $session = new Session;

            if (!empty($key)) {
                if (!empty($value)) {

                    $session->add([
                        $key => $value
                    ]);
                }

                return $session->get($key);
            }else {
                return $session;
            }
        }
    }