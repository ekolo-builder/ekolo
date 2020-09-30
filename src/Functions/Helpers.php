<?php
    /**
     * This file is a part of the Ekolo Builder
     * @author Don de Dieu BOLENGE <dondedieubolenge@gmail.com>
     */

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