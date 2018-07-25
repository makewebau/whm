<?php

namespace MakeWeb\WHM;

use MakeWeb\WHM\Http\Client;

class CPanelClient extends Client
{
    public function createDatabase($name)
    {
        return $this->mysqlFE('createdb', [
            'db' => $this->normalizeDatabaseSlug($name),
        ]);
    }

    public function deleteDatabase($name)
    {
        return $this->mysqlFE('deletedb', [
            'db' => $this->normalizeDatabaseSlug($name),
        ]);
    }

    public function mysqlFE($function, $parameters)
    {
        return $this->function('MysqlFE', $function, $parameters);
    }

    public function function($module, $function, $parameters)
    {
        return $this->get('json-api/cpanel', array_merge([
            'cpanel_jsonapi_user' => $this->name,
            'cpanel_jsonapi_version' => '2',
            'cpanel_jsonapi_module' => $module,
            'cpanel_jsonapi_func' => $function,
        ], $parameters));
    }

    protected function normalizeDatabaseSlug($name)
    {
        if (substr($name, 0, 9) === $this->databaseSlug().'_') {
            return $name;
        }

        return $this->databaseSlug().'_'.$name;
    }

    public function databaseSlug()
    {
        return substr($this->name, 0, 8);
    }
}
