<?php

namespace MakeWeb\WHM\Http;

use Illuminate\Support\Str;
use MakeWeb\WHM\Exceptions\DomainAlreadyExistsException;
use MakeWeb\WHM\Exceptions\DatabaseAlreadyExistsException;
use MakeWeb\WHM\Exceptions\DatabaseUserAlreadyExistsException;
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

    public function cpanelResultData()
    {
        return collect($this->zttpResponse->json()['cpanelresult']['data']);
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

    public function hasExceptionClass()
    {
        return !is_null($this->exceptionClass());
    }

    public function exceptionClass()
    {
        $errorMessage = $this->buildErrorMessage();

        foreach ([
            DomainAlreadyExistsException::class => 'A DNS entry for the domain',
            DatabaseAlreadyExistsException::class => [
                'The database ',
                ' already exists.',
            ],
            DatabaseUserAlreadyExistsException::class => [
                'The user ',
                'cannot be created because it already exists.',
            ],
        ] as $exceptionClass => $messageSignature) {
            if (is_string($messageSignature)) {
                if (Str::contains($errorMessage, $messageSignature)) {
                    return $exceptionClass;
                }
            }

            if (is_array($messageSignature)) {
                $match = true;
                foreach ($messageSignature as $string) {
                    if (!Str::contains($errorMessage, $string)) {
                        $match = false;
                    }
                }
                if ($match) {
                    return $exceptionClass;
                }
            }
        }
    }

    public function throwException()
    {
        $className = $this->exceptionClass();

        throw new $className($this->buildErrorMessage());
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
