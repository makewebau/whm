<?php

namespace MakeWeb\WHM;

use MakeWeb\WHM\Http\Client;
use MakeWeb\WHM\Http\Response;
use MakeWeb\WHM\Models\Database;
use Zttp\Zttp;

class WHMClient extends Client
{
    protected $user;

    protected $port;

    protected $hostname;

    protected $apiKey;

    public function __construct($attributes = [])
    {
        foreach (array_merge([
            'user' => 'root',
            'port' => '2087',
        ], $attributes) as $key => $value) {
            $this->$key = $value;
        }
    }

    public function listDatabases()
    {
        return $this->get('list_databases')->toModels(Database::class);
    }

    public function get($endpoint)
    {
        return $this->call('get', $endpoint);
    }

    public function call($method, $endpoint)
    {
        return new Response(
            Zttp::withHeaders([
                'Authorization' => 'whm '.$this->user.':'.$this->apiKey
            ])->$method($this->url($endpoint), ['api.version' => '1'])
        );
    }

    public function url($uri = null)
    {
        return 'https://'.$this->hostname.':'.$this->port.'/'.$uri;
    }
}
