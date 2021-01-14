<?php

declare (strict_types=1);
namespace _PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\IBMDB2;

use _PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\FetchUtils;
use _PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\IBMDB2\Exception\StatementError;
use _PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\Result as ResultInterface;
use function db2_fetch_array;
use function db2_fetch_assoc;
use function db2_free_result;
use function db2_num_fields;
use function db2_num_rows;
use function db2_stmt_error;
final class Result implements \_PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\Result
{
    /** @var resource */
    private $statement;
    /**
     * @internal The result can be only instantiated by its driver connection or statement.
     *
     * @param resource $statement
     */
    public function __construct($statement)
    {
        $this->statement = $statement;
    }
    /**
     * {@inheritDoc}
     */
    public function fetchNumeric()
    {
        $row = @\db2_fetch_array($this->statement);
        if ($row === \false && \db2_stmt_error($this->statement) !== '02000') {
            throw \_PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\IBMDB2\Exception\StatementError::new($this->statement);
        }
        return $row;
    }
    /**
     * {@inheritDoc}
     */
    public function fetchAssociative()
    {
        $row = @\db2_fetch_assoc($this->statement);
        if ($row === \false && \db2_stmt_error($this->statement) !== '02000') {
            throw \_PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\IBMDB2\Exception\StatementError::new($this->statement);
        }
        return $row;
    }
    /**
     * {@inheritDoc}
     */
    public function fetchOne()
    {
        return \_PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\FetchUtils::fetchOne($this);
    }
    /**
     * {@inheritDoc}
     */
    public function fetchAllNumeric() : array
    {
        return \_PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\FetchUtils::fetchAllNumeric($this);
    }
    /**
     * {@inheritDoc}
     */
    public function fetchAllAssociative() : array
    {
        return \_PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\FetchUtils::fetchAllAssociative($this);
    }
    /**
     * {@inheritDoc}
     */
    public function fetchFirstColumn() : array
    {
        return \_PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\FetchUtils::fetchFirstColumn($this);
    }
    public function rowCount() : int
    {
        return @\db2_num_rows($this->statement);
    }
    public function columnCount() : int
    {
        $count = \db2_num_fields($this->statement);
        if ($count !== \false) {
            return $count;
        }
        return 0;
    }
    public function free() : void
    {
        \db2_free_result($this->statement);
    }
}
