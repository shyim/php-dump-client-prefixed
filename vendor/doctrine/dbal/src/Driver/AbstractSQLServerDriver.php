<?php

namespace _PhpScoper3fe455fa007d\Doctrine\DBAL\Driver;

use _PhpScoper3fe455fa007d\Doctrine\DBAL\Connection;
use _PhpScoper3fe455fa007d\Doctrine\DBAL\Driver;
use _PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\API\ExceptionConverter as ExceptionConverterInterface;
use _PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\API\SQLSrv\ExceptionConverter;
use _PhpScoper3fe455fa007d\Doctrine\DBAL\Platforms\AbstractPlatform;
use _PhpScoper3fe455fa007d\Doctrine\DBAL\Platforms\SQLServer2012Platform;
use _PhpScoper3fe455fa007d\Doctrine\DBAL\Schema\SQLServerSchemaManager;
/**
 * Abstract base implementation of the {@link Driver} interface for Microsoft SQL Server based drivers.
 */
abstract class AbstractSQLServerDriver implements \_PhpScoper3fe455fa007d\Doctrine\DBAL\Driver
{
    /**
     * {@inheritdoc}
     */
    public function getDatabasePlatform()
    {
        return new \_PhpScoper3fe455fa007d\Doctrine\DBAL\Platforms\SQLServer2012Platform();
    }
    /**
     * {@inheritdoc}
     */
    public function getSchemaManager(\_PhpScoper3fe455fa007d\Doctrine\DBAL\Connection $conn, \_PhpScoper3fe455fa007d\Doctrine\DBAL\Platforms\AbstractPlatform $platform)
    {
        return new \_PhpScoper3fe455fa007d\Doctrine\DBAL\Schema\SQLServerSchemaManager($conn, $platform);
    }
    public function getExceptionConverter() : \_PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\API\ExceptionConverter
    {
        return new \_PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\API\SQLSrv\ExceptionConverter();
    }
}
