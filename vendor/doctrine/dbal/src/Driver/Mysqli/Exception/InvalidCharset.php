<?php

declare (strict_types=1);
namespace _PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\Mysqli\Exception;

use _PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\AbstractException;
use mysqli;
use function sprintf;
/**
 * @internal
 *
 * @psalm-immutable
 */
final class InvalidCharset extends \_PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\AbstractException
{
    public static function fromCharset(\mysqli $connection, string $charset) : self
    {
        return new self(\sprintf('Failed to set charset "%s": %s', $charset, $connection->error), $connection->sqlstate, $connection->errno);
    }
}
