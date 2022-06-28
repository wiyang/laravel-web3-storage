<?php

namespace App\Web3\Storage;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;

interface APIClientInterface
{
    public function getList($apiKey);
    public function upload($apiKey, $fileContent, $fileName);
    public function getStatus($apiKey, $cid);
}
