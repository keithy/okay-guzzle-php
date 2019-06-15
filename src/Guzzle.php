<?php

use Phianola\App\BoogieWoogie;
use Phianola\App\Configuration;

class Guzzle
{
    const useGuzzle = true;

    protected $http;

    protected $sessionKey;
    protected $scopeKey;
    protected $config;
    protected $host;

    function _construct($uri, $headers = [] )
    {
        $this->http = new GuzzleHttp\Client([
            'base_uri' => $uri,
            'http_errors' => false,
            'headers' => $headers
        ]);
    }

    public function setUp(): void
    {

        $this->config = Configuration::fromFilePath(__DIR__ . "/../../_config/default.inc");
        $C = $this->config;

        $this->host = $C['phpunit-test-host'];
        $this->sessionKey = $C['header_key_session_id'];
        $this->scopeKey = $C['header_key_scope'];

        if (static::useGuzzle) {
            $this->http = new GuzzleHttp\Client(['base_uri' => $this->host,
                'http_errors' => false,
                'headers' => $this->defaultHeaders()]);
        } else {
            $appFactory = function() {
                return (new BoogieWoogie($this->config))->initializeApp();
                ;
            };

            $this->http = new Guzzleless($appFactory, [/* 'base_uri' => $this->host, */
                'http_errors' => false,
                'headers' => $this->defaultHeaders()]);
        }
    }

    public function tearDown(): void
    {
        $this->http = null;
    }
    use Slim\Http\Response;
    public $subject;
    public $array;
    public $data;
    public $value;

    function verify($subject)
    {
        $this->subject = $subject;
        $this->data = null;
        return $this;
    }

    function replied($code)
    {

        $response = $this->subject;

        $this->assertImplements("Psr\Http\Message\ResponseInterface", $response);

        $status = $response->getStatusCode();

        if ($code != $status) {
            $body = $response->getBody();
            $data = json_decode($body, true);
            if ($data === null) error_log("Result was NULL");
            error_log(print_r($data, true));
        }

        $this->assertEquals($code, $status);

        return $this;
    }

    function assertImplements($interface, $instOrClass)
    {
        $this->assertContains($interface, class_implements($instOrClass));
    }

    function getJson()
    {
        return $this->data = json_decode($this->subject->getBody(), JSON_PRETTY_PRINT);
    }

    function getArrayNext()
    {
        $this->array = $this->array ?? $this->getJson();
        return $this->data = array_shift($this->array);
    }

    function dataAt($key)
    {
        $this->data = $this->data ?? $this->getJson();
        $this->value = $this->data[$key];
        return $this;
    }

    function is($value)
    {
        $this->assertEquals($value, $this->value);
        return $this;
    }

    function beginsWith($str)
    {
        $this->assertStringStartsWith($str, $this->value);
        return $this;
    }

    function assertUlid(string $ulid)
    {
        return (1 === preg_match("/^[0-7][0-9A-HJKMNP-Z]{25}$/", $ulid));
    }
}
