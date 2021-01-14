<?php

declare (strict_types=1);
namespace _PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\API\PostgreSQL;

use _PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\API\ExceptionConverter as ExceptionConverterInterface;
use _PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\Exception;
use _PhpScoper3fe455fa007d\Doctrine\DBAL\Exception\ConnectionException;
use _PhpScoper3fe455fa007d\Doctrine\DBAL\Exception\DeadlockException;
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
use function strpos;
/**
 * @internal
 */
final class ExceptionConverter implements \_PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\API\ExceptionConverter
{
    /**
     * @link http://www.postgresql.org/docs/9.4/static/errcodes-appendix.html
     */
    public function convert(\_PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\Exception $exception, ?\_PhpScoper3fe455fa007d\Doctrine\DBAL\Query $query) : \_PhpScoper3fe455fa007d\Doctrine\DBAL\Exception\DriverException
    {
        switch ($exception->getSQLState()) {
            case '40001':
            case '40P01':
                return new \_PhpScoper3fe455fa007d\Doctrine\DBAL\Exception\DeadlockException($exception, $query);
            case '0A000':
                // Foreign key constraint violations during a TRUNCATE operation
                // are considered "feature not supported" in PostgreSQL.
                if (\strpos($exception->getMessage(), 'truncate') !== \false) {
                    return new \_PhpScoper3fe455fa007d\Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException($exception, $query);
                }
                break;
            case '23502':
                return new \_PhpScoper3fe455fa007d\Doctrine\DBAL\Exception\NotNullConstraintViolationException($exception, $query);
            case '23503':
                return new \_PhpScoper3fe455fa007d\Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException($exception, $query);
            case '23505':
                return new \_PhpScoper3fe455fa007d\Doctrine\DBAL\Exception\UniqueConstraintViolationException($exception, $query);
            case '42601':
                return new \_PhpScoper3fe455fa007d\Doctrine\DBAL\Exception\SyntaxErrorException($exception, $query);
            case '42702':
                return new \_PhpScoper3fe455fa007d\Doctrine\DBAL\Exception\NonUniqueFieldNameException($exception, $query);
            case '42703':
                return new \_PhpScoper3fe455fa007d\Doctrine\DBAL\Exception\InvalidFieldNameException($exception, $query);
            case '42P01':
                return new \_PhpScoper3fe455fa007d\Doctrine\DBAL\Exception\TableNotFoundException($exception, $query);
            case '42P07':
                return new \_PhpScoper3fe455fa007d\Doctrine\DBAL\Exception\TableExistsException($exception, $query);
            case '08006':
                return new \_PhpScoper3fe455fa007d\Doctrine\DBAL\Exception\ConnectionException($exception, $query);
        }
        // Prior to fixing https://bugs.php.net/bug.php?id=64705 (PHP 7.3.22 and PHP 7.4.10),
        // in some cases (mainly connection errors) the PDO exception wouldn't provide a SQLSTATE via its code.
        // We have to match against the SQLSTATE in the error message in these cases.
        if ($exception->getCode() === 7 && \strpos($exception->getMessage(), 'SQLSTATE[08006]') !== \false) {
            return new \_PhpScoper3fe455fa007d\Doctrine\DBAL\Exception\ConnectionException($exception, $query);
        }
        return new \_PhpScoper3fe455fa007d\Doctrine\DBAL\Exception\DriverException($exception, $query);
    }
}
