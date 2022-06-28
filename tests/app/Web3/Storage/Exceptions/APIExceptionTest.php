<?php

namespace App\Web3\Storage\Exceptions;

use PHPUnit\Framework\TestCase;

class APIExceptionTest extends TestCase
{
    public function test_getStatusCode()
    {
        $exception = new APIException(429);
        $this->assertEquals(429, $exception->getStatusCode());
    }
}
