<?php

namespace _PhpScoper3fe455fa007d\Doctrine\DBAL\Driver;

use _PhpScoper3fe455fa007d\Doctrine\DBAL\ParameterType;
/**
 * Connection interface.
 * Driver connections must implement this interface.
 */
interface Connection
{
    /**
     * Prepares a statement for execution and returns a Statement object.
     *
     * @throws Exception
     */
    public function prepare(string $sql) : \_PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\Statement;
    /**
     * Executes an SQL statement, returning a result set as a Statement object.
     *
     * @throws Exception
     */
    public function query(string $sql) : \_PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\Result;
    /**
     * Quotes a string for use in a query.
     *
     * @param mixed $value
     * @param int   $type
     *
     * @return mixed
     */
    public function quote($value, $type = \_PhpScoper3fe455fa007d\Doctrine\DBAL\ParameterType::STRING);
    /**
     * Executes an SQL statement and return the number of affected rows.
     *
     * @throws Exception
     */
    public function exec(string $sql) : int;
    /**
     * Returns the ID of the last inserted row or sequence value.
     *
     * @param string|null $name
     *
     * @return string
     *
     * @throws Exception
     */
    public function lastInsertId($name = null);
    /**
     * Initiates a transaction.
     *
     * @return bool TRUE on success or FALSE on failure.
     *
     * @throws Exception
     */
    public function beginTransaction();
    /**
     * Commits a transaction.
     *
     * @return bool TRUE on success or FALSE on failure.
     *
     * @throws Exception
     */
    public function commit();
    /**
     * Rolls back the current transaction, as initiated by beginTransaction().
     *
     * @return bool TRUE on success or FALSE on failure.
     *
     * @throws Exception
     */
    public function rollBack();
}
