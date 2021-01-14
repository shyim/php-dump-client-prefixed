<?php

declare (strict_types=1);
namespace _PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\API\SQLite;

use _PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\API\ExceptionConverter as ExceptionConverterInterface;
use _PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\Exception;
use _PhpScoper3fe455fa007d\Doctrine\DBAL\Exception\ConnectionException;
use _PhpScoper3fe455fa007d\Doctrine\DBAL\Exception\DriverException;
use _PhpScoper3fe455fa007d\Doctrine\DBAL\Exception\InvalidFieldNameException;
use _PhpScoper3fe455fa007d\Doctrine\DBAL\Exception\LockWaitTimeoutException;
use _PhpScoper3fe455fa007d\Doctrine\DBAL\Exception\NonUniqueFieldNameException;
use _PhpScoper3fe455fa007d\Doctrine\DBAL\Exception\NotNullConstraintViolationException;
use _PhpScoper3fe455fa007d\Doctrine\DBAL\Exception\ReadOnlyException;
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
     * @link http://www.sqlite.org/c3ref/c_abort.html
     */
    public function convert(\_PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\Exception $exception, ?\_PhpScoper3fe455fa007d\Doctrine\DBAL\Query $query) : \_PhpScoper3fe455fa007d\Doctrine\DBAL\Exception\DriverException
    {
        if (\strpos($exception->getMessage(), 'database is locked') !== \false) {
            return new \_PhpScoper3fe455fa007d\Doctrine\DBAL\Exception\LockWaitTimeoutException($exception, $query);
        }
        if (\strpos($exception->getMessage(), 'must be unique') !== \false || \strpos($exception->getMessage(), 'is not unique') !== \false || \strpos($exception->getMessage(), 'are not unique') !== \false || \strpos($exception->getMessage(), 'UNIQUE constraint failed') !== \false) {
            return new \_PhpScoper3fe455fa007d\Doctrine\DBAL\Exception\UniqueConstraintViolationException($exception, $query);
        }
        if (\strpos($exception->getMessage(), 'may not be NULL') !== \false || \strpos($exception->getMessage(), 'NOT NULL constraint failed') !== \false) {
            return new \_PhpScoper3fe455fa007d\Doctrine\DBAL\Exception\NotNullConstraintViolationException($exception, $query);
        }
        if (\strpos($exception->getMessage(), 'no such table:') !== \false) {
            return new \_PhpScoper3fe455fa007d\Doctrine\DBAL\Exception\TableNotFoundException($exception, $query);
        }
        if (\strpos($exception->getMessage(), 'already exists') !== \false) {
            return new \_PhpScoper3fe455fa007d\Doctrine\DBAL\Exception\TableExistsException($exception, $query);
        }
        if (\strpos($exception->getMessage(), 'has no column named') !== \false) {
            return new \_PhpScoper3fe455fa007d\Doctrine\DBAL\Exception\InvalidFieldNameException($exception, $query);
        }
        if (\strpos($exception->getMessage(), 'ambiguous column name') !== \false) {
            return new \_PhpScoper3fe455fa007d\Doctrine\DBAL\Exception\NonUniqueFieldNameException($exception, $query);
        }
        if (\strpos($exception->getMessage(), 'syntax error') !== \false) {
            return new \_PhpScoper3fe455fa007d\Doctrine\DBAL\Exception\SyntaxErrorException($exception, $query);
        }
        if (\strpos($exception->getMessage(), 'attempt to write a readonly database') !== \false) {
            return new \_PhpScoper3fe455fa007d\Doctrine\DBAL\Exception\ReadOnlyException($exception, $query);
        }
        if (\strpos($exception->getMessage(), 'unable to open database file') !== \false) {
            return new \_PhpScoper3fe455fa007d\Doctrine\DBAL\Exception\ConnectionException($exception, $query);
        }
        return new \_PhpScoper3fe455fa007d\Doctrine\DBAL\Exception\DriverException($exception, $query);
    }
}
