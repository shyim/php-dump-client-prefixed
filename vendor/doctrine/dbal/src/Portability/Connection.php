<?php

namespace _PhpScoper3fe455fa007d\Doctrine\DBAL\Portability;

use _PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\Connection as ConnectionInterface;
use _PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\Result as DriverResult;
use _PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\Statement as DriverStatement;
use _PhpScoper3fe455fa007d\Doctrine\DBAL\ParameterType;
/**
 * Portability wrapper for a Connection.
 */
final class Connection implements \_PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\Connection
{
    public const PORTABILITY_ALL = 255;
    public const PORTABILITY_NONE = 0;
    public const PORTABILITY_RTRIM = 1;
    public const PORTABILITY_EMPTY_TO_NULL = 4;
    public const PORTABILITY_FIX_CASE = 8;
    /** @var ConnectionInterface */
    private $connection;
    /** @var Converter */
    private $converter;
    public function __construct(\_PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\Connection $connection, \_PhpScoper3fe455fa007d\Doctrine\DBAL\Portability\Converter $converter)
    {
        $this->connection = $connection;
        $this->converter = $converter;
    }
    public function prepare(string $sql) : \_PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\Statement
    {
        return new \_PhpScoper3fe455fa007d\Doctrine\DBAL\Portability\Statement($this->connection->prepare($sql), $this->converter);
    }
    public function query(string $sql) : \_PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\Result
    {
        return new \_PhpScoper3fe455fa007d\Doctrine\DBAL\Portability\Result($this->connection->query($sql), $this->converter);
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
        return $this->connection->lastInsertId($name);
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
}
