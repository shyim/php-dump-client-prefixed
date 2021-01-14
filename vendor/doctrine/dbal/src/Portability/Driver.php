<?php

namespace _PhpScoper3fe455fa007d\Doctrine\DBAL\Portability;

use _PhpScoper3fe455fa007d\Doctrine\DBAL\ColumnCase;
use _PhpScoper3fe455fa007d\Doctrine\DBAL\Connection as DBALConnection;
use _PhpScoper3fe455fa007d\Doctrine\DBAL\Driver as DriverInterface;
use _PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\API\ExceptionConverter;
use _PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\PDO;
use _PhpScoper3fe455fa007d\Doctrine\DBAL\Platforms\AbstractPlatform;
use const CASE_LOWER;
use const CASE_UPPER;
final class Driver implements \_PhpScoper3fe455fa007d\Doctrine\DBAL\Driver
{
    /** @var DriverInterface */
    private $driver;
    /** @var int */
    private $mode;
    /** @var int */
    private $case;
    public function __construct(\_PhpScoper3fe455fa007d\Doctrine\DBAL\Driver $driver, int $mode, int $case)
    {
        $this->driver = $driver;
        $this->mode = $mode;
        $this->case = $case;
    }
    /**
     * {@inheritDoc}
     */
    public function connect(array $params)
    {
        $connection = $this->driver->connect($params);
        $portability = (new \_PhpScoper3fe455fa007d\Doctrine\DBAL\Portability\OptimizeFlags())($this->getDatabasePlatform(), $this->mode);
        $case = 0;
        if ($this->case !== 0 && ($portability & \_PhpScoper3fe455fa007d\Doctrine\DBAL\Portability\Connection::PORTABILITY_FIX_CASE) !== 0) {
            if ($connection instanceof \_PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\PDO\Connection) {
                // make use of c-level support for case handling
                $portability &= ~\_PhpScoper3fe455fa007d\Doctrine\DBAL\Portability\Connection::PORTABILITY_FIX_CASE;
                $connection->getWrappedConnection()->setAttribute(\PDO::ATTR_CASE, $this->case);
            } else {
                $case = $this->case === \_PhpScoper3fe455fa007d\Doctrine\DBAL\ColumnCase::LOWER ? \CASE_LOWER : \CASE_UPPER;
            }
        }
        $convertEmptyStringToNull = ($portability & \_PhpScoper3fe455fa007d\Doctrine\DBAL\Portability\Connection::PORTABILITY_EMPTY_TO_NULL) !== 0;
        $rightTrimString = ($portability & \_PhpScoper3fe455fa007d\Doctrine\DBAL\Portability\Connection::PORTABILITY_RTRIM) !== 0;
        if (!$convertEmptyStringToNull && !$rightTrimString && $case === 0) {
            return $connection;
        }
        return new \_PhpScoper3fe455fa007d\Doctrine\DBAL\Portability\Connection($connection, new \_PhpScoper3fe455fa007d\Doctrine\DBAL\Portability\Converter($convertEmptyStringToNull, $rightTrimString, $case));
    }
    /**
     * {@inheritDoc}
     */
    public function getDatabasePlatform()
    {
        return $this->driver->getDatabasePlatform();
    }
    /**
     * {@inheritDoc}
     */
    public function getSchemaManager(\_PhpScoper3fe455fa007d\Doctrine\DBAL\Connection $conn, \_PhpScoper3fe455fa007d\Doctrine\DBAL\Platforms\AbstractPlatform $platform)
    {
        return $this->driver->getSchemaManager($conn, $platform);
    }
    public function getExceptionConverter() : \_PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\API\ExceptionConverter
    {
        return $this->driver->getExceptionConverter();
    }
}
