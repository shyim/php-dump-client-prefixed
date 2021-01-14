<?php

namespace _PhpScoper3fe455fa007d\Doctrine\DBAL\ArrayParameters\Exception;

use _PhpScoper3fe455fa007d\Doctrine\DBAL\ArrayParameters\Exception;
use LogicException;
use function sprintf;
/**
 * @psalm-immutable
 */
class MissingNamedParameter extends \LogicException implements \_PhpScoper3fe455fa007d\Doctrine\DBAL\ArrayParameters\Exception
{
    public static function new(string $name) : self
    {
        return new self(\sprintf('Named parameter "%s" does not have a bound value.', $name));
    }
}
