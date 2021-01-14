<?php

namespace _PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\SQLSrv;

use _PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\AbstractSQLServerDriver;
use _PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\AbstractSQLServerDriver\Exception\PortWithoutHost;
/**
 * Driver for ext/sqlsrv.
 */
final class Driver extends \_PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\AbstractSQLServerDriver
{
    /**
     * {@inheritdoc}
     *
     * @return Connection
     */
    public function connect(array $params)
    {
        $serverName = '';
        if (isset($params['host'])) {
            $serverName = $params['host'];
            if (isset($params['port'])) {
                $serverName .= ',' . $params['port'];
            }
        } elseif (isset($params['port'])) {
            throw \_PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\AbstractSQLServerDriver\Exception\PortWithoutHost::new();
        }
        $driverOptions = $params['driverOptions'] ?? [];
        if (isset($params['dbname'])) {
            $driverOptions['Database'] = $params['dbname'];
        }
        if (isset($params['charset'])) {
            $driverOptions['CharacterSet'] = $params['charset'];
        }
        if (isset($params['user'])) {
            $driverOptions['UID'] = $params['user'];
        }
        if (isset($params['password'])) {
            $driverOptions['PWD'] = $params['password'];
        }
        if (!isset($driverOptions['ReturnDatesAsStrings'])) {
            $driverOptions['ReturnDatesAsStrings'] = 1;
        }
        return new \_PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\SQLSrv\Connection($serverName, $driverOptions);
    }
}
