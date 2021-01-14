<?php

namespace _PhpScoper3fe455fa007d\Doctrine\DBAL\Exception;

use _PhpScoper3fe455fa007d\Doctrine\DBAL\Exception;
/**
 * Exception to be thrown when invalid arguments are passed to any DBAL API
 *
 * @psalm-immutable
 */
class InvalidArgumentException extends \_PhpScoper3fe455fa007d\Doctrine\DBAL\Exception
{
    /**
     * @return self
     */
    public static function fromEmptyCriteria()
    {
        return new self('Empty criteria was used, expected non-empty criteria');
    }
}
