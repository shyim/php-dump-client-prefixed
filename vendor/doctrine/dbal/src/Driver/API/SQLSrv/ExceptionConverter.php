<?php

declare (strict_types=1);
namespace _PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\API\SQLSrv;

use _PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\API\ExceptionConverter as ExceptionConverterInterface;
use _PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\Exception;
use _PhpScoper3fe455fa007d\Doctrine\DBAL\Exception\DriverException;
use _PhpScoper3fe455fa007d\Doctrine\DBAL\Query;
/**
 * @internal
 */
final class ExceptionConverter implements \_PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\API\ExceptionConverter
{
    public function convert(\_PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\Exception $exception, ?\_PhpScoper3fe455fa007d\Doctrine\DBAL\Query $query) : \_PhpScoper3fe455fa007d\Doctrine\DBAL\Exception\DriverException
    {
        return new \_PhpScoper3fe455fa007d\Doctrine\DBAL\Exception\DriverException($exception, $query);
    }
}
