<?php

declare (strict_types=1);
namespace _PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\OCI8\Exception;

use _PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\AbstractException;
use function assert;
use function oci_error;
/**
 * @internal
 *
 * @psalm-immutable
 */
final class Error extends \_PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\AbstractException
{
    /**
     * @param resource $resource
     */
    public static function new($resource) : self
    {
        $error = \oci_error($resource);
        \assert($error !== \false);
        return new self($error['message'], null, $error['code']);
    }
}
