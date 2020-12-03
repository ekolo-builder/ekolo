<?php

/**
 * This file is a part of the Ekolo Builder
 * @author Don de Dieu BOLENGE <dondedieubolenge@gmail.com>
 */
namespace Ekolo\Builder\Http;

use Ekolo\Http\Request as HTTPRequest;
use Ekolo\Builder\Http\Validator;

class Request extends HTTPRequest
{

    /**
     * Data validator and their type
     * @var Validator
     */
    protected $validator;

    public function __construct()
    {
        parent::__construct();
        $this->validator = new Validator($this);
    }

    /**
     * Instance of validator
     * @return Validator
     */
    public function validator()
    {
        return $this->validator;
    }
}