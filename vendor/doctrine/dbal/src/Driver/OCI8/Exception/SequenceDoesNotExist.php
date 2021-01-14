<?php

declare (strict_types=1);
namespace _PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\OCI8\Exception;

use _PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\AbstractException;
/**
 * @internal
 *
 * @psalm-immutable
 */
final class SequenceDoesNotExist extends \_PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\AbstractException
{
    public static function new() : self
    {
        return new self('lastInsertId failed: Query was executed but no result was returned.');
    }
}
