<?php

namespace _PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\IBMDB2;

use _PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\AbstractDB2Driver;
final class Driver extends \_PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\AbstractDB2Driver
{
    /**
     * {@inheritdoc}
     *
     * @return Connection
     */
    public function connect(array $params)
    {
        return new \_PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\IBMDB2\Connection(\_PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\IBMDB2\DataSourceName::fromConnectionParameters($params)->toString(), isset($params['persistent']) && $params['persistent'] === \true, $params['user'] ?? '', $params['password'] ?? '', $params['driverOptions'] ?? []);
    }
}
