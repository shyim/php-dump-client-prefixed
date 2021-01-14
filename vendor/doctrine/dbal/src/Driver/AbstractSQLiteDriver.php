<?php

namespace _PhpScoper3fe455fa007d\Doctrine\DBAL\Driver;

use _PhpScoper3fe455fa007d\Doctrine\DBAL\Connection;
use _PhpScoper3fe455fa007d\Doctrine\DBAL\Driver;
use _PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\API\ExceptionConverter;
use _PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\API\SQLite;
use _PhpScoper3fe455fa007d\Doctrine\DBAL\Platforms\AbstractPlatform;
use _PhpScoper3fe455fa007d\Doctrine\DBAL\Platforms\SqlitePlatform;
use _PhpScoper3fe455fa007d\Doctrine\DBAL\Schema\SqliteSchemaManager;
/**
 * Abstract base implementation of the {@link Doctrine\DBAL\Driver} interface for SQLite based drivers.
 */
abstract class AbstractSQLiteDriver implements \_PhpScoper3fe455fa007d\Doctrine\DBAL\Driver
{
    /**
     * {@inheritdoc}
     */
    public function getDatabasePlatform()
    {
        return new \_PhpScoper3fe455fa007d\Doctrine\DBAL\Platforms\SqlitePlatform();
    }
    /**
     * {@inheritdoc}
     */
    public function getSchemaManager(\_PhpScoper3fe455fa007d\Doctrine\DBAL\Connection $conn, \_PhpScoper3fe455fa007d\Doctrine\DBAL\Platforms\AbstractPlatform $platform)
    {
        return new \_PhpScoper3fe455fa007d\Doctrine\DBAL\Schema\SqliteSchemaManager($conn, $platform);
    }
    public function getExceptionConverter() : \_PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\API\ExceptionConverter
    {
        return new \_PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\API\SQLite\ExceptionConverter();
    }
}
