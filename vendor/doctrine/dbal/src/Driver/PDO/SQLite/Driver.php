<?php

namespace _PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\PDO\SQLite;

use _PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\AbstractSQLiteDriver;
use _PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\PDO\Connection;
use _PhpScoper3fe455fa007d\Doctrine\DBAL\Platforms\SqlitePlatform;
use function array_merge;
final class Driver extends \_PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\AbstractSQLiteDriver
{
    /** @var mixed[] */
    protected $_userDefinedFunctions = ['sqrt' => ['callback' => [\_PhpScoper3fe455fa007d\Doctrine\DBAL\Platforms\SqlitePlatform::class, 'udfSqrt'], 'numArgs' => 1], 'mod' => ['callback' => [\_PhpScoper3fe455fa007d\Doctrine\DBAL\Platforms\SqlitePlatform::class, 'udfMod'], 'numArgs' => 2], 'locate' => ['callback' => [\_PhpScoper3fe455fa007d\Doctrine\DBAL\Platforms\SqlitePlatform::class, 'udfLocate'], 'numArgs' => -1]];
    /**
     * {@inheritdoc}
     *
     * @return Connection
     */
    public function connect(array $params)
    {
        $driverOptions = $params['driverOptions'] ?? [];
        if (isset($driverOptions['userDefinedFunctions'])) {
            $this->_userDefinedFunctions = \array_merge($this->_userDefinedFunctions, $driverOptions['userDefinedFunctions']);
            unset($driverOptions['userDefinedFunctions']);
        }
        $connection = new \_PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\PDO\Connection($this->_constructPdoDsn($params), $params['user'] ?? '', $params['password'] ?? '', $driverOptions);
        $pdo = $connection->getWrappedConnection();
        foreach ($this->_userDefinedFunctions as $fn => $data) {
            $pdo->sqliteCreateFunction($fn, $data['callback'], $data['numArgs']);
        }
        return $connection;
    }
    /**
     * Constructs the Sqlite PDO DSN.
     *
     * @param mixed[] $params
     *
     * @return string The DSN.
     */
    protected function _constructPdoDsn(array $params)
    {
        $dsn = 'sqlite:';
        if (isset($params['path'])) {
            $dsn .= $params['path'];
        } elseif (isset($params['memory'])) {
            $dsn .= ':memory:';
        }
        return $dsn;
    }
}
