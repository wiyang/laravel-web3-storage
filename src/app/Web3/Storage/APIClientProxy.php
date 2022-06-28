<?php

namespace App\Web3\Storage;

use App\Web3\Storage\Exceptions\APIException;
use Illuminate\Support\Arr;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Exception\ClientException;

class APIClientProxy implements APIClientInterface
{
    const DOMAIN = "https://api.web3.storage";

    private $guzzleClient = NULL;

    public function __construct(Client $client = NULL)
    {
        if ($client !== NULL) {
            $this->guzzleClient = $client;
        } else {
            $this->guzzleClient = new Client();
        }
    }

    public function getList($apiKey)
    {
        $request = new Request(
            'GET',
            APIClient::DOMAIN . "/user/uploads",
            [
                "Authorization" => $this->getAuthorizationHeaderValue($apiKey)
            ]
        );
        $responseObject = NULL;
        try {
            $responseObject = $this->guzzleClient->send($request);    
        } catch (ClientException $e) {
            $responseObject = $e->getResponse();
            $status = $responseObject->getStatusCode();
            throw new APIException($status);
        }

        $stored_files = [];
        $response = json_decode($responseObject->getBody(), true);
        foreach ($response as $item) {
            $cid = $item["cid"];
            $ipfs_link = "https://ipfs.io/ipfs/{$cid}/";
            $stored_files[] = $ipfs_link;
        }

        return $stored_files;
    }

    public function upload($apiKey, $fileContent, $fileName)
    {
        $responseObject = $this->guzzleClient->request(
            'POST',
            APIClient::DOMAIN . "/upload",
            [
                'multipart' => [
                    [
                        'name'     => 'file',
                        'contents' => $fileContent,
                        'filename' => $fileName
                    ]
                ],
                'headers' => [
                    "Authorization" => $this->getAuthorizationHeaderValue($apiKey)
                ]
            ]
        );

        $status = $responseObject->getStatusCode();
        if ($status !== 200) {
            throw new APIException($status);
        }

        $response = json_decode($responseObject->getBody(), true);
        $cid = Arr::get($response, "cid");
        $ipfs_link = "https://ipfs.io/ipfs/{$cid}/{$fileName}";
        return $cid;
    }

    public function getStatus($apiKey, $cid)
    {
        $request = new Request(
            'GET',
            APIClient::DOMAIN . "/status/" . $cid,
            [
                "Authorization" => $this->getAuthorizationHeaderValue($apiKey)
            ]
        );
        $responseObject = $this->guzzleClient->send($request);

        $status = $responseObject->getStatusCode();
        if ($status !== 200) {
            throw new APIException($status);
        }

        $response = json_decode($responseObject->getBody(), true);
        $cid = Arr::get($response, "cid");
        $dagSize = Arr::get($response, "dagSize");
        $created = Arr::get($response, "created");
        return [$cid, $dagSize, $created];
    }

    private function getAuthorizationHeaderValue($apiKey)
    {
        return "Bearer " . $apiKey;
    }
}
