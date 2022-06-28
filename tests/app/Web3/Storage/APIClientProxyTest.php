<?php

namespace App\Web3\Storage;

use App\Web3\Storage\Exceptions\APIException;
use PHPUnit\Framework\TestCase;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Client;

class APIClientProxyTest extends TestCase
{
    public function test_getList_Too_Many_Requests()
    {
        $this->expectException(APIException::class);
        $mock = new MockHandler([
            new Response(429, ['X-Foo' => 'Bar'], 'Hello, World')
        ]);
        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);

        $proxy = new APIClientProxy($client);
        $proxy->getList("");
    }
}
