<?php

declare (strict_types=1);
namespace _PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\API\OCI;

use _PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\API\ExceptionConverter as ExceptionConverterInterface;
use _PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\Exception;
use _PhpScoper3fe455fa007d\Doctrine\DBAL\Exception\ConnectionException;
use _PhpScoper3fe455fa007d\Doctrine\DBAL\Exception\DriverException;
use _PhpScoper3fe455fa007d\Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use _PhpScoper3fe455fa007d\Doctrine\DBAL\Exception\InvalidFieldNameException;
use _PhpScoper3fe455fa007d\Doctrine\DBAL\Exception\NonUniqueFieldNameException;
use _PhpScoper3fe455fa007d\Doctrine\DBAL\Exception\NotNullConstraintViolationException;
use _PhpScoper3fe455fa007d\Doctrine\DBAL\Exception\SyntaxErrorException;
use _PhpScoper3fe455fa007d\Doctrine\DBAL\Exception\TableExistsException;
use _PhpScoper3fe455fa007d\Doctrine\DBAL\Exception\TableNotFoundException;
use _PhpScoper3fe455fa007d\Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use _PhpScoper3fe455fa007d\Doctrine\DBAL\Query;
/**
 * @internal
 */
final class ExceptionConverter implements \_PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\API\ExceptionConverter
{
    /**
     * @link http://www.dba-oracle.com/t_error_code_list.htm
     */
    public function convert(\_PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\Exception $exception, ?\_PhpScoper3fe455fa007d\Doctrine\DBAL\Query $query) : \_PhpScoper3fe455fa007d\Doctrine\DBAL\Exception\DriverException
    {
        switch ($exception->getCode()) {
            case 1:
            case 2299:
            case 38911:
                return new \_PhpScoper3fe455fa007d\Doctrine\DBAL\Exception\UniqueConstraintViolationException($exception, $query);
            case 904:
                return new \_PhpScoper3fe455fa007d\Doctrine\DBAL\Exception\InvalidFieldNameException($exception, $query);
            case 918:
            case 960:
                return new \_PhpScoper3fe455fa007d\Doctrine\DBAL\Exception\NonUniqueFieldNameException($exception, $query);
            case 923:
                return new \_PhpScoper3fe455fa007d\Doctrine\DBAL\Exception\SyntaxErrorException($exception, $query);
            case 942:
                return new \_PhpScoper3fe455fa007d\Doctrine\DBAL\Exception\TableNotFoundException($exception, $query);
            case 955:
                return new \_PhpScoper3fe455fa007d\Doctrine\DBAL\Exception\TableExistsException($exception, $query);
            case 1017:
            case 12545:
                return new \_PhpScoper3fe455fa007d\Doctrine\DBAL\Exception\ConnectionException($exception, $query);
            case 1400:
                return new \_PhpScoper3fe455fa007d\Doctrine\DBAL\Exception\NotNullConstraintViolationException($exception, $query);
            case 2266:
            case 2291:
            case 2292:
                return new \_PhpScoper3fe455fa007d\Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException($exception, $query);
        }
        return new \_PhpScoper3fe455fa007d\Doctrine\DBAL\Exception\DriverException($exception, $query);
    }
}
