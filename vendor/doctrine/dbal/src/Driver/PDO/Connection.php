<?php

namespace _PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\PDO;

use _PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\Exception as ExceptionInterface;
use _PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\Result as ResultInterface;
use _PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\ServerInfoAwareConnection;
use _PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\Statement as StatementInterface;
use _PhpScoper3fe455fa007d\Doctrine\DBAL\ParameterType;
use PDO;
use PDOException;
use PDOStatement;
use function assert;
final class Connection implements \_PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\ServerInfoAwareConnection
{
    /** @var PDO */
    private $connection;
    /**
     * @internal The connection can be only instantiated by its driver.
     *
     * @param string       $dsn
     * @param string|null  $user
     * @param string|null  $password
     * @param mixed[]|null $options
     *
     * @throws ExceptionInterface
     */
    public function __construct($dsn, $user = null, $password = null, ?array $options = null)
    {
        try {
            $this->connection = new \PDO($dsn, (string) $user, (string) $password, (array) $options);
            $this->connection->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        } catch (\PDOException $exception) {
            throw \_PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\PDO\Exception::new($exception);
        }
    }
    public function exec(string $sql) : int
    {
        try {
            $result = $this->connection->exec($sql);
            \assert($result !== \false);
            return $result;
        } catch (\PDOException $exception) {
            throw \_PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\PDO\Exception::new($exception);
        }
    }
    /**
     * {@inheritdoc}
     */
    public function getServerVersion()
    {
        return $this->connection->getAttribute(\PDO::ATTR_SERVER_VERSION);
    }
    /**
     * {@inheritDoc}
     *
     * @return Statement
     */
    public function prepare(string $sql) : \_PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\Statement
    {
        try {
            $stmt = $this->connection->prepare($sql);
            \assert($stmt instanceof \PDOStatement);
            return $this->createStatement($stmt);
        } catch (\PDOException $exception) {
            throw \_PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\PDO\Exception::new($exception);
        }
    }
    public function query(string $sql) : \_PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\Result
    {
        try {
            $stmt = $this->connection->query($sql);
            \assert($stmt instanceof \PDOStatement);
            return new \_PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\PDO\Result($stmt);
        } catch (\PDOException $exception) {
            throw \_PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\PDO\Exception::new($exception);
        }
    }
    /**
     * {@inheritdoc}
     */
    public function quote($value, $type = \_PhpScoper3fe455fa007d\Doctrine\DBAL\ParameterType::STRING)
    {
        return $this->connection->quote($value, $type);
    }
    /**
     * {@inheritdoc}
     */
    public function lastInsertId($name = null)
    {
        try {
            if ($name === null) {
                return $this->connection->lastInsertId();
            }
            return $this->connection->lastInsertId($name);
        } catch (\PDOException $exception) {
            throw \_PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\PDO\Exception::new($exception);
        }
    }
    /**
     * Creates a wrapped statement
     */
    protected function createStatement(\PDOStatement $stmt) : \_PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\PDO\Statement
    {
        return new \_PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\PDO\Statement($stmt);
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
    public function getWrappedConnection() : \PDO
    {
        return $this->connection;
    }
}
