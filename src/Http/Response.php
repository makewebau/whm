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
}
