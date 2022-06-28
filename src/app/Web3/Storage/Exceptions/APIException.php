<?php

namespace App\Web3\Storage\Exceptions;

class APIException extends \Exception
{
    private $statusCode;

    public function __construct($statusCode)
    {
        parent::__construct();

        $this->statusCode = $statusCode;
    }

    public function getStatusCode()
    {
        return $this->statusCode;
    }
}