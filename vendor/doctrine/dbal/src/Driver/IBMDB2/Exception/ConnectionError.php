<?php

declare (strict_types=1);
namespace _PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\IBMDB2\Exception;

use _PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\AbstractException;
use function db2_conn_error;
use function db2_conn_errormsg;
/**
 * @internal
 *
 * @psalm-immutable
 */
final class ConnectionError extends \_PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\AbstractException
{
    /**
     * @param resource $connection
     */
    public static function new($connection) : self
    {
        return new self(\db2_conn_errormsg($connection), \db2_conn_error($connection));
    }
}
