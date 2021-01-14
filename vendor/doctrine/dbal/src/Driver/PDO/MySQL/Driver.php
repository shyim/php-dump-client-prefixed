<?php

namespace _PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\PDO\MySQL;

use _PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\AbstractMySQLDriver;
use _PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\PDO\Connection;
use PDO;
final class Driver extends \_PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\AbstractMySQLDriver
{
    /**
     * {@inheritdoc}
     *
     * @return Connection
     */
    public function connect(array $params)
    {
        $driverOptions = $params['driverOptions'] ?? [];
        if (!empty($params['persistent'])) {
            $driverOptions[\PDO::ATTR_PERSISTENT] = \true;
        }
        return new \_PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\PDO\Connection($this->constructPdoDsn($params), $params['user'] ?? '', $params['password'] ?? '', $driverOptions);
    }
    /**
     * Constructs the MySQL PDO DSN.
     *
     * @param mixed[] $params
     *
     * @return string The DSN.
     */
    protected function constructPdoDsn(array $params)
    {
        $dsn = 'mysql:';
        if (isset($params['host']) && $params['host'] !== '') {
            $dsn .= 'host=' . $params['host'] . ';';
        }
        if (isset($params['port'])) {
            $dsn .= 'port=' . $params['port'] . ';';
        }
        if (isset($params['dbname'])) {
            $dsn .= 'dbname=' . $params['dbname'] . ';';
        }
        if (isset($params['unix_socket'])) {
            $dsn .= 'unix_socket=' . $params['unix_socket'] . ';';
        }
        if (isset($params['charset'])) {
            $dsn .= 'charset=' . $params['charset'] . ';';
        }
        return $dsn;
    }
}
