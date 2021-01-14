<?php

namespace _PhpScoper3fe455fa007d\Doctrine\DBAL\Tools\Console\ConnectionProvider;

use _PhpScoper3fe455fa007d\Doctrine\DBAL\Connection;
use _PhpScoper3fe455fa007d\Doctrine\DBAL\Tools\Console\ConnectionNotFound;
use _PhpScoper3fe455fa007d\Doctrine\DBAL\Tools\Console\ConnectionProvider;
use function sprintf;
class SingleConnectionProvider implements \_PhpScoper3fe455fa007d\Doctrine\DBAL\Tools\Console\ConnectionProvider
{
    /** @var Connection */
    private $connection;
    /** @var string */
    private $defaultConnectionName;
    public function __construct(\_PhpScoper3fe455fa007d\Doctrine\DBAL\Connection $connection, string $defaultConnectionName = 'default')
    {
        $this->connection = $connection;
        $this->defaultConnectionName = $defaultConnectionName;
    }
    public function getDefaultConnection() : \_PhpScoper3fe455fa007d\Doctrine\DBAL\Connection
    {
        return $this->connection;
    }
    public function getConnection(string $name) : \_PhpScoper3fe455fa007d\Doctrine\DBAL\Connection
    {
        if ($name !== $this->defaultConnectionName) {
            throw new \_PhpScoper3fe455fa007d\Doctrine\DBAL\Tools\Console\ConnectionNotFound(\sprintf('Connection with name "%s" does not exist.', $name));
        }
        return $this->connection;
    }
}
