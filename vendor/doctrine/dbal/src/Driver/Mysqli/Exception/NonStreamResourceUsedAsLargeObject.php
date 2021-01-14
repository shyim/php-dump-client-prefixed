<?php

declare (strict_types=1);
namespace _PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\Mysqli\Exception;

use _PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\AbstractException;
use function sprintf;
/**
 * @internal
 *
 * @psalm-immutable
 */
final class NonStreamResourceUsedAsLargeObject extends \_PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\AbstractException
{
    public static function new(int $parameter) : self
    {
        return new self(\sprintf('The resource passed as a LARGE_OBJECT parameter #%d must be of type "stream"', $parameter));
    }
}
