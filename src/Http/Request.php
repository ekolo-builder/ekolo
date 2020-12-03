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
    public function __contruct()
    {
        parent::__contruct();
        $this->validator = new Validator($this);
    }

    /**
     * Data validator and their type
     * @var Validator
     */
    protected $validator;

    public function validator()
    {
        return $this->validator;
    }
}