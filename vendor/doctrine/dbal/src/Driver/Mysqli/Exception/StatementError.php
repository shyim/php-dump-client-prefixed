<?php

declare (strict_types=1);
namespace _PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\Mysqli\Exception;

use _PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\AbstractException;
use mysqli_stmt;
/**
 * @internal
 *
 * @psalm-immutable
 */
final class StatementError extends \_PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\AbstractException
{
    public static function new(\mysqli_stmt $statement) : self
    {
        return new self($statement->error, $statement->sqlstate, $statement->errno);
    }
}
