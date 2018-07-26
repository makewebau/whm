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

    public function mysqlFE($function, $parameters = [])
    {
        return $this->api2('MysqlFE', $function, $parameters);
    }

    public function api2($module, $function, $parameters)
    {
        return $this->function(2, $module, $function, $parameters);
    }

    public function function($apiVersion, $module, $function, $parameters)
    {
        return $this->get('json-api/cpanel', array_merge([
            'cpanel_jsonapi_user' => $this->name,
            'cpanel_jsonapi_version' => $apiVersion,
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

    protected function normalizeDatabaseUserSlug($name)
    {
        return substr($this->normalizeDatabaseSlug($name), 0, 16);
    }

    public function databaseSlug()
    {
        return substr($this->name, 0, 8);
    }

    public function createSubdomain($subdomain)
    {
        return $this->api2('SubDomain', 'addsubdomain', [
            'domain' => $subdomain,
            'rootdomain' => $this->getRootDomain($subdomain),
        ]);
    }

    public function createDatabaseUser($username, $password)
    {
        return $this->mysqlFE('createdbuser', [
            'dbuser' => $this->normalizeDatabaseUserSlug($username),
            'password' => $password,
        ]);
    }

    public function grantAllPrivelegesToUserOnDatabase($username, $database)
    {
        return $this->mysqlFE('setdbuserprivileges', [
            'db' => $this->normalizeDatabaseSlug($database),
            'dbuser' => $this->normalizeDatabaseUserSlug($username),
            'privileges' => 'ALL PRIVILEGES'
        ]);
    }

    public function domainExists($domain)
    {
        return isset($this->api2('DnsLookup', 'name2ip', ['domain' => $domain])->json()['cpanelresult']['data'][0]['ip']);
    }

    public function databaseExists($name)
    {
        $name = $this->normalizeDatabaseSlug($name);

        $result = $this->mysqlFE('listdbs');

        return $result->cpanelResultData()->contains(function ($database) use ($name) {
            return $database['db'] === $name;
        });
    }

    public function databaseUserExists($name)
    {
        $name = $this->normalizeDatabaseSlug($name);

        $result = $this->mysqlFE('getdbusers');

        return $result->cpanelResultData()->contains($name);
    }

    public function getRootDomain($subdomain)
    {
        $myhost = strtolower(trim($subdomain));
        $count = substr_count($myhost, '.');
        if ($count === 2) {
            if (strlen(explode('.', $myhost)[1]) > 3) {
                $myhost = explode('.', $myhost, 2)[1];
            }
        } elseif ($count > 2) {
            $myhost = get_domain(explode('.', $myhost, 2)[1]);
        }

        return $myhost;
    }
}
