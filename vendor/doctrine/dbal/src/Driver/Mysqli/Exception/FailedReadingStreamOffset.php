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
final class FailedReadingStreamOffset extends \_PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\AbstractException
{
    public static function new(int $parameter) : self
    {
        return new self(\sprintf('Failed reading the stream resource for parameter #%d.', $parameter));
    }
}
