<?php

namespace _PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\PDO\SQLSrv;

use _PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\PDO\Connection as PDOConnection;
use _PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\Result;
use _PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\ServerInfoAwareConnection;
use _PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\Statement as StatementInterface;
use _PhpScoper3fe455fa007d\Doctrine\DBAL\ParameterType;
use PDO;
final class Connection implements \_PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\ServerInfoAwareConnection
{
    /** @var PDOConnection */
    private $connection;
    public function __construct(\_PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\PDO\Connection $connection)
    {
        $this->connection = $connection;
    }
    public function prepare(string $sql) : \_PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\Statement
    {
        return new \_PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\PDO\SQLSrv\Statement($this->connection->prepare($sql));
    }
    public function query(string $sql) : \_PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\Result
    {
        return $this->connection->query($sql);
    }
    /**
     * {@inheritDoc}
     */
    public function quote($value, $type = \_PhpScoper3fe455fa007d\Doctrine\DBAL\ParameterType::STRING)
    {
        return $this->connection->quote($value, $type);
    }
    public function exec(string $sql) : int
    {
        return $this->connection->exec($sql);
    }
    /**
     * {@inheritDoc}
     */
    public function lastInsertId($name = null)
    {
        if ($name === null) {
            return $this->connection->lastInsertId($name);
        }
        return $this->prepare('SELECT CONVERT(VARCHAR(MAX), current_value) FROM sys.sequences WHERE name = ?')->execute([$name])->fetchOne();
    }
    /**
     * {@inheritDoc}
     */
    public function beginTransaction()
    {
        return $this->connection->beginTransaction();
    }
    /**
     * {@inheritDoc}
     */
    public function commit()
    {
        return $this->connection->commit();
    }
    /**
     * {@inheritDoc}
     */
    public function rollBack()
    {
        return $this->connection->rollBack();
    }
    /**
     * {@inheritDoc}
     */
    public function getServerVersion()
    {
        return $this->connection->getServerVersion();
    }
    public function getWrappedConnection() : \PDO
    {
        return $this->connection->getWrappedConnection();
    }
}
