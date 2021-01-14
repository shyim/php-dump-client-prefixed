<?php

namespace _PhpScoper3fe455fa007d\Doctrine\DBAL;

use _PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\API\ExceptionConverter;
use _PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\Connection as DriverConnection;
use _PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\Exception;
use _PhpScoper3fe455fa007d\Doctrine\DBAL\Platforms\AbstractPlatform;
use _PhpScoper3fe455fa007d\Doctrine\DBAL\Schema\AbstractSchemaManager;
/**
 * Driver interface.
 * Interface that all DBAL drivers must implement.
 */
interface Driver
{
    /**
     * Attempts to create a connection with the database.
     *
     * @param mixed[] $params All connection parameters.
     *
     * @return DriverConnection The database connection.
     *
     * @throws Exception
     */
    public function connect(array $params);
    /**
     * Gets the DatabasePlatform instance that provides all the metadata about
     * the platform this driver connects to.
     *
     * @return AbstractPlatform The database platform.
     */
    public function getDatabasePlatform();
    /**
     * Gets the SchemaManager that can be used to inspect and change the underlying
     * database schema of the platform this driver connects to.
     *
     * @return AbstractSchemaManager
     */
    public function getSchemaManager(\_PhpScoper3fe455fa007d\Doctrine\DBAL\Connection $conn, \_PhpScoper3fe455fa007d\Doctrine\DBAL\Platforms\AbstractPlatform $platform);
    /**
     * Gets the ExceptionConverter that can be used to convert driver-level exceptions into DBAL exceptions.
     */
    public function getExceptionConverter() : \_PhpScoper3fe455fa007d\Doctrine\DBAL\Driver\API\ExceptionConverter;
}
