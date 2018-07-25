<?php

namespace MakeWeb\WHM\Http;

use Zttp\ZttpResponse;

class Response
{
    protected $zttpResponse;

    public function __construct(ZttpResponse $zttpResponse)
    {
        $this->zttpResponse = $zttpResponse;
    }

    public function toModels($className)
    {
        return $this->payload()->map(function ($model) use ($className) {
            return (new $className($model));
        });
    }

    public function payload()
    {
        return collect($this->zttpResponse->json()['data']['payload']);
    }

    public function isError()
    {
        return !$this->isSuccessful();
    }

    public function isSuccessful()
    {
        return $this->statusCode() >= 200 && $this->statusCode() < 300 && !$this->hasJsonError();
    }

    public function statusCode()
    {
        return (int) $this->zttpResponse->response->getStatusCode();
    }

    public function hasJsonError()
    {
        return (bool) $this->jsonError();
    }

    public function jsonError()
    {
        $json = $this->json();

        if (isset($json['error'])) {
            return $json['error'];
        }

        if (isset($json['cpanelresult']) && isset($json['cpanelresult']['error'])) {
            return $json['cpanelresult']['error'];
        }

        return null;
    }

    public function json()
    {
        return $this->zttpResponse->json();
    }

    public function buildErrorMessage()
    {
        if ($this->hasJsonError()) {
            return $this->jsonError();
        }
        dd($this->zttpResponse);

        return $this->statusCode();
    }
}
