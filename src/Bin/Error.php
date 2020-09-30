<?php

    /**
     * This file is a part of the Ekolo Builder
     * @author Don de Dieu BOLENGE <dondedieubolenge@gmail.com>
     */

    namespace Ekolo\Builder\Bin;

    /**
     * Represent a error
     */
    class Error {

        /**
         * Error message
         * @var string
         */
        public $message;

        /**
         * Error status
         * @var int|string
         */
        public $status;

        public function __construct(string $message, $status)
        {
            $this->message = $message;
            $this->status = $status;
        }

    }