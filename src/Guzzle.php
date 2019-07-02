<?php

namespace Okay;

class Guzzle
{
    public $client;
    public $response;
    public $status;
    public $headers;

    function __construct($uri, $headers = [])
    {
        $this->client = new \GuzzleHttp\Client([
            'base_uri' => $uri,
            'http_errors' => false,
            'headers' => $headers
        ]);
    }

    function setFactory($f)
    {
        
    }

    function __call($method, $args)
    {
        if (count($args) < 1) {
            throw new \InvalidArgumentException('Magic request methods require a URI and optional options array');
        }

        $uri = $args[0];
        $opts = isset($args[1]) ? $args[1] : [];

        return $this->request($method, $uri, $opts);
    }

    function request($method, $uri = '', $options = [])
    {
        $response = call_user_func([$this->client, strtolower($method)], $uri, $options);

        assert(in_array("Psr\Http\Message\ResponseInterface", class_implements($response)));

        $this->response = $response;
        $this->status = $response->getStatusCode();
        $this->headers = $response->getHeaders();

        return $this;
    }

    function assertCode($code)
    {
        if ($code !== $this->status) {
            $body = $this->response->getBody();
            $data = json_decode($body, true);
            if ($data === null) error_log("Result was: '{$body}'");
            if ($data === null) print_r("Result was: '{$body}'");
            print_r($data);
            // k_error_log(print_r($data));
        }

        assert($code == $this->status, $this->status);

        return $this;
    }

    function assertContentType($type)
    {
        $contentType = $this->headers["Content-Type"][0];
        assert($type == $contentType, $contentType);
        return $this;
    }

    function assertNoContentType($type = null)
    {
        assert(!isset($this->headers["Content-Type"]));
        return $this;
    }

    function assertHeader($key, $value)
    {
        assert ( $value == $this->headers[$key], "actual {$key}: {$value}");
    }

    function getJson()
    {
        return $this->data = json_decode($this->response->getBody(), JSON_PRETTY_PRINT);
    }
//    function getArrayNext()
//    {
//        $this->array = $this->array ?? $this->getJson();
//        return $this->data = array_shift($this->array);
//    }
//
//    function dataAt($key)
//    {
//        $this->data = $this->data ?? $this->getJson();
//        $this->value = $this->data[$key];
//        return $this;
//    }
//
//    function is($value)
//    {
//        $this->assertEquals($value, $this->value);
//        return $this;
//    }
//
//    function beginsWith($str)
//    {
//        $this->assertStringStartsWith($str, $this->value);
//        return $this;
//    }
//
//    function assertUlid(string $ulid)
//    {
//        return (1 === preg_match("/^[0-7][0-9A-HJKMNP-Z]{25}$/", $ulid));
//    }
}
