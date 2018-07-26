<?php

namespace MakeWeb\WHM\Http;

use Zttp\Zttp;

abstract class Client
{
    protected $user;

    protected $port;

    protected $hostname;

    protected $apiKey;

    protected $name;

    public function __construct($attributes = [])
    {
        foreach (array_merge([
            'user' => 'root',
            'port' => '2087',
        ], $attributes) as $key => $value) {
            $this->$key = $value;
        }
    }

    public function get($endpoint, $queryParameters = [])
    {
        return $this->call('get', $endpoint, $queryParameters);
    }

    public function call($method, $endpoint, $queryParameters = [])
    {
        $response = new Response(
            Zttp::withHeaders([
                'Authorization' => 'whm '.$this->user.':'.$this->apiKey
            ])->$method($this->url($endpoint), $queryParameters)
        );

        if ($response->isError()) {
            if ($response->hasExceptionClass()) {
                $response->throwException();
            }
            throw new \Exception('Request failed with message: '.$response->buildErrorMessage());
        }

        return $response;
    }

    public function url($uri = null, $parameters = [])
    {
        return 'https://'.$this->hostname.':'.$this->port.'/'.$uri.(count($parameters) ? '?'.$this->buildQuery($parameters) : '');
    }

    public function buildQuery($parameters = [])
    {
        return http_build_query($parameters);
    }
}
