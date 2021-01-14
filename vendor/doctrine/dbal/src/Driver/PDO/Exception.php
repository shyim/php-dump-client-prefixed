<?php

declare (strict_types=1);
namespace _PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\PDO;

use _PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\AbstractException;
use PDOException;
/**
 * @internal
 *
 * @psalm-immutable
 */
final class Exception extends \_PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\AbstractException
{
    public static function new(\PDOException $exception) : self
    {
        if ($exception->errorInfo !== null) {
            [$sqlState, $code] = $exception->errorInfo;
        } else {
            $code = $exception->getCode();
            $sqlState = null;
        }
        return new self($exception->getMessage(), $sqlState, $code, $exception);
    }
}
