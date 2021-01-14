<?php

declare (strict_types=1);
namespace _PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\API\MySQL;

use _PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\API\ExceptionConverter as ExceptionConverterInterface;
use _PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\Exception;
use _PhpScoper3fe455fa007d\Doctrine\DBAL\Exception\ConnectionException;
use _PhpScoper3fe455fa007d\Doctrine\DBAL\Exception\ConnectionLost;
use _PhpScoper3fe455fa007d\Doctrine\DBAL\Exception\DeadlockException;
use _PhpScoper3fe455fa007d\Doctrine\DBAL\Exception\DriverException;
use _PhpScoper3fe455fa007d\Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use _PhpScoper3fe455fa007d\Doctrine\DBAL\Exception\InvalidFieldNameException;
use _PhpScoper3fe455fa007d\Doctrine\DBAL\Exception\LockWaitTimeoutException;
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
     * @link https://dev.mysql.com/doc/refman/8.0/en/client-error-reference.html
     * @link https://dev.mysql.com/doc/refman/8.0/en/server-error-reference.html
     */
    public function convert(\_PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\Exception $exception, ?\_PhpScoper3fe455fa007d\Doctrine\DBAL\Query $query) : \_PhpScoper3fe455fa007d\Doctrine\DBAL\Exception\DriverException
    {
        switch ($exception->getCode()) {
            case 1213:
                return new \_PhpScoper3fe455fa007d\Doctrine\DBAL\Exception\DeadlockException($exception, $query);
            case 1205:
                return new \_PhpScoper3fe455fa007d\Doctrine\DBAL\Exception\LockWaitTimeoutException($exception, $query);
            case 1050:
                return new \_PhpScoper3fe455fa007d\Doctrine\DBAL\Exception\TableExistsException($exception, $query);
            case 1051:
            case 1146:
                return new \_PhpScoper3fe455fa007d\Doctrine\DBAL\Exception\TableNotFoundException($exception, $query);
            case 1216:
            case 1217:
            case 1451:
            case 1452:
            case 1701:
                return new \_PhpScoper3fe455fa007d\Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException($exception, $query);
            case 1062:
            case 1557:
            case 1569:
            case 1586:
                return new \_PhpScoper3fe455fa007d\Doctrine\DBAL\Exception\UniqueConstraintViolationException($exception, $query);
            case 1054:
            case 1166:
            case 1611:
                return new \_PhpScoper3fe455fa007d\Doctrine\DBAL\Exception\InvalidFieldNameException($exception, $query);
            case 1052:
            case 1060:
            case 1110:
                return new \_PhpScoper3fe455fa007d\Doctrine\DBAL\Exception\NonUniqueFieldNameException($exception, $query);
            case 1064:
            case 1149:
            case 1287:
            case 1341:
            case 1342:
            case 1343:
            case 1344:
            case 1382:
            case 1479:
            case 1541:
            case 1554:
            case 1626:
                return new \_PhpScoper3fe455fa007d\Doctrine\DBAL\Exception\SyntaxErrorException($exception, $query);
            case 1044:
            case 1045:
            case 1046:
            case 1049:
            case 1095:
            case 1142:
            case 1143:
            case 1227:
            case 1370:
            case 1429:
            case 2002:
            case 2005:
                return new \_PhpScoper3fe455fa007d\Doctrine\DBAL\Exception\ConnectionException($exception, $query);
            case 2006:
                return new \_PhpScoper3fe455fa007d\Doctrine\DBAL\Exception\ConnectionLost($exception, $query);
            case 1048:
            case 1121:
            case 1138:
            case 1171:
            case 1252:
            case 1263:
            case 1364:
            case 1566:
                return new \_PhpScoper3fe455fa007d\Doctrine\DBAL\Exception\NotNullConstraintViolationException($exception, $query);
        }
        return new \_PhpScoper3fe455fa007d\Doctrine\DBAL\Exception\DriverException($exception, $query);
    }
}
