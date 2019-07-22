<?php

namespace Okay;

class GuzzlelessClient_Slim3
{
    public $properties;
    public $appFactory;

    function __construct($properties)
    {
        $this->properties = $properties;
    }

    function setFactory($f)
    {
        $this->appFactory = $f;
    }

    function request($method, $uriString, $stuff = [], $payload = '', $contentType = null)
    {
        $blaggerFn = function( $request ) use ($method, $uriString, $stuff, $payload, $contentType) {
            // your includes
            return GuzzlelessRequest::fromSlimRequest($request, $method, $uriString, $this->properties, $stuff, $payload, $contentType);
        };

        return ($this->appFactory)->newApp()->run($blaggerFn);
    }

    function get($uriString, $inStuff = [])
    {
        return $this->request('GET', "$uriString", $inStuff);
    }

    function post($uriString, $stuff = [], $method = 'POST')
    {
        switch (true) {
            case (isset($stuff['form_params']));

                $payload = http_build_query($stuff['form_params'], '', '&');
                return $this->request($method, $uriString, $stuff, $payload, 'application/x-www-form-urlencoded');

            case (isset($stuff['json']));

                $payload = json_encode($stuff['json']);
                return $this->request($method, $uriString, $stuff, $payload, 'application/json');
            default;
                return $this->request($method, $uriString, $stuff, '', 'application/json');
        }
    }

    function put($uriString, $stuff = [])
    {  
        return $this->post($uriString, $stuff, 'PUT');
    }
}
