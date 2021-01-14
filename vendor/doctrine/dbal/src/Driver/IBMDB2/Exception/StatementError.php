<?php

declare (strict_types=1);
namespace _PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\IBMDB2\Exception;

use _PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\AbstractException;
use function db2_stmt_error;
use function db2_stmt_errormsg;
/**
 * @internal
 *
 * @psalm-immutable
 */
final class StatementError extends \_PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\AbstractException
{
    /**
     * @param resource $statement
     */
    public static function new($statement) : self
    {
        return new self(\db2_stmt_errormsg($statement), \db2_stmt_error($statement));
    }
}
