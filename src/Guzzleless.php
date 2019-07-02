<?php

namespace Okay;

use Okay\GuzzlelessClient_Slim3 as GuzzlelessClient;

class Guzzleless extends Guzzle
{
    function __construct($uri, $headers = [])
    {
        $this->client = new GuzzlelessClient([
            'base_uri' => $uri,
            'http_errors' => false,
            'headers' => $headers
        ]);
    }

    function setFactory($f)
    {
        $this->client->appFactory = $f;
    }
}
