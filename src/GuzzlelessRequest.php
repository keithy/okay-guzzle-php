<?php

namespace Okay;

use \Slim\Http\Request;
use \Slim\Http\RequestBody;
use \Slim\Http\Uri;

class GuzzlelessRequest extends Request {

    static function fromSlimRequest($request, $method, $uriString, $stuff, $reqStuff, $payload = '', $contentType = null) {

        $body = new RequestBody();
        $body->write($payload);
        $body->rewind();
        
        $newRequest = new static(
                $method,
                Uri::createFromString($uriString),
                $request->headers,
                $request->cookies,
                $request->serverParams,
                $body,
                $request->uploadedFiles
        );

        $newRequest->headers->set('Content-Type', $contentType);

        foreach (($stuff['headers'] ?? []) as $k => $v) {
            $newRequest->headers->set($k, $v);
        }
        
        foreach (($reqStuff['headers'] ?? []) as $k => $v) {
            $newRequest->headers->set($k, $v);
        }
        
        return $newRequest;
    }

}
