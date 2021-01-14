<?php

declare (strict_types=1);
namespace _PhpScoper3fe455fa007d\Doctrine\DBAL\Schema\Exception;

use _PhpScoper3fe455fa007d\Doctrine\DBAL\Schema\SchemaException;
use function sprintf;
/**
 * @psalm-immutable
 */
final class UnknownColumnOption extends \_PhpScoper3fe455fa007d\Doctrine\DBAL\Schema\SchemaException
{
    public static function new(string $name) : self
    {
        return new self(\sprintf('The "%s" column option is not supported.', $name));
    }
}
