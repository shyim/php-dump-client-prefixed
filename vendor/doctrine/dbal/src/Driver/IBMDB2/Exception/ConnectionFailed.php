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
final class ConnectionFailed extends \_PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\AbstractException
{
    public static function new() : self
    {
        return new self(\db2_conn_errormsg(), \db2_conn_error());
    }
}
