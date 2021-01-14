<?php

namespace _PhpScoper3fe455fa007d\Doctrine\DBAL\ArrayParameters\Exception;

use _PhpScoper3fe455fa007d\Doctrine\DBAL\ArrayParameters\Exception;
use LogicException;
use function sprintf;
/**
 * @internal
 *
 * @psalm-immutable
 */
class MissingPositionalParameter extends \LogicException implements \_PhpScoper3fe455fa007d\Doctrine\DBAL\ArrayParameters\Exception
{
    public static function new(int $index) : self
    {
        return new self(\sprintf('Positional parameter at index %d does not have a bound value.', $index));
    }
}
