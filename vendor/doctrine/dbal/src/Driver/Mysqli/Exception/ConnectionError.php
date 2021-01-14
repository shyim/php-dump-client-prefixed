<?php

declare (strict_types=1);
namespace _PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\Mysqli\Exception;

use _PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\AbstractException;
use mysqli;
/**
 * @internal
 *
 * @psalm-immutable
 */
final class ConnectionError extends \_PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\AbstractException
{
    public static function new(\mysqli $connection) : self
    {
        return new self($connection->error, $connection->sqlstate, $connection->errno);
    }
}
