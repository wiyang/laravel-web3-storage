<?php

namespace App\Web3\Storage;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;

class APIClient
{
    const DOMAIN = "https://api.web3.storage";

    public function getList($apiKey)
    {
        $response = Http::withHeaders([
            "Authorization" => $this->getAuthorizationHeaderValue($apiKey)
        ])->get(APIClient::DOMAIN . "/user/uploads")->json();

        $stored_files = [];
        foreach ($response as $item) {
            $cid = $item["cid"];
            $ipfs_link = "https://ipfs.io/ipfs/{$cid}/";
            $stored_files[] = $ipfs_link;
        }

        return $stored_files;
    }

    public function upload($apiKey, $fileContent, $fileName)
    {
        $response = Http::withHeaders([
            "Authorization" => $this->getAuthorizationHeaderValue($apiKey)
        ])->attach("file", $fileContent, $fileName)
            ->post(APIClient::DOMAIN . "/upload")
            ->json();

        $cid = Arr::get($response, "cid");
        $ipfs_link = "https://ipfs.io/ipfs/{$cid}/{$fileName}";
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

        return [$cid, $dagSize, $created];
    }
    
    private function getAuthorizationHeaderValue($apiKey)
    {
        return "Bearer " . $apiKey;
    }
}
