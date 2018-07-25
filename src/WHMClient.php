<?php

namespace MakeWeb\WHM;

use MakeWeb\WHM\Http\Client;
use MakeWeb\WHM\Models\Database;

class WHMClient extends Client
{
    public function listDatabases()
    {
        return $this->get('json-api/list_databases', ['api.version' => 1])->toModels(Database::class);
    }
}
