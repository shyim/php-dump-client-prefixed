<?php

declare (strict_types=1);
namespace _PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\API;

use _PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\Exception;
use _PhpScoper3fe455fa007d\Doctrine\DBAL\Exception\DriverException;
use _PhpScoper3fe455fa007d\Doctrine\DBAL\Query;
interface ExceptionConverter
{
    /**
     * Converts a given driver-level exception into a DBAL-level driver exception.
     *
     * Implementors should use the vendor-specific error code and SQLSTATE of the exception
     * and instantiate the most appropriate specialized {@link DriverException} subclass.
     *
     * @param Exception  $exception The driver exception to convert.
     * @param Query|null $query     The SQL query that triggered the exception, if any.
     *
     * @return DriverException An instance of {@link DriverException} or one of its subclasses.
     */
    public function convert(\_PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\Exception $exception, ?\_PhpScoper3fe455fa007d\Doctrine\DBAL\Query $query) : \_PhpScoper3fe455fa007d\Doctrine\DBAL\Exception\DriverException;
}
