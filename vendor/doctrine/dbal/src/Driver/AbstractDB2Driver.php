<?php

namespace _PhpScoper3fe455fa007d\Doctrine\DBAL\Driver;

use _PhpScoper3fe455fa007d\Doctrine\DBAL\Connection;
use _PhpScoper3fe455fa007d\Doctrine\DBAL\Driver;
use _PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\API\ExceptionConverter as ExceptionConverterInterface;
use _PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\API\IBMDB2\ExceptionConverter;
use _PhpScoper3fe455fa007d\Doctrine\DBAL\Platforms\AbstractPlatform;
use _PhpScoper3fe455fa007d\Doctrine\DBAL\Platforms\DB2Platform;
use _PhpScoper3fe455fa007d\Doctrine\DBAL\Schema\DB2SchemaManager;
/**
 * Abstract base implementation of the {@link Driver} interface for IBM DB2 based drivers.
 */
abstract class AbstractDB2Driver implements \_PhpScoper3fe455fa007d\Doctrine\DBAL\Driver
{
    /**
     * {@inheritdoc}
     */
    public function getDatabasePlatform()
    {
        return new \_PhpScoper3fe455fa007d\Doctrine\DBAL\Platforms\DB2Platform();
    }
    /**
     * {@inheritdoc}
     */
    public function getSchemaManager(\_PhpScoper3fe455fa007d\Doctrine\DBAL\Connection $conn, \_PhpScoper3fe455fa007d\Doctrine\DBAL\Platforms\AbstractPlatform $platform)
    {
        return new \_PhpScoper3fe455fa007d\Doctrine\DBAL\Schema\DB2SchemaManager($conn, $platform);
    }
    public function getExceptionConverter() : \_PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\API\ExceptionConverter
    {
        return new \_PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\API\IBMDB2\ExceptionConverter();
    }
}
