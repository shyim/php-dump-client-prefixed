<?php

declare (strict_types=1);
namespace _PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\IBMDB2\Exception;

use _PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\AbstractException;
/**
 * @internal
 *
 * @psalm-immutable
 */
final class PrepareFailed extends \_PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\AbstractException
{
    /**
     * @psalm-param array{message: string}|null $error
     */
    public static function new(?array $error) : self
    {
        if ($error === null) {
            return new self('Unknown error');
        }
        return new self($error['message']);
    }
}
