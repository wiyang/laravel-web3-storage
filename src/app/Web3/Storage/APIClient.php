<?php

namespace App\Web3\Storage;

use App\Web3\Storage\Exceptions\APIException;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;

class APIClient implements APIClientInterface
{
    const DOMAIN = "https://api.web3.storage";

    private $proxy;

    public function __construct()
    {
        $this->proxy = new APIClientProxy();
    }

    public function getList($apiKey)
    {
        $stored_files = [];
        try {
            $stored_files = $this->proxy->getList($apiKey);
        } catch (\Exception $exception) {
            if (($exception instanceof APIException) && $exception->getStatusCode() == 429) {
                sleep(10);
                $stored_files = $this->proxy->getList($apiKey);
            }
        }

        return $stored_files;
    }

    public function upload($apiKey, $fileContent, $fileName)
    {
        $cid = null;
        try {
            $cid = $this->proxy->upload($apiKey, $fileContent, $fileName);
        } catch (\Exception $exception) {
            if (($exception instanceof APIException) && $exception->getStatusCode() == 429) {
                sleep(10);
                $cid = $this->proxy->upload($apiKey, $fileContent, $fileName);
            }
        }
        return $cid;
    }

    public function getStatus($apiKey, $cid)
    {
        $response = Http::withHeaders([
            "Authorization" => $this->getAuthorizationHeaderValue($apiKey)
        ])->get(APIClient::DOMAIN . "/status/" . $cid)->json();

        $cid = Arr::get($response, "cid");
        $dagSize = Arr::get($response, "dagSize");
        $created = Arr::get($response, "created");

        $return_array = null;
        try {
            $return_array = $this->proxy->getStatus($apiKey, $cid);
        } catch (\Exception $exception) {
            if (($exception instanceof APIException) && $exception->getStatusCode() == 429) {
                sleep(10);
                $return_array = $this->proxy->getStatus($apiKey, $cid);
            }
        }
        return $return_array;
    }

    private function getAuthorizationHeaderValue($apiKey)
    {
        return "Bearer " . $apiKey;
    }
}
