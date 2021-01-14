<?php

namespace _PhpScoper3fe455fa007d\Doctrine\DBAL\Exception;

use _PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\Exception as TheDriverException;
use _PhpScoper3fe455fa007d\Doctrine\DBAL\Exception;
use _PhpScoper3fe455fa007d\Doctrine\DBAL\Query;
use function assert;
/**
 * Base class for all errors detected in the driver.
 *
 * @psalm-immutable
 */
class DriverException extends \_PhpScoper3fe455fa007d\Doctrine\DBAL\Exception implements \_PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\Exception
{
    /**
     * The query that triggered the exception, if any.
     *
     * @var Query|null
     */
    private $query;
    /**
     * @internal
     *
     * @param TheDriverException $driverException The DBAL driver exception to chain.
     * @param Query|null         $query           The SQL query that triggered the exception, if any.
     */
    public function __construct(\_PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\Exception $driverException, ?\_PhpScoper3fe455fa007d\Doctrine\DBAL\Query $query)
    {
        if ($query !== null) {
            $message = 'An exception occurred while executing a query: ' . $driverException->getMessage();
        } else {
            $message = 'An exception occurred in the driver: ' . $driverException->getMessage();
        }
        parent::__construct($message, $driverException->getCode(), $driverException);
        $this->query = $query;
    }
    /**
     * {@inheritDoc}
     */
    public function getSQLState()
    {
        $previous = $this->getPrevious();
        \assert($previous instanceof \_PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\Exception);
        return $previous->getSQLState();
    }
    public function getQuery() : ?\_PhpScoper3fe455fa007d\Doctrine\DBAL\Query
    {
        return $this->query;
    }
}
