<?php

declare (strict_types=1);
namespace _PhpScoper3fe455fa007d\Doctrine\DBAL\Schema\Exception;

use _PhpScoper3fe455fa007d\Doctrine\DBAL\Schema\SchemaException;
use function sprintf;
/**
 * @psalm-immutable
 */
final class InvalidTableName extends \_PhpScoper3fe455fa007d\Doctrine\DBAL\Schema\SchemaException
{
    public static function new(string $tableName) : self
    {
        return new self(\sprintf('Invalid table name specified "%s".', $tableName));
    }
}
